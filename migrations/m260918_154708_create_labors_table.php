<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%labors}}`.
 *
 * Column/index hardening (unsigned minutes, composite index) lives in
 * m260921_120000_harden_labors_table so it also reaches databases created before it.
 */
class m260918_154708_create_labors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // explicit charset/engine: Hungarian accents need utf8mb4, transactions need InnoDB
            $tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%labors}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->null(),
            'ip_address' => $this->string(45)->null(),
            'need_work' => $this->boolean()->notNull()->defaultValue(false),
            'working_minutes' => $this->integer()->null(),
            'working_date' => $this->dateTime()->null(),
        ], $tableOptions);

        $this->createIndex('idx-labors-need_work', '{{%labors}}', 'need_work');
        $this->createIndex('idx-labors-working_date', '{{%labors}}', 'working_date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%labors}}');
    }
}
