<?php

namespace app\tests\Unit;

use app\controllers\ApiController;
use app\models\WorksReport;
use Codeception\Test\Unit;
use Yii;
use yii\db\Connection;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UnauthorizedHttpException;

class ApiTest extends Unit
{
    /**
     * @var \app\tests\UnitTester
     */
    protected $tester;

    private ?string $originalMethod = null;

    protected function _before()
    {
        $this->originalMethod = $_SERVER['REQUEST_METHOD'] ?? null;
    }

    protected function _after()
    {
        $_SERVER['REQUEST_METHOD'] = $this->originalMethod;
        Yii::$app->params['apiToken'] = '';
        Yii::$app->request->headers->remove('Authorization');
    }

    // ---------------------------------------------------------------- aggregation rules

    public function testSameDayShiftsOfOneWorkerAreSummed()
    {
        $result = WorksReport::aggregate([
            $this->row('John', 'Doe', '2021-05-19 08:00:00', 240),
            $this->row('John', 'Doe', '2021-05-19 14:00:00', 180),
            $this->row('Jane', 'Roe', '2021-05-19 09:00:00', 60),
        ]);

        $this->assertSame([
            '2021-05-19' => [
                'John Doe' => ['name' => 'John Doe', 'working_minutes' => 420],
                'Jane Roe' => ['name' => 'Jane Roe', 'working_minutes' => 60],
            ],
        ], $result);
    }

    public function testShiftsOnDifferentDaysAreNotMerged()
    {
        $result = WorksReport::aggregate([
            $this->row('John', 'Doe', '2021-05-19 23:30:00', 30),
            $this->row('John', 'Doe', '2021-05-20 00:15:00', 45),
        ]);

        // grouping uses the stored calendar day, so shifts near midnight never drift to another day
        $this->assertSame(30, $result['2021-05-19']['John Doe']['working_minutes']);
        $this->assertSame(45, $result['2021-05-20']['John Doe']['working_minutes']);
    }

    public function testRowsThatDidNotNeedWorkAreExcluded()
    {
        $result = WorksReport::aggregate([
            $this->row('John', 'Doe', '2021-05-19 08:00:00', 240, false),
            $this->row('Jane', 'Roe', '2021-05-19 08:00:00', 100, 0),
        ]);

        $this->assertSame([], $result);
    }

    public function testNullAndMissingMinutesCountAsZero()
    {
        $withoutMinutes = $this->row('Jane', 'Roe', '2021-05-19 10:00:00', null);
        unset($withoutMinutes['working_minutes']);

        $result = WorksReport::aggregate([
            $this->row('John', 'Doe', '2021-05-19 08:00:00', null),
            $withoutMinutes,
            $this->row('John', 'Doe', '2021-05-19 12:00:00', 90),
        ]);

        $this->assertSame(90, $result['2021-05-19']['John Doe']['working_minutes']);
        $this->assertSame(0, $result['2021-05-19']['Jane Roe']['working_minutes']);
    }

    public function testWhitespaceVariantsOfANameAreTheSameWorker()
    {
        $result = WorksReport::aggregate([
            $this->row('John ', ' Doe', '2021-05-19 08:00:00', 100),
            $this->row('John', 'Doe', '2021-05-19 12:00:00', 50),
        ]);

        $this->assertSame(['John Doe'], array_keys($result['2021-05-19']));
        $this->assertSame(150, $result['2021-05-19']['John Doe']['working_minutes']);
    }

    public function testInvalidDatesAndNamesAreSkippedNotGroupedUnder1970()
    {
        $result = WorksReport::aggregate([
            $this->row('John', 'Doe', 'not-a-date', 100),
            $this->row('John', 'Doe', '2021-02-31 08:00:00', 100),
            $this->row('John', 'Doe', null, 100),
            $this->row('  ', '', '2021-05-19 08:00:00', 100),
        ]);

        $this->assertSame([], $result);
    }

    public function testFallbackRowsAreSortedChronologically()
    {
        $sorted = WorksReport::sortChronologically([
            $this->row('C', 'C', '2021-05-19 13:23:05', 1),
            $this->row('A', 'A', '2020-10-17 08:00:00', 1),
            $this->row('B', 'B', null, 1),
        ]);

        $this->assertSame(['2020-10-17 08:00:00', '2021-05-19 13:23:05', null], array_column($sorted, 'working_date'));
    }

    // ---------------------------------------------------------------- endpoint

    public function testEndpointMatchesAnIndependentComputationOverTheSeedData()
    {
        $result = (new ApiController('api', Yii::$app))->actionWorks();

        $this->assertSame($this->expectedFromSeedFile(), $result);
    }

    public function testEndpointFallsBackToMockDataWhenDatabaseIsDown()
    {
        $db = Yii::$app->get('db');
        Yii::$app->set('db', new Connection(['dsn' => 'mysql:host=127.0.0.1;port=1;dbname=nope']));
        try {
            $result = (new ApiController('api', Yii::$app))->actionWorks();
        } finally {
            Yii::$app->set('db', $db);
        }

        $this->assertSame($this->expectedFromSeedFile(), $result);
        $dates = array_keys($result);
        $sorted = $dates;
        sort($sorted);
        $this->assertSame($sorted, $dates, 'fallback dates must be in chronological order');
    }

    public function testEndpointRejectsNonPostRequests()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->expectException(MethodNotAllowedHttpException::class);
        (new ApiController('api', Yii::$app))->runAction('works');
    }

    public function testEndpointRequiresBearerTokenWhenConfigured()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Yii::$app->params['apiToken'] = 'secret-token';
        Yii::$app->request->headers->set('Authorization', 'Bearer wrong');

        $this->expectException(UnauthorizedHttpException::class);
        (new ApiController('api', Yii::$app))->runAction('works');
    }

    public function testEndpointAcceptsTheConfiguredBearerToken()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        Yii::$app->params['apiToken'] = 'secret-token';
        Yii::$app->request->headers->set('Authorization', 'Bearer secret-token');

        $result = (new ApiController('api', Yii::$app))->runAction('works');

        $this->assertNotEmpty($result);
    }

    // ---------------------------------------------------------------- helpers

    private function row(string $first, string $last, ?string $date, ?int $minutes, $needWork = true): array
    {
        return [
            'first_name' => $first,
            'last_name' => $last,
            'need_work' => $needWork,
            'working_minutes' => $minutes,
            'working_date' => $date,
        ];
    }

    /**
     * Straightforward re-implementation of the specification over data/mock-data.json,
     * used as the oracle for the endpoint tests (the test database is seeded from the same file).
     */
    private function expectedFromSeedFile(): array
    {
        $rows = json_decode(file_get_contents(Yii::getAlias('@app/data/mock-data.json')), true);
        $rows = array_values(array_filter($rows, fn ($r) => !empty($r['need_work'])));
        usort($rows, fn ($a, $b) => [$a['working_date'], $a['id']] <=> [$b['working_date'], $b['id']]);

        $expected = [];
        foreach ($rows as $r) {
            $day = substr($r['working_date'], 0, 10);
            $name = $r['first_name'] . ' ' . $r['last_name'];
            $expected[$day][$name]['name'] = $name;
            $expected[$day][$name]['working_minutes'] = ($expected[$day][$name]['working_minutes'] ?? 0) + (int) $r['working_minutes'];
        }

        return $expected;
    }
}
