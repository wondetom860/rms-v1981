<?php

namespace frontend\modules\Depal\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\Depal\models\DepalSetting;

/**
 * DepalSettingSearch represents the model behind the search form of `frontend\modules\Depal\models\DepalSetting`.
 */
class DepalSettingSearch extends DepalSetting
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'section_relId', 'depts_count', 'start_date', 'end_date', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['category_IDS', 'elligible_depts', 'data'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = DepalSetting::find()
            ->innerJoin('ac_active_programs','ac_active_programs.id=section_relId');

        $query->orderBy(['ac_active_programs.academic_year_relId' => SORT_DESC]);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'section_relId' => $this->section_relId,
            'depts_count' => $this->depts_count,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->status,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_by' => $this->updated_by,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'category_IDS', $this->category_IDS])
            ->andFilterWhere(['like', 'elligible_depts', $this->elligible_depts])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
