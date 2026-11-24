<?php

namespace frontend\modules\TRANSTUD\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\TRANSTUD\models\TransferStudent;

/**
 * TransferStudentSearch represents the model behind the search form of `frontend\modules\TRANSTUD\models\TransferStudent`.
 */
class TransferStudentSearch extends TransferStudent
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'std_relId', 'year_transfered', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by','class_level_joined'], 'integer'],
            [['transfered_from', 'year_admitted', 'exempted_course_ids', 'data'], 'safe'],
            [['sgpa', 'cgpa'], 'number'],
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
        $query = TransferStudent::find();

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
            'class_level_joined' => $this->class_level_joined,
            'std_relId' => $this->std_relId,
            'year_transfered' => $this->year_transfered,
            'sgpa' => $this->sgpa,
            'cgpa' => $this->cgpa,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'transfered_from', $this->transfered_from])
            ->andFilterWhere(['like', 'year_admitted', $this->year_admitted])
            ->andFilterWhere(['like', 'exempted_course_ids', $this->exempted_course_ids])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
