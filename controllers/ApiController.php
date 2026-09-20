<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;
use app\models\Labors;

class ApiController extends Controller
{
    /**
     * Disable CSRF validation for API POST requests.
     */
    public $enableCsrfValidation = false;

    /**
     * Endpoint: POST /api/works
     * Returns all workers with need_work = true, grouped by working_date (YYYY-MM-DD),
     * aggregating working_minutes for identical workers on the same day.
     *
     * @return array
     */
    public function actionWorks()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Fetch records via ActiveRecord Labors::find() with fallback
        try {
            $records = Labors::find()
                ->where(['need_work' => 1])
                ->orderBy(['working_date' => SORT_ASC])
                ->asArray()
                ->all();
        } catch (\Exception $e) {
            $dataFile = Yii::getAlias('@app/data/mock-data.php');
            $dbreturn = file_exists($dataFile) ? require $dataFile : [];
            $records = array_filter($dbreturn, function ($item) {
                return !empty($item['need_work']);
            });
        }

        $result = [];

        foreach ($records as $row) {
            $workingDate = $row['working_date'] ?? null;
            if (!$workingDate) {
                continue;
            }

            // Group key: calendar date YYYY-MM-DD
            $dateKey = date('Y-m-d', strtotime($workingDate));
            $fullName = trim(($row['first_name'] ?? '') . ' ' . ($row['last_name'] ?? ''));

            if (empty($fullName)) {
                continue;
            }

            $minutes = (isset($row['working_minutes']) && $row['working_minutes'] !== null) ? (int)$row['working_minutes'] : 0;

            if (!isset($result[$dateKey])) {
                $result[$dateKey] = [];
            }

            if (!isset($result[$dateKey][$fullName])) {
                $result[$dateKey][$fullName] = [
                    'name' => $fullName,
                    'working_minutes' => $minutes,
                ];
            } else {
                $result[$dateKey][$fullName]['working_minutes'] += $minutes;
            }
        }

        return $result;
    }
}
