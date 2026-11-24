<?php

namespace frontend\modules\ClassSchedule\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\ClassSchedule\models\CsClassScheduleDetail;

/**
 * CsClassScheduleDetailSearch represents the model behind the search form of `frontend\modules\ClassSchedule\models\CsClassScheduleDetail`.
 */
class CsClassScheduleDetailSearch extends CsClassScheduleDetail
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'gset_relId', 'class_time_distrib_relId', 'coff2det_relId', 'room_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'safe'],
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
        $query = CsClassScheduleDetail::find();

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
            'gset_relId' => $this->gset_relId,
            'class_time_distrib_relId' => $this->class_time_distrib_relId,
            'coff2det_relId' => $this->coff2det_relId,
            'room_relId' => $this->room_relId,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'updated_at' => $this->updated_at,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
