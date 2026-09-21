<?php

namespace app\tests\Unit;

use Codeception\Test\Unit;
use app\models\Labors;
use app\models\LaborsSearch;
use Yii;

class LaborsTest extends Unit
{
    /**
     * @var \app\tests\UnitTester
     */
    protected $tester;

    private $transaction;

    protected function _before()
    {
        // every test leaves the test database exactly as it found it
        $this->transaction = Yii::$app->db->beginTransaction();
    }

    protected function _after()
    {
        $this->transaction->rollBack();
    }

    public function testRequiredFields()
    {
        $labor = new Labors();

        $this->assertFalse($labor->validate(['first_name', 'last_name']));
        $this->assertArrayHasKey('first_name', $labor->getErrors());
        $this->assertArrayHasKey('last_name', $labor->getErrors());
    }

    public function testWhitespaceOnlyNamesAreRejected()
    {
        $labor = $this->validLabor(['first_name' => '   ', 'last_name' => "\t"]);

        $this->assertFalse($labor->validate());
        $this->assertArrayHasKey('first_name', $labor->getErrors());
        $this->assertArrayHasKey('last_name', $labor->getErrors());
    }

    public function testValuesAreTrimmed()
    {
        $labor = $this->validLabor(['first_name' => '  John ', 'email' => ' john@example.com ']);

        $this->assertTrue($labor->validate());
        $this->assertSame('John', $labor->first_name);
        $this->assertSame('john@example.com', $labor->email);
    }

    /**
     * @dataProvider invalidValues
     */
    public function testInvalidValuesAreRejected(string $attribute, $value)
    {
        $labor = $this->validLabor([$attribute => $value]);

        $this->assertFalse($labor->validate(), "$attribute = " . var_export($value, true) . ' should be invalid');
        $this->assertArrayHasKey($attribute, $labor->getErrors());
    }

    public static function invalidValues(): array
    {
        return [
            'email' => ['email', 'not-an-email'],
            'ip' => ['ip_address', '999.999.999.999'],
            'negative minutes' => ['working_minutes', -250],
            'more than a day' => ['working_minutes', 99999999],
            'need_work not boolean' => ['need_work', 5],
            'garbage date' => ['working_date', 'yesterday-ish'],
            'overflowing date' => ['working_date', '31-Feb-2021'],
        ];
    }

    public function testValidRecordPasses()
    {
        $labor = $this->validLabor(['ip_address' => '2001:db8::1']);

        $this->assertTrue($labor->validate(), json_encode($labor->getErrors()));
    }

    public function testPickerDateIsStoredInDatabaseFormat()
    {
        $labor = $this->validLabor(['working_date' => '23-Feb-1982']);

        $this->assertTrue($labor->save(), json_encode($labor->getErrors()));
        $labor->refresh();

        $this->assertSame('1982-02-23 00:00:00', $labor->working_date);
        $this->assertSame('23-Feb-1982', $labor->getWorkingDateInput());
        $this->assertSame('23-Feb-1982 00:00:00', $labor->getWorkingDateDisplay());
    }

    public function testEditingTheDateKeepsTheStoredTimeOfDay()
    {
        $labor = $this->validLabor(['working_date' => '2021-05-19 13:23:05']);
        $this->assertTrue($labor->save());

        $labor = Labors::findOne($labor->id);
        $this->assertSame('19-May-2021', $labor->getWorkingDateInput());

        $labor->working_date = '20-May-2021';
        $this->assertTrue($labor->save(), json_encode($labor->getErrors()));
        $labor->refresh();

        $this->assertSame('2021-05-20 13:23:05', $labor->working_date);
    }

    public function testFullName()
    {
        $labor = $this->validLabor(['first_name' => 'John ', 'last_name' => ' Doe']);

        $this->assertSame('John Doe', $labor->fullName);
    }

    public function testLabelsAreTranslated()
    {
        $this->assertSame('hu-HU', Yii::$app->language);
        $labor = new Labors();

        $this->assertSame('Keresztnév', $labor->getAttributeLabel('first_name'));
        $this->assertSame('IP cím', $labor->getAttributeLabel('ip_address'));
        $this->assertSame('Munkavégzés dátuma', $labor->getAttributeLabel('working_date'));
    }

    public function testSeedDataIsPresent()
    {
        $this->assertSame(1000, (int) Labors::find()->count());

        $record = Labors::findOne(1);
        $this->assertNotNull($record);
        $this->assertSame('Basile Seedhouse', $record->fullName);
    }

    public function testSearchFiltersAndPaginates()
    {
        $search = new LaborsSearch();

        $all = $search->search([]);
        $this->assertSame(1000, $all->getTotalCount());
        $this->assertCount(LaborsSearch::PAGE_SIZE, $all->getModels());
        $this->assertSame(1, $all->getModels()[0]->id, 'default order is by id');

        $byName = (new LaborsSearch())->search(['LaborsSearch' => ['first_name' => 'Basile']]);
        $this->assertContains('Basile Seedhouse', array_map(fn ($m) => $m->fullName, $byName->getModels()));

        $byPickerDate = (new LaborsSearch())->search(['LaborsSearch' => ['working_date' => '19-May-2021']]);
        $byDbDate = (new LaborsSearch())->search(['LaborsSearch' => ['working_date' => '2021-05-19']]);
        $this->assertGreaterThan(0, $byPickerDate->getTotalCount());
        $this->assertSame($byDbDate->getTotalCount(), $byPickerDate->getTotalCount());

        $wildcard = (new LaborsSearch())->search(['LaborsSearch' => ['working_date' => '%']]);
        $this->assertSame(0, $wildcard->getTotalCount(), '% must be matched literally');
    }

    private function validLabor(array $overrides = []): Labors
    {
        return new Labors(array_merge([
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'email' => 'test@example.com',
            'ip_address' => '192.168.1.1',
            'need_work' => 1,
            'working_minutes' => 120,
            'working_date' => '2026-09-18 10:00:00',
        ], $overrides));
    }
}
