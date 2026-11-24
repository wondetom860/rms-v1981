<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Attendancedetail;

/**
 * AttendancedetailSearch represents the model behind the search form of `frontend\models\Attendancedetail`.
 */
class AttendancedetailSearch extends Attendancedetail
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'ad_attendance_relId', 'ad_student_relId', 'ad_status'], 'integer'],
            [['ad_remark'], 'safe'],
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
        $query = Attendancedetail::find();

        // add conditions that should always apply here

        if (Yii::$app->user->can('Instructor')) {
            $query->innerJoin('attendance','attendance.id = attendancedetail.ad_attendance_relId')
                ->andFilterWhere(['a_createdBy'=>Yii::$app->user->identity->id]);
        }else if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            $deptgroups = \backend\models\WGroupSetup::getCurrentDepGroups($instDept);
            $deptgroupsArray = [];
            if ($deptgroups && count($deptgroups) > 0) {
                foreach ($deptgroups as $key) {
                    $deptgroupsArray[] = $key->id;
                }
            }
            $query->innerJoin('attendance','attendance.id = attendancedetail.ad_attendance_relId')
                ->andFilterWhere(['a_group_relId'=>$deptgroupsArray]);
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
            'id' => $this->id,
            'ad_attendance_relId' => $this->ad_attendance_relId,
            'ad_student_relId' => $this->ad_student_relId,
            'ad_status' => $this->ad_status,
        ]);

        $query->andFilterWhere(['like', 'ad_remark', $this->ad_remark]);

        return $dataProvider;
    }
}
