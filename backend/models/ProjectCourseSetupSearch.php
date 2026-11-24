<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use backend\models\ProjectCourseSetup;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ProjectCourseSetupSearch represents the model behind the search form about `backend\models\ProjectCourseSetup`.
 */
class ProjectCourseSetupSearch extends ProjectCourseSetup
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

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
            [['id', 'pcs_acadYear_relId', 'pcs_sem_relId', 'pcs_gset_relId', 'pcs_course_relId', 'pcs_pgName_relId','pcs_group_leader', 'pcs_approved', 'pcs_approved_by', 'pcs_created_by', 'pcs_updated_by'], 'integer'],
            [['pcs_title', 'pcs_advisor',  'pcs_approved_at', 'pcs_created_at', 'pcs_updated_at'], 'safe'],
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

    protected function getGsetArray1($i){
        $instDept = NULL;
        // Get current HoDs dept.
        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;

        }
        // Then groups Assigned to his dept.

        $this->__constructor();
        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId'=>$this->acadYearId,
                'gs_sem_relId'=>$this->semId,
                'gs_dept_relId'=>$instDept])
            ->all();
            $gsetArray  = [];
        if ($gset && count($gset) > 0) {
            if ($i==0) {
                foreach ($gset as $key ) {
                    $gsetArray[] = $key->id;
                }
                return $gsetArray;
            }else if ($i == 1) {
                // $option = "<option value=''> - </option>";
                // foreach ($gset as $key ) {
                //    $gname = \backend\models\WGroup::findOne(['id'=>$key->gs_group_relId])->group_name;
                //    $adm = \backend\models\Admission::findOne(['id'=>$key->gs_admission_relId])->admission_name;
                //    $prog = \backend\models\Program::findOne(['id'=>$key->gs_prog_relId])->program_name;

                //    $clslvl = \backend\models\ClassLevel::findOne(['id'=>$key->gs_clslvl_relId])->clslvl_num;
                //    $spec = isset($key->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$key->gs_spec_relId])->spec_name:'-';

                //    $dept = \backend\models\Department::findOne(['id'=>$key->gs_dept_relId])->dept_name;

                //    $title = $adm . '_'.$prog.'_'.$dept.'_'.$clslvl.'_'.$spec;
                //    $option .= "<option value='{$key->id}' title='{$title}'>{$gname}</option>";
                // }
                return $gset;
            }
        }
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

        $gsetArray  =$this->getGsetArray1(0);

        $query = $query->where(['pcs_gset_relId'=>$gsetArray]);

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
        $query->andFilterWhere(['like', 'pcs_advisor', $this->pcs_advisor]);

        return $dataProvider;
    }
}
