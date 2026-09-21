<?php

namespace app\models;

use DateTimeImmutable;
use Yii;

/**
 * This is the model class for table "labors".
 *
 * The table name is plural and the class keeps the plural `Labors` name on purpose:
 * the challenge specification requires the generated model to be called `Labors`.
 *
 * `working_date` is always stored in the attribute in database format (`Y-m-d H:i:s`).
 * The form sends the English display format (`d-M-Y`, e.g. `23-Feb-1982`); the
 * `normalizeWorkingDate` validator converts it before saving.
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string|null $email
 * @property string|null $ip_address
 * @property int $need_work
 * @property int|null $working_minutes
 * @property string|null $working_date
 *
 * @property-read string $fullName
 */
class Labors extends \yii\db\ActiveRecord
{
    /** Date format used by the DatePicker and the UI (English, e.g. 23-Feb-1982). */
    public const DISPLAY_DATE_FORMAT = 'd-M-Y';
    /** Date-time format used in list/detail views. */
    public const DISPLAY_DATETIME_FORMAT = 'd-M-Y H:i:s';
    /** Storage format of the DATETIME column. */
    public const DB_DATETIME_FORMAT = 'Y-m-d H:i:s';
    /** A working day cannot be longer than 24 hours. */
    public const MAX_WORKING_MINUTES = 1440;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%labors}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'email', 'ip_address', 'working_date'], 'trim'],
            [['email', 'ip_address', 'working_minutes', 'working_date'], 'default', 'value' => null],
            [['need_work'], 'default', 'value' => 0],
            [['first_name', 'last_name'], 'required'],
            [['first_name', 'last_name', 'email'], 'string', 'max' => 255],
            [['email'], 'email'],
            [['ip_address'], 'string', 'max' => 45],
            [['ip_address'], 'ip'],
            [['need_work'], 'boolean'],
            [['working_minutes'], 'integer', 'min' => 0, 'max' => self::MAX_WORKING_MINUTES],
            [['working_date'], 'normalizeWorkingDate'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'first_name' => Yii::t('app', 'First Name'),
            'last_name' => Yii::t('app', 'Last Name'),
            'email' => Yii::t('app', 'Email'),
            'ip_address' => Yii::t('app', 'IP Address'),
            'need_work' => Yii::t('app', 'Need Work'),
            'working_minutes' => Yii::t('app', 'Working Minutes'),
            'working_date' => Yii::t('app', 'Working Date'),
        ];
    }

    /**
     * Validator: accepts `d-M-Y` (form input) or `Y-m-d[ H:i:s]` (API, fixtures, DB)
     * and rewrites the attribute into database format.
     *
     * When only a date is submitted for an existing record, the stored time of day is kept,
     * so editing a shift through the date-only picker does not reset its start time.
     */
    public function normalizeWorkingDate(string $attribute): void
    {
        $value = (string) $this->$attribute;
        if ($value === '') {
            return;
        }

        $dateTime = self::parseDateTime($value, self::DB_DATETIME_FORMAT);
        if ($dateTime === null) {
            $dateTime = self::parseDateTime($value, self::DISPLAY_DATE_FORMAT)
                ?? self::parseDateTime($value, 'Y-m-d');
            if ($dateTime !== null) {
                $previous = self::parseDateTime((string) $this->getOldAttribute($attribute), self::DB_DATETIME_FORMAT);
                if ($previous !== null) {
                    $dateTime = $dateTime->setTime(
                        (int) $previous->format('H'),
                        (int) $previous->format('i'),
                        (int) $previous->format('s')
                    );
                }
            }
        }

        if ($dateTime === null) {
            $this->addError($attribute, Yii::t('app', '{attribute} must be a valid date, e.g. {example}.', [
                'attribute' => $this->getAttributeLabel($attribute),
                'example' => '23-Feb-1982',
            ]));

            return;
        }

        $this->$attribute = $dateTime->format(self::DB_DATETIME_FORMAT);
    }

    /**
     * Full name of the worker ("First Last"), with surrounding and repeated whitespace removed.
     */
    public function getFullName(): string
    {
        return self::buildFullName($this->first_name, $this->last_name);
    }

    public static function buildFullName(?string $firstName, ?string $lastName): string
    {
        return trim((string) preg_replace('/\s+/u', ' ', trim((string) $firstName) . ' ' . trim((string) $lastName)));
    }

    /**
     * `working_date` in the DatePicker format (e.g. 23-Feb-1982), or null.
     */
    public function getWorkingDateInput(): ?string
    {
        return $this->formatWorkingDate(self::DISPLAY_DATE_FORMAT);
    }

    /**
     * `working_date` for list/detail views (e.g. 23-Feb-1982 13:23:05), or null.
     *
     * Formatted with PHP instead of Yii's Formatter on purpose: the Formatter would use the
     * hu-HU locale (Hungarian month names) and convert the stored value from UTC to the
     * server timezone, while the specification requires the English format and the column
     * holds local wall-clock times.
     */
    public function getWorkingDateDisplay(): ?string
    {
        return $this->formatWorkingDate(self::DISPLAY_DATETIME_FORMAT);
    }

    private function formatWorkingDate(string $format): ?string
    {
        $value = (string) $this->working_date;
        if ($value === '') {
            return null;
        }
        $dateTime = self::parseDateTime($value, self::DB_DATETIME_FORMAT)
            ?? self::parseDateTime($value, self::DISPLAY_DATE_FORMAT);

        return $dateTime !== null ? $dateTime->format($format) : $value;
    }

    /**
     * Strict parse: returns null for malformed input and for overflowing dates such as 31-Feb-2021.
     */
    private static function parseDateTime(string $value, string $format): ?DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat('!' . $format, $value);
        $errors = DateTimeImmutable::getLastErrors();
        if ($dateTime === false || ($errors !== false && ($errors['warning_count'] > 0 || $errors['error_count'] > 0))) {
            return null;
        }

        return $dateTime;
    }
}
