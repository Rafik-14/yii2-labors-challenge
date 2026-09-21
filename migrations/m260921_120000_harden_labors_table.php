<?php

use yii\db\Migration;

/**
 * Hardens the `{{%labors}}` schema:
 * - `working_minutes` becomes UNSIGNED (negative durations are rejected by the database too);
 * - the low-selectivity `need_work` index is replaced by a composite (need_work, working_date)
 *   index that serves the API query `WHERE need_work = 1 ORDER BY working_date` without a filesort.
 */
class m260921_120000_harden_labors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if ($this->db->driverName === 'mysql') {
            $this->alterColumn('{{%labors}}', 'working_minutes', $this->integer()->unsigned()->null());
        } elseif ($this->db->driverName === 'pgsql') {
            // UNSIGNED is MySQL-only; PostgreSQL gets an equivalent CHECK constraint.
            $this->execute('ALTER TABLE {{%labors}} ADD CONSTRAINT [[chk-labors-working_minutes]] CHECK ([[working_minutes]] >= 0)');
        }

        $this->dropIndex('idx-labors-need_work', '{{%labors}}');
        $this->createIndex('idx-labors-need_work-working_date', '{{%labors}}', ['need_work', 'working_date']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('idx-labors-need_work-working_date', '{{%labors}}');
        $this->createIndex('idx-labors-need_work', '{{%labors}}', 'need_work');

        if ($this->db->driverName === 'mysql') {
            $this->alterColumn('{{%labors}}', 'working_minutes', $this->integer()->null());
        } elseif ($this->db->driverName === 'pgsql') {
            $this->execute('ALTER TABLE {{%labors}} DROP CONSTRAINT [[chk-labors-working_minutes]]');
        }
    }
}
