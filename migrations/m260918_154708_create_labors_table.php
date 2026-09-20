<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%labors}}`.
 */
class m260918_154708_create_labors_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%labors}}', [
            'id' => $this->primaryKey(),
            'first_name' => $this->string(255)->notNull(),
            'last_name' => $this->string(255)->notNull(),
            'email' => $this->string(255)->null(),
            'ip_address' => $this->string(45)->null(),
            'need_work' => $this->boolean()->notNull()->defaultValue(false),
            'working_minutes' => $this->integer()->null(),
            'working_date' => $this->dateTime()->null(),
        ]);

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
