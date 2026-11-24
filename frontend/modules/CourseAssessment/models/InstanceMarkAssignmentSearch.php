<?php

namespace frontend\modules\CourseAssessment\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\CourseAssessment\models\InstanceMarkAssignment;

/**
 * InstanceMarkAssignmentSearch represents the model behind the search form of `frontend\modules\CourseAssessment\models\InstanceMarkAssignment`.
 */
class InstanceMarkAssignmentSearch extends InstanceMarkAssignment
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ima_catagorySetup_relId', 'ima_created_by', 'ima_updated_by', 'ima_approved', 'ima_approved_by', 'ima_assessed'], 'integer'],
            [['ima_possiblePoint'], 'number'],
            [['ima_instanceName', 'ima_created_at', 'ima_updated_at', 'ima_approved_at'], 'safe'],
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
        $query = InstanceMarkAssignment::find();

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
            'ima_catagorySetup_relId' => $this->ima_catagorySetup_relId,
            'ima_possiblePoint' => $this->ima_possiblePoint,
            'ima_created_by' => $this->ima_created_by,
            'ima_created_at' => $this->ima_created_at,
            'ima_updated_by' => $this->ima_updated_by,
            'ima_updated_at' => $this->ima_updated_at,
            'ima_approved' => $this->ima_approved,
            'ima_approved_at' => $this->ima_approved_at,
            'ima_approved_by' => $this->ima_approved_by,
            'ima_assessed' => $this->ima_assessed,
        ]);

        $query->andFilterWhere(['like', 'ima_instanceName', $this->ima_instanceName]);

        return $dataProvider;
    }
}
