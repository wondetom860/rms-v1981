<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\StudentGroupHistory;

/**
 * StudentGroupHistorySearch represents the model behind the search form about `backend\models\StudentGroupHistory`.
 */
class StudentGroupHistorySearch extends StudentGroupHistory
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'sgh_stud_relId', 'sgh_group_relId', 'sgh_acadYear_relId', 'sgh_classlevel', 'sgh_created_by', 'sgh_updated_by'], 'integer'],
            [['sgh_semester_relId', 'sgh_created_at', 'sgh_updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = StudentGroupHistory::find();

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
            'sgh_stud_relId' => $this->sgh_stud_relId,
            'sgh_group_relId' => $this->sgh_group_relId,
            'sgh_acadYear_relId' => $this->sgh_acadYear_relId,
            'sgh_classlevel' => $this->sgh_classlevel,
            'sgh_created_by' => $this->sgh_created_by,
            'sgh_created_at' => $this->sgh_created_at,
            'sgh_updated_by' => $this->sgh_updated_by,
            'sgh_updated_at' => $this->sgh_updated_at,
        ]);

        $query->andFilterWhere(['like', 'sgh_semester_relId', $this->sgh_semester_relId]);

        return $dataProvider;
    }
}
