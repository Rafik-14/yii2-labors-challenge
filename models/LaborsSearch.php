<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * LaborsSearch represents the model behind the search form of `app\models\Labors`.
 */
class LaborsSearch extends Labors
{
    public const PAGE_SIZE = 20;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'working_minutes'], 'integer'],
            [['need_work'], 'boolean'],
            [['first_name', 'last_name', 'email', 'ip_address', 'working_date'], 'trim'],
            [['first_name', 'last_name', 'email', 'ip_address', 'working_date'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied.
     *
     * `working_date` accepts either a (partial) database date such as `2021-05` / `2021-05-19`
     * or the display format `19-May-2021`.
     */
    public function search(array $params): ActiveDataProvider
    {
        $query = Labors::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => self::PAGE_SIZE,
            ],
            'sort' => [
                'defaultOrder' => [
                    'id' => SORT_ASC,
                ],
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'need_work' => $this->need_work === null || $this->need_work === '' ? null : (int) $this->need_work,
            'working_minutes' => $this->working_minutes,
        ]);

        $query->andFilterWhere(['like', 'first_name', $this->first_name])
            ->andFilterWhere(['like', 'last_name', $this->last_name])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'ip_address', $this->ip_address]);

        if ($this->working_date !== null && $this->working_date !== '') {
            $date = \DateTimeImmutable::createFromFormat('!' . self::DISPLAY_DATE_FORMAT, $this->working_date);
            $prefix = $date !== false ? $date->format('Y-m-d') : $this->working_date;
            // "starts with" match; user input is escaped so % and _ are not treated as wildcards
            $query->andWhere(['like', 'working_date', addcslashes($prefix, '%_\\') . '%', false]);
        }

        return $dataProvider;
    }
}
