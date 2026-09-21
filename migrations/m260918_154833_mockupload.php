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
                !empty($item['need_work']),
                isset($item['working_minutes']) ? (int) $item['working_minutes'] : null,
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

        // Explicit ids do not advance the id sequence on PostgreSQL/SQLite; move it past the seeded rows.
        $this->db->createCommand()->resetSequence('{{%labors}}')->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // DELETE (not TRUNCATE): TRUNCATE causes an implicit commit in MySQL and cannot be rolled back.
        $this->delete('{{%labors}}');
    }
}
