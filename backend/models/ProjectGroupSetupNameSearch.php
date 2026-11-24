<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProjectCourseSetup;

/**
 * ProjectGroupSetupNameSearch represents the model behind the search form about `backend\models\ProjectCourseSetup`.
 */
class ProjectGroupSetupNameSearch extends ProjectCourseSetup
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pcs_acadYear_relId', 'pcs_sem_relId', 'pcs_gset_relId', 'pcs_course_relId', 'pcs_pgName_relId', 'pcs_advisor', 'pcs_group_leader', 'pcs_approved', 'pcs_approved_by', 'pcs_created_by', 'pcs_updated_by'], 'integer'],
            [['pcs_title', 'pcs_approved_at', 'pcs_created_at', 'pcs_updated_at'], 'safe'],
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
        $query = ProjectCourseSetup::find();

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
            'pcs_acadYear_relId' => $this->pcs_acadYear_relId,
            'pcs_sem_relId' => $this->pcs_sem_relId,
            'pcs_gset_relId' => $this->pcs_gset_relId,
            'pcs_course_relId' => $this->pcs_course_relId,
            'pcs_pgName_relId' => $this->pcs_pgName_relId,
            'pcs_advisor' => $this->pcs_advisor,
            'pcs_group_leader' => $this->pcs_group_leader,
            'pcs_approved' => $this->pcs_approved,
            'pcs_approved_at' => $this->pcs_approved_at,
            'pcs_approved_by' => $this->pcs_approved_by,
            'pcs_created_at' => $this->pcs_created_at,
            'pcs_created_by' => $this->pcs_created_by,
            'pcs_updated_at' => $this->pcs_updated_at,
            'pcs_updated_by' => $this->pcs_updated_by,
        ]);

        $query->andFilterWhere(['like', 'pcs_title', $this->pcs_title]);

        return $dataProvider;
    }
}
