<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ProjectGroupMembers;

/**
 * ProjectGroupMembersSearch represents the model behind the search form about `backend\models\ProjectGroupMembers`.
 */
class ProjectGroupMembersSearch extends ProjectGroupMembers
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'pgm_pgSetup_relId', 'pgm_std_relId', 'pgm_enroll_type', 'pgm_grade_relId', 'pgm_gradeApproved', 'pgm_gradeApproved_by', 'pgm_craeted_by', 'pgm_updated_by'], 'integer'],
            [['pgm_gradeApproved_at', 'pgm_advisor_comment', 'pgm_created_at', 'pgm_updated_at'], 'safe'],
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
        $query = ProjectGroupMembers::find();

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
            'pgm_pgSetup_relId' => $this->pgm_pgSetup_relId,
            'pgm_std_relId' => $this->pgm_std_relId,
            'pgm_enroll_type' => $this->pgm_enroll_type,
            'pgm_grade_relId' => $this->pgm_grade_relId,
            'pgm_gradeApproved' => $this->pgm_gradeApproved,
            'pgm_gradeApproved_by' => $this->pgm_gradeApproved_by,
            'pgm_gradeApproved_at' => $this->pgm_gradeApproved_at,
            'pgm_created_at' => $this->pgm_created_at,
            'pgm_craeted_by' => $this->pgm_craeted_by,
            'pgm_updated_by' => $this->pgm_updated_by,
            'pgm_updated_at' => $this->pgm_updated_at,
        ]);

        $query->andFilterWhere(['like', 'pgm_advisor_comment', $this->pgm_advisor_comment]);

        return $dataProvider;
    }
}
