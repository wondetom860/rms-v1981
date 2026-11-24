<?php

namespace frontend\modules\WithdrawalReadmission\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory;

/**
 * WStudentReadmissionHistorySearch represents the model behind the search form of `frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory`.
 */
class WStudentReadmissionHistorySearch extends WStudentReadmissionHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_HOD_approved', 'srh_HOD_approved_by', 'srh_approved', 'srh_approved_by', 'srh_created_by', 'srh_updated_by'], 'integer'],
            [['srh_return_back_on', 'srh_HOD_approved_at', 'srh_approved_at', 'srh_return_back_letter', 'remark_HOD', 'remark_registrar', 'srh_created_at', 'srh_updated_at','std_name'], 'safe'],
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
        $query = WStudentReadmissionHistory::find()
            ->innerJoin('student','student.id=w_student_readmission_history.srh_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId');

        // add conditions that should always apply here
        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            $query->andFilterWhere(['student.std_dept_relId' => $inst->inst_dep_relId]);
        }

        $query->orderBy([
            'srh_HOD_approved' => SORT_ASC,
            'srh_acadYear_relId' => SORT_DESC,
            'srh_sem_relId' => SORT_DESC,
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
            'id' => $this->id,
            'srh_acadYear_relId' => $this->srh_acadYear_relId,
            'srh_sem_relId' => $this->srh_sem_relId,
            'srh_swh_relId' => $this->srh_swh_relId,
            'srh_std_relId' => $this->srh_std_relId,
            'srh_return_back_on' => $this->srh_return_back_on,
            'srh_HOD_approved' => $this->srh_HOD_approved,
            'srh_HOD_approved_at' => $this->srh_HOD_approved_at,
            'srh_HOD_approved_by' => $this->srh_HOD_approved_by,
            'srh_approved' => $this->srh_approved,
            'srh_approved_at' => $this->srh_approved_at,
            'srh_approved_by' => $this->srh_approved_by,
            'srh_created_at' => $this->srh_created_at,
            'srh_updated_at' => $this->srh_updated_at,
            'srh_created_by' => $this->srh_created_by,
            'srh_updated_by' => $this->srh_updated_by,
        ]);

        $query->andFilterWhere(['like', 'srh_return_back_letter', $this->srh_return_back_letter])
            ->andFilterWhere(['like', 'remark_HOD', $this->remark_HOD])
            ->andFilterWhere(['like', 'remark_registrar', $this->remark_registrar]);
        $query->andFilterWhere(['OR',
            ['like', 'first_name', $this->std_name],
            ['like', 'middle_name', $this->std_name],
            ['like', 'last_name', $this->std_name],
        ]);

        return $dataProvider;
    }
}
