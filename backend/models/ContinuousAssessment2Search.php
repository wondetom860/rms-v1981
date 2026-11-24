<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\ContinuousAssessment2;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ContinuousAssessment2Search represents the model behind the search form about `backend\models\ContinuousAssessment2`.
 */
class ContinuousAssessment2Search extends ContinuousAssessment2
{

    public $acadYearId;
    public $semId;
    public $pieReportArray = [];
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
            [['id', 'ca_std_relid', 'ca_semreg_relId', 'ca_markAssignment_relId', 'ca_catagorySetup_relId', 'ca_coff_relId', 'ca_created_by', 'ca_updated_by'], 'integer'],
            [['ca_markObtained'], 'number'],
            [['ca_created_at', 'ca_updated_at'], 'safe'],
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
        $query = ContinuousAssessment2::find();

        // add conditions that should always apply here

        if (isset($_GET['gsetId'])) {

            // var_dump($_GET['instanceId']);die();
            // find students who enrolled courses with in this group.
            $srd = \frontend\models\SemesterRegistration2Detail::find()
                ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->where([
                    'srd2_group_relId'=>intval($_GET['gsetId']),
                    'srd2_course_relId'=>intval($_GET['courseId']),
                ])
                ->innerJoin('continuous_assessment2','continuous_assessment2.ca_semreg_relId = semester_registration_2.id')
                ->all();
            $groupStds = [];
            $srdIds = [];
            if ($srd && count($srd) > 0) {
                foreach ($srd as $key) {
                    $ss = \frontend\models\SemesterRegistration2::findOne($key->srd2_semreg_relId)->semreg_std_relId;
                    $groupStds[] = $ss;
                    $srdIds[$ss] = $key->srd2_semreg_relId;
                    // $srdIds[$ss][] = $key->srd2_coff_relId;
                }
            }

            // those students may not be in the assessment list. add them if not.

            $this->saveLaggedSudents($groupStds,$srdIds);
            // 'ca_created_by'=>Yii::$app->user->id,

            $query = $query->andFilterWhere([
                'ca_markAssignment_relId'=>intval($_GET['instanceId']),
                'ca_std_relid'=>$groupStds,
                // hardly checks for an entry::
            ])
            ->innerJoin('cont_assess_catagory2','cont_assess_catagory2.id = continuous_assessment2.ca_catagorySetup_relId')
            ->andWhere(['cat_course_relId'=>$_GET['courseId']]);
        }else{
            // $query = NULL;
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'ca_std_relid' => $this->ca_std_relid,
            'ca_semreg_relId' => $this->ca_semreg_relId,
            'ca_markAssignment_relId' => $this->ca_markAssignment_relId,
            'ca_catagorySetup_relId' => $this->ca_catagorySetup_relId,
            'ca_markObtained' => $this->ca_markObtained,
            'ca_coff_relId' => $this->ca_coff_relId,
            'ca_created_by' => $this->ca_created_by,
            'ca_created_at' => $this->ca_created_at,
            'ca_updated_by' => $this->ca_updated_by,
            'ca_updated_at' => $this->ca_updated_at,
        ]);

        return $dataProvider;
    }

    private function saveLaggedSudents($groupStds,$srd){
        if (is_array($groupStds) && !empty($groupStds)) {
            $this->__constructor();
            foreach ($groupStds as $key) {
                $exists = $this->find()
                    ->innerJoin('cont_assess_catagory2','cont_assess_catagory2.id = continuous_assessment2.ca_catagorySetup_relId')
                    ->where([
                        'ca_markAssignment_relId'=>$_GET['instanceId']
                    ])
                    ->andWhere(['cat_course_relId'=>$_GET['courseId']])
                    ->andWhere([
                        'ca_std_relid'=>$key
                    ])
                    ->all();
                if ($exists && count($exists) > 0) {
                    // will be returned.
                }else{
                    // get catagory made for the course.
                    $catagory = new ContAssessCatagory2();
                    $ima = new InstanceMarkAssignment();

                    $model = new ContinuousAssessment2();

                    $modelCoff = new CourseOffering();
                    $catagoryId = $ima->getImaCatagory(intval($_GET['instanceId']))->ima_catagorySetup_relId;

                    $s = ContinuousAssessment2::findOne(['ca_catagorySetup_relId'=>$catagoryId]);
                    if ($s) {
                        
                    }else{
                        // return "Such Assessment Not Collected yet.";
                        return;
                    }
                    $model->ca_coff_relId = ContinuousAssessment2::findOne(['ca_catagorySetup_relId'=>$catagoryId])->ca_coff_relId;

                    $model->ca_std_relid = $key;
                    $model->ca_created_by = \Yii::$app->user->identity->id;
                    $model->ca_markAssignment_relId = intval($_GET['instanceId']);
                    // $model->ca_course_relId = intval($_POST['courseId']);
                    $model->ca_created_at = date('Y-m-d h:i:s');
                    $model->ca_catagorySetup_relId = $catagoryId;
                    $model->ca_semreg_relId = $srd[$key];
                    // $model->ca_coff_relId = NULL;

                    $model->save(false);

                    unset($model);
                }
            }
        }
    }
}
