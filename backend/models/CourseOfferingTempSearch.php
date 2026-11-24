<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\CourseOfferingTemp;

/**
 * CourseOfferingTempSearch represents the model behind the search form of `backend\models\CourseOfferingTemp`.
 */
class CourseOfferingTempSearch extends CourseOfferingTemp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'cot_acadYear_relId', 'cot_sem_relId', 'cot_adm_relId', 'cot_prog_relId', 'cot_dept_relId', 'cot_clslvel_relId', 'cot_spec_relId', 'cot_created_by', 'cot_updated_by', 'cot_locked', 'cot_registrarApproved', 'cot_registrarApproved_by', 'cot_acProg_relId'], 'integer'],
            [['cot_cocat_IDS', 'cot_created_at', 'cot_updated_at', 'cot_registrarApproved_at'], 'safe'],
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
        $query = CourseOfferingTemp::find();

        // add conditions that should always apply here

        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = Instructor::find()->where(['inst_user_relId' => \Yii::$app->user->id])->one();
            if ($inst) {
                $query->andFilterWhere(['cot_dept_relId' => $inst->inst_dep_relId]);
            } else {
                $query->andFilterWhere(['cot_dept_relId' => null]);
            }
        }
        $query->orderBy([
            'cot_acadYear_relId' => SORT_DESC, 
            'cot_sem_relId' => SORT_DESC,
            'cot_clslvel_relId' => SORT_DESC,
        ]);

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
            'cot_acadYear_relId' => $this->cot_acadYear_relId,
            'cot_sem_relId' => $this->cot_sem_relId,
            'cot_adm_relId' => $this->cot_adm_relId,
            'cot_prog_relId' => $this->cot_prog_relId,
            'cot_dept_relId' => $this->cot_dept_relId,
            'cot_clslvel_relId' => $this->cot_clslvel_relId,
            'cot_spec_relId' => $this->cot_spec_relId,
            'cot_created_at' => $this->cot_created_at,
            'cot_created_by' => $this->cot_created_by,
            'cot_updated_at' => $this->cot_updated_at,
            'cot_updated_by' => $this->cot_updated_by,
            'cot_locked' => $this->cot_locked,
            'cot_registrarApproved' => $this->cot_registrarApproved,
            'cot_registrarApproved_at' => $this->cot_registrarApproved_at,
            'cot_registrarApproved_by' => $this->cot_registrarApproved_by,
            'cot_acProg_relId' => $this->cot_acProg_relId,
        ]);

        $query->andFilterWhere(['like', 'cot_cocat_IDS', $this->cot_cocat_IDS]);

        return $dataProvider;
    }
}
