<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "instance_mark_assignment".
 *
 * @property integer $id
 * @property integer $ima_catagorySetup_relId
 * @property double $ima_possiblePoint 
 * @property string $ima_instanceName
 * @property integer $ima_created_by
 * @property string $ima_created_at
 * @property integer $ima_updated_by
 * @property string $ima_updated_at
 *
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property ContAssessCatagory2 $imaCatagorySetupRel
 * @property User $imaUpdatedBy
 * @property User $imaCreatedBy
 */
class InstanceMarkAssignment extends \yii\db\ActiveRecord
{

    public $acadYearId;
    public $semId;
    public $pieReportArray = [];
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public $ima_course;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'instance_mark_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ima_catagorySetup_relId', 'ima_possiblePoint', 'ima_instanceName', 'ima_created_by', 'ima_created_at'], 'required'],
            [['ima_catagorySetup_relId', 'ima_created_by', 'ima_updated_by'], 'integer'],
            [['ima_possiblePoint'], 'number'],
            [['ima_created_at', 'ima_updated_at','ima_assessed'], 'safe'],
            [['ima_instanceName'], 'string', 'max' => 50],
            [['ima_catagorySetup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory2::className(), 'targetAttribute' => ['ima_catagorySetup_relId' => 'id']],
            [['ima_course'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['ima_course' => 'id']],

            [['ima_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ima_updated_by' => 'id']],
            [['ima_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ima_created_by' => 'id']],
            [['ima_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ima_approved_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ima_course'=>Yii::t('rms', 'Course Title'),
            'ima_catagorySetup_relId' => Yii::t('rms', 'Catagory Setup ID'),
            'ima_possiblePoint' => Yii::t('rms', 'Possible Point'),
            'ima_instanceName' => Yii::t('rms', 'Instance Name'),
            'ima_created_by' => Yii::t('rms', 'Created By'),
            'ima_created_at' => Yii::t('rms', 'ICreated At'),
            'ima_updated_by' => Yii::t('rms', 'Updated By'),
            'ima_updated_at' => Yii::t('rms', 'Updated At'),
            'ima_approved' => Yii::t('rms', 'Approve Status'),
        ];
    }

    // Get assessment under the selected assessment catagory,
    public function getCourseAssessInstances($coffdetId,$catagoryId){
        return $this->find()->where(['ima_catagorySetup_relId'=>$catagoryId])->all();
    }


    // get each mark the student has missed. for make up exam mark submission
    public function getMissingStudentMark($stdId,$courseId){
        $allInstances = $this->getAssessmentInstances2($courseId,1);
    }


    // get remaining points under the selected catagory
    public function remainingPoint($catagoryId,$max){
        return [$max - $this->find()->where(['ima_catagorySetup_relId'=>$catagoryId])->sum('ima_possiblePoint'),$this->getSlice($catagoryId)];
    }

   public function getAssessmentInstances2($courseId,$wonde = NULL,$acadYearId=NULL,$semId=NULL){
        $this->__constructor();
        $this->acadYearId = $acadYearId ? $acadYearId : $this->acadYearId;
        $this->semId = $semId ? $semId : $this->semId;
        if (is_null($wonde)) {
            return $this->find()
                ->innerJoin('cont_assess_catagory2','cont_assess_catagory2.id = instance_mark_assignment.ima_catagorySetup_relId')
                ->where([
                    'cont_assess_catagory2.cat_acadYear_relId'=>$this->acadYearId,
                    'cont_assess_catagory2.cat_sem_relId'=>$this->semId
                    ])
                ->andWhere(['cont_assess_catagory2.cat_course_relId'=>$courseId,'ima_approved'=>1])
                ->all();
        }

        return $this->find()
                ->innerJoin('cont_assess_catagory2','cont_assess_catagory2.id = instance_mark_assignment.ima_catagorySetup_relId')
                ->where([
                    'cont_assess_catagory2.cat_acadYear_relId'=>$this->acadYearId,
                    'cont_assess_catagory2.cat_sem_relId'=>$this->semId
                    ])
                ->andWhere(['cont_assess_catagory2.cat_course_relId'=>$courseId,'ima_approved'=>1,'ima_assessed'=>1])
                ->all();
       
   } 

    private function getSlice($catagoryId){
        $catName = CatagoryName2::find()->innerJoin('cont_assess_catagory2','cont_assess_catagory2.cat_catagory_relId = catagory_name2.id')
        ->where(['cont_assess_catagory2.id'=>$catagoryId])->one();
        return $catName->catagory_name .'_'.($this->find()->where(['ima_catagorySetup_relId'=>$catagoryId])->count()+1);
    }

    // find all assessments' instances made for this course
    public function getAssessmentInstances($courseId,$coffDetId){
        return $this->find()->where(['ca_coff_relId'=>$coffDetId])->distinct('ca_assignment_relId')->all();
    }

    public function getInstance($catagoryId,$haile = NULL){
        // Yesirahin bila.

        // ima.id <> ima.id ==> such query waste my time of 1/2hr.
        // $tthis = ContinuousAssessment2::find()->all();
        // $tthisArray = [];
        // if ($tthis && count($tthis) > 0) {
        //     foreach ($tthis as $key) {
        //         $tthisArray[] = $key->ca_markAssignment_relId;
        //     }
        //     if (is_null($haile)) {
        //         return $this->find()
        //             ->innerJoin('continuous_assessment2','continuous_assessment2.ca_markAssignment_relId = instance_mark_assignment.id')
        //             ->where(['ima_catagorySetup_relId'=>$catagoryId])
        //             ->andWhere(['NOT IN','instance_mark_assignment.id',$tthisArray])
        //             ->all();
        //     }
        //     return $this->find()
        //         ->where(['ima_catagorySetup_relId'=>$catagoryId,'ima_approved'=>1])
        //         ->innerJoin('continuous_assessment2','continuous_assessment2.ca_markAssignment_relId = instance_mark_assignment.id')
        //         ->andWhere(['NOT IN','instance_mark_assignment.id',$tthisArray])
        //         ->all();
        // }else{
        //     if (is_null($haile)) {
        //         return $this->find()
        //             ->where(['ima_catagorySetup_relId'=>$catagoryId])
        //             ->all();
        //     }
        //     return $this->find()
        //         ->where(['ima_catagorySetup_relId'=>$catagoryId,'ima_approved'=>1])
        //         ->all();
        // }

        if (is_null($haile)) {
                return $this->find()
                    ->where(['ima_catagorySetup_relId'=>$catagoryId])
                    ->all();
            }
            return $this->find()
                ->where(['ima_catagorySetup_relId'=>$catagoryId,'ima_approved'=>1])
                ->all();
        // return $this->find()->where(['ima_catagorySetup_relId'=>$catagoryId])->all();
        
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s($stdId = null)
    {
        if (!is_null($stdId)) {
            return $this->hasMany(ContinuousAssessment2::className(), ['ca_markAssignment_relId' => 'id'])->andFilterWhere(['ca_std_relid' => $stdId]);
        }
        return $this->hasMany(ContinuousAssessment2::className(), ['ca_markAssignment_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaCatagorySetupRel()
    {
        return $this->hasOne(ContAssessCatagory2::className(), ['id' => 'ima_catagorySetup_relId']);
    }

    public function getImaCatagory($imaId){
        return $this->findOne($imaId);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaCourse()
    {
        $catagory = ContAssessCatagory2::findOne($this->ima_catagorySetup_relId);
        $this->ima_course = $catagory->cat_course_relId;
        if (!is_null($this->ima_course)) {
            return $this->hasOne(Course::className(), ['id' => 'ima_course']);
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ima_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ima_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ima_created_by']);
    }
}
