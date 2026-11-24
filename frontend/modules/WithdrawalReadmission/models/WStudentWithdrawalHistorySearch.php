<?php

namespace frontend\modules\WithdrawalReadmission\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory;

/**
 * WStudentWithdrawalHistorySearch represents the model behind the search form of `frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory`.
 */
class WStudentWithdrawalHistorySearch extends WStudentWithdrawalHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'swh_acadYear_relId', 'swh_sem_relId', 'swh_std_relId', 'swh_reason_relId', 'swh_course_removed', 'swh_approved', 'swh_approved_by', 'swh_created_by', 'swh_updated_by', 'swh_HOD_approved', 'swh_HOD_approved_by', 'swh_returnedBack', 'swh_stdSigned', 'swh_stdSigned_by', 'swh_lastApproved', 'swh_lastApproved_by','section_relId'], 'integer'],
            [['swh_approved_at', 'swh_created_at', 'swh_updated_at', 'swh_HOD_approved_at', 'swh_stdSigned_at', 'swh_lastApproved_at', 'swh_supDoc', 'swh_last_dateAttended','std_name'], 'safe'],
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
        $query = WStudentWithdrawalHistory::find()
            ->innerJoin('student','student.id=w_student_withdrawal_history.swh_std_relId')
            ->innerJoin('user','user.id=student.std_user_relId');

        // add conditions that should always apply here
        $query->orderBy([
            'swh_HOD_approved' => SORT_ASC,
            'swh_acadYear_relId' => SORT_DESC,
            'swh_sem_relId' => SORT_DESC,
        ]);

        if (\Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            $query->andFilterWhere(['student.std_dept_relId' => $inst->inst_dep_relId]);
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
            'swh_acadYear_relId' => $this->swh_acadYear_relId,
            'swh_sem_relId' => $this->swh_sem_relId,
            'swh_std_relId' => $this->swh_std_relId,
            'swh_reason_relId' => $this->swh_reason_relId,
            'swh_course_removed' => $this->swh_course_removed,
            'swh_approved' => $this->swh_approved,
            'swh_approved_by' => $this->swh_approved_by,
            'swh_approved_at' => $this->swh_approved_at,
            'swh_created_at' => $this->swh_created_at,
            'swh_created_by' => $this->swh_created_by,
            'swh_updated_at' => $this->swh_updated_at,
            'swh_updated_by' => $this->swh_updated_by,
            'swh_HOD_approved' => $this->swh_HOD_approved,
            'swh_HOD_approved_at' => $this->swh_HOD_approved_at,
            'swh_HOD_approved_by' => $this->swh_HOD_approved_by,
            'swh_returnedBack' => $this->swh_returnedBack,
            'swh_stdSigned' => $this->swh_stdSigned,
            'swh_stdSigned_at' => $this->swh_stdSigned_at,
            'swh_stdSigned_by' => $this->swh_stdSigned_by,
            'swh_lastApproved' => $this->swh_lastApproved,
            'swh_lastApproved_at' => $this->swh_lastApproved_at,
            'swh_lastApproved_by' => $this->swh_lastApproved_by,
            'swh_last_dateAttended' => $this->swh_last_dateAttended,
        ]);

        $query->andFilterWhere(['OR',
            ['like', 'first_name', $this->std_name],
            ['like', 'middle_name', $this->std_name],
            ['like', 'last_name', $this->std_name],
        ]);

        return $dataProvider;
    }
}
