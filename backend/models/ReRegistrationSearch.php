<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ReRegistration;

/**
 * ReRegistrationSearch represents the model behind the search form of `backend\models\ReRegistration`.
 */
class ReRegistrationSearch extends ReRegistration
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'std_relId', 'prev_gas_relId', 'new_gas_relId', 'course_relId', 'new_coff2det_relId', 'trail_count', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'dept_approved', 'dept_approved_at', 'dept_approved_by', 'admin_processed', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['prev_semreg', 'prev_srd2', 'data','std_regId'], 'safe'],
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
        $query = ReRegistration::find()
            ->innerJoinWith('stdRel')
            ->innerJoinWith('prevGasRel');

        // add conditions that should always apply here
        if(\Yii::$app->user->can('DepartmentHead')){
            $inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            if($inst){
                $query->andFilterWhere(['w_group_setup.gs_dept_relId' => $inst->inst_dep_relId]);
            }
        }else if (\Yii::$app->user->can('Admin') || \Yii::$app->user->can('Registrar')){

        }else{
            $query->andFilterWhere(['1=0']);
        }

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
            'std_relId' => $this->std_relId,
            'prev_gas_relId' => $this->prev_gas_relId,
            'new_gas_relId' => $this->new_gas_relId,
            'course_relId' => $this->course_relId,
            'new_coff2det_relId' => $this->new_coff2det_relId,
            'trail_count' => $this->trail_count,
            'reg_approved' => $this->reg_approved,
            'reg_approved_at' => $this->reg_approved_at,
            'reg_approved_by' => $this->reg_approved_by,
            'dept_approved' => $this->dept_approved,
            'dept_approved_at' => $this->dept_approved_at,
            'dept_approved_by' => $this->dept_approved_by,
            'admin_processed' => $this->admin_processed,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_by' => $this->deleted_by,
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
        ]);

        $query->andFilterWhere(['like', 'prev_semreg', $this->prev_semreg])
            ->andFilterWhere(['like', 'student.std_regId', $this->std_regId])
            ->andFilterWhere(['like', 'prev_srd2', $this->prev_srd2])
            ->andFilterWhere(['like', 'data', $this->data]);

        return $dataProvider;
    }
}
