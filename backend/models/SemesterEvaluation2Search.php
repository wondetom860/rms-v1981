<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\SemesterEvaluation2;
use \common\components\CommonControllerFunctions as CCF;

/**
 * SemesterEvaluation2Search represents the model behind the search form about `backend\models\SemesterEvaluation2`.
 */
class SemesterEvaluation2Search extends SemesterEvaluation2
{

    public $acadYearId;
    public $semId;
    public $semev_evaluee_name;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'semev_acadYear_relId', 'semev_sem_relId', 'semev_group_relId', 'semev_course_relId', 'semev_evaluee_relId', 'semev_evalType_relId'], 'integer'],
            [['semev_averagePoint'], 'number'],
            [['semev_evaluee_name'], 'string'],
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
        $query = SemesterEvaluation2::find()->innerJoin('user', 'user.id = semester_evaluation2.semev_evaluee_relId');
        $this->__constructor();

        // add conditions that should always apply here
        // $query = $query->where(['semev_acadYear_relId'=>$this->acadYearId,'semev_sem_relId'=>$this->semId]);

        if (Yii::$app->user->can('DepartmentHead')) {
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
            // $query = $query->innerJoin('user','user.id = semester_evaluation2.semev_evaluee_relId');
            $deptGroups = [];
            $i_users = [];
            if ($instructor) {
                // Get groups assigned for his dept.
                $gsets = \backend\models\WGroupSetup::find()
                    ->where([
                        'gs_acadYear_relId' => $this->acadYearId,
                        'gs_sem_relId' => $this->semId,
                    ])
                    ->andWhere([
                        'gs_dept_relId' => $instructor->inst_dep_relId,
                    ])
                    ->all();
                if ($gsets && count($gsets) > 0) {
                    foreach ($gsets as $gset) {
                        array_push($deptGroups, $gset['id']);
                    }
                }

                // List all instructor under the Hods Department::
                $insts = \backend\models\Instructor::find()->where(['inst_dep_relId' => $instructor->inst_dep_relId])->all();
                if ($insts && count($insts) > 0) {
                    foreach ($insts as $keyInst) {
                        $i_users[] = $keyInst->inst_user_relId;
                    }
                }
                $query = $query->where([
                    'OR',
                    ['semev_group_relId' => $deptGroups],
                    ['semev_evaluee_relId' => $i_users]

                ]);
            }
        } else if (Yii::$app->user->can('AcademicDean')) {
            // ->andFilterWhere(['semev_type_level' => 11])
            $query->innerJoin('semester_evaluation_types', 'semester_evaluation_types.id=semester_evaluation2.semev_evalType_relId');
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $query->innerJoin('semester_evaluation_types', 'semester_evaluation_types.id=semester_evaluation2.semev_evalType_relId');
        } else {
            $query->andFilterWhere(['semester_evaluation2.id' => 0]);
        }

        // $query->groupBy('semev_evalType_relId');

        $query->orderBy([
            'semev_acadYear_relId' => SORT_DESC,
            'semev_sem_relId' => SORT_ASC,
            'first_name' => SORT_ASC,
            'middle_name' => SORT_ASC,
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
            'semev_acadYear_relId' => $this->semev_acadYear_relId,
            'semev_sem_relId' => $this->semev_sem_relId,
            'semev_group_relId' => $this->semev_group_relId,
            'semev_course_relId' => $this->semev_course_relId,
            'semev_evaluee_relId' => $this->semev_evaluee_relId,
            'semev_evalType_relId' => $this->semev_evalType_relId,
            'semev_averagePoint' => $this->semev_averagePoint,
        ]);
        $query->andFilterWhere([
            'OR',
            ['like', 'user.first_name', $this->semev_evaluee_name],
            ['like', 'user.middle_name', $this->semev_evaluee_name]
        ]);
        return $dataProvider;
    }
}
