<?php

use yii\db\Migration;

class m260918_154833_mockupload extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $dataFile = Yii::getAlias('@app/data/mock-data.json');
        if (!file_exists($dataFile)) {
            throw new \Exception("Mock data file not found at: {$dataFile}");
        }

        $jsonContent = file_get_contents($dataFile);
        $records = json_decode($jsonContent, true);

        if (!is_array($records)) {
            throw new \Exception("Failed to parse mock data JSON.");
        }

        $columns = ['id', 'first_name', 'last_name', 'email', 'ip_address', 'need_work', 'working_minutes', 'working_date'];
        $batch = [];
        $chunkSize = 250;

        foreach ($records as $item) {
            $batch[] = [
                $item['id'] ?? null,
                $item['first_name'] ?? '',
                $item['last_name'] ?? '',
                $item['email'] ?? null,
                $item['ip_address'] ?? null,
                !empty($item['need_work']) ? 1 : 0,
                $item['working_minutes'] !== null ? (int)$item['working_minutes'] : null,
                $item['working_date'] ?? null,
            ];

            if (count($batch) >= $chunkSize) {
                $this->batchInsert('{{%labors}}', $columns, $batch);
                $batch = [];
            }
        }

        if (!empty($batch)) {
            $this->batchInsert('{{%labors}}', $columns, $batch);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->truncateTable('{{%labors}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m260918_154833_mockupload cannot be reverted.\n";

        return false;
    }
    */
}
