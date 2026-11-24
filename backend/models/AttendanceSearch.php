<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
// use backend\models\Attendance;

/**
 * AttendanceSearch represents the model behind the search form of `frontend\models\Attendance`.
 */
class AttendanceSearch extends Attendance
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'a_instructor_relId', 'a_course_relId','a_hour','acadYear','semester', 'a_group_relId'], 'integer'],
            [['a_createdAt', 'a_updatedAt','course'], 'safe'],
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

        $gsetTable = WGroupSetup::tableName();
        $courseTable = Course::tableName();
        $query = Attendance::find()
            ->innerJoin($courseTable,$courseTable.'.id = attendance.a_course_relId')
            ->innerJoin($gsetTable,$gsetTable.'.id = attendance.a_group_relId');

        // add conditions that should always apply here

        if (Yii::$app->user->can('Instructor')) {
            $query->andFilterWhere(['a_createdBy'=>Yii::$app->user->identity->id]);
                
        }else if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            $deptgroups = \backend\models\WGroupSetup::find()->where(['gs_dept_relId' => $instDept])->all();
            $deptgroupsArray = [];
            if ($deptgroups && count($deptgroups) > 0) {
                foreach ($deptgroups as $key) {
                    $deptgroupsArray[] = $key->id;
                }
            }
            $query->andFilterWhere(['OR',
                ['a_group_relId'=>$deptgroupsArray],
                ['a_createdBy'=>Yii::$app->user->identity->id],
            ]);
        }else if(Yii::$app->user->can('PGCoordinator')){
            $query->innerJoin('program','program.id='.$gsetTable.'.gs_prog_relId')
                ->andFilterWhere(['program_name' => 'MASTERS']);
        }

        $query->orderBy(['a_group_relId' => SORT_DESC]);

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
            $gsetTable.'.gs_acadYear_relId' => $this->acadYear,
            $gsetTable.'.gs_sem_relId' => $this->semester,
            $courseTable.'.course_title' => $this->course,
            'a_instructor_relId' => $this->a_instructor_relId,
            'a_course_relId' => $this->a_course_relId,
            'a_group_relId' => $this->a_group_relId,
        ]);

        return $dataProvider;
    }
}
