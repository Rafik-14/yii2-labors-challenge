<?php

declare(strict_types=1);

namespace app\controllers;

use app\models\Labors;
use app\models\WorksReport;
use Yii;
use yii\filters\Cors;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UnauthorizedHttpException;

class ApiController extends Controller
{
    /** Rows fetched per round-trip, so the whole table is never hydrated at once. */
    private const BATCH_SIZE = 500;

    /**
     * Disable CSRF validation for API POST requests.
     */
    public $enableCsrfValidation = false;

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            // must run before the verb filter so browser preflight (OPTIONS) requests succeed
            'corsFilter' => [
                'class' => Cors::class,
                'cors' => [
                    'Origin' => Yii::$app->params['apiCorsOrigins'],
                    'Access-Control-Request-Method' => ['POST', 'OPTIONS'],
                    'Access-Control-Request-Headers' => ['Content-Type', 'Authorization'],
                    'Access-Control-Max-Age' => 3600,
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'works' => ['POST'],
                ],
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // JSON for every response of this controller, including 401/405 errors
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (!parent::beforeAction($action)) {
            return false;
        }
        $this->checkApiToken();

        return true;
    }

    /**
     * Endpoint: POST /api/works
     * Returns all workers with need_work = true, grouped by working_date (YYYY-MM-DD),
     * aggregating working_minutes for identical workers on the same day.
     *
     * @return array<string, array<string, array{name: string, working_minutes: int}>>
     */
    public function actionWorks(): array
    {
        try {
            $rows = Labors::find()
                ->select(['first_name', 'last_name', 'need_work', 'working_minutes', 'working_date'])
                ->where(['need_work' => true])
                ->orderBy(['working_date' => SORT_ASC, 'id' => SORT_ASC])
                ->asArray()
                ->each(self::BATCH_SIZE);

            return WorksReport::aggregate($rows);
        } catch (\Throwable $e) {
            Yii::error('POST /api/works: database unavailable, serving mock data. ' . $e, __METHOD__);

            return WorksReport::aggregate(WorksReport::sortChronologically($this->loadMockRows()));
        }
    }

    /**
     * Fallback rows read from the JSON seed file. Parsed as data, never executed as PHP.
     *
     * @return array<int, array<string, mixed>>
     */
    private function loadMockRows(): array
    {
        $dataFile = Yii::getAlias('@app/data/mock-data.json');
        $rows = is_file($dataFile) ? json_decode((string) file_get_contents($dataFile), true) : null;

        return is_array($rows) ? $rows : [];
    }

    /**
     * Optional shared-secret protection: when the API_TOKEN environment variable is set,
     * requests must send `Authorization: Bearer <token>`. Left unset, the endpoint stays
     * public as the specification describes.
     *
     * @throws UnauthorizedHttpException
     */
    private function checkApiToken(): void
    {
        $expected = (string) (Yii::$app->params['apiToken'] ?? '');
        if ($expected === '') {
            return;
        }

        $header = (string) $this->request->headers->get('Authorization', '');
        if (!preg_match('/^Bearer\s+(\S+)$/i', $header, $m) || !hash_equals($expected, $m[1])) {
            Yii::$app->response->headers->set('WWW-Authenticate', 'Bearer');
            throw new UnauthorizedHttpException('A valid API token is required.');
        }
    }
}
