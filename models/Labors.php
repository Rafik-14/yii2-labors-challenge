<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "labors".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $ip_address
 * @property int $need_work
 * @property int|null $working_minutes
 * @property string|null $working_date
 */
class Labors extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'labors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['email', 'ip_address', 'working_minutes', 'working_date'], 'default', 'value' => null],
            [['need_work'], 'default', 'value' => 0],
            [['first_name', 'last_name'], 'required'],
            [['need_work', 'working_minutes'], 'integer'],
            [['working_date'], 'safe'],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 255],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'email' => 'Email',
            'ip_address' => 'Ip Address',
            'need_work' => 'Need Work',
            'working_minutes' => 'Working Minutes',
            'working_date' => 'Working Date',
        ];
    }

}
