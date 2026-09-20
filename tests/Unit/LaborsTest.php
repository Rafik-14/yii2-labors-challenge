<?php

namespace app\tests\Unit;

use Codeception\Test\Unit;
use app\models\Labors;

class LaborsTest extends Unit
{
    /**
     * @var \app\tests\UnitTester
     */
    protected $tester;

    public function testValidationRules()
    {
        $labor = new Labors();

        // Model should be invalid without required fields
        $this->assertFalse($labor->validate(['first_name', 'last_name']));
        $this->assertArrayHasKey('first_name', $labor->getErrors());
        $this->assertArrayHasKey('last_name', $labor->getErrors());

        // Fill required fields
        $labor->first_name = 'TestFirst';
        $labor->last_name = 'TestLast';
        $labor->email = 'test@example.com';
        $labor->need_work = 1;
        $labor->working_minutes = 120;
        $labor->working_date = '2026-09-18 10:00:00';

        $this->assertTrue($labor->validate());
    }

    public function testDatabaseRecordsExist()
    {
        // Verify mock data seed exists in database
        $count = Labors::find()->count();
        $this->assertGreaterThanOrEqual(1000, $count);

        $record = Labors::findOne(1);
        $this->assertNotNull($record);
        $this->assertEquals('Basile', $record->first_name);
        $this->assertEquals('Seedhouse', $record->last_name);
    }
}
