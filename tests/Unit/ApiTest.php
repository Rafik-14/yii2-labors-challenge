<?php

namespace app\tests\Unit;

use Codeception\Test\Unit;
use Yii;
use app\controllers\ApiController;

class ApiTest extends Unit
{
    /**
     * @var \app\tests\UnitTester
     */
    protected $tester;

    public function testWorksEndpointLogic()
    {
        $controller = new ApiController('api', Yii::$app);
        $result = $controller->actionWorks();

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);

        // Check date key format YYYY-MM-DD
        $firstDateKey = array_key_first($result);
        $this->assertMatchesRegularExpression('/^\d{4}-\d{2}-\d{2}$/', $firstDateKey);

        // Check date structure containing worker entries
        $dayEntries = $result[$firstDateKey];
        $this->assertIsArray($dayEntries);

        $firstWorkerName = array_key_first($dayEntries);
        $workerData = $dayEntries[$firstWorkerName];

        $this->assertArrayHasKey('name', $workerData);
        $this->assertArrayHasKey('working_minutes', $workerData);
        $this->assertIsInt($workerData['working_minutes']);
    }
}
