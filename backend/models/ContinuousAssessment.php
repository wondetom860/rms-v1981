<?php

namespace backend\models;
use \common\components\CommonControllerFunctions as CCF;

use Yii;

/**
 * This is the model class for table "continuous_assessment".
 *
 * @property integer $id
 * @property integer $ca_coff_relId
 * @property integer $ca_semreg_relId
 * @property integer $ca_std_relId
 * @property integer $ca_assignment_relId
 * @property double $ca_markObtained
 * @property string $ca_created_at
 * @property integer $ca_created_by
 * @property integer $ca_updated_by
 * @property string $ca_updated_at
 * @property integer $ca_approved
 * @property string $ca_approved_at
 * @property integer $ca_approved_by
 *
 * @property CourseOffering2Detail $caCoffRel
 * @property SemesterRegistration2 $caSemregRel
 * @property Student $caStdRel
 * @property User $caCreatedBy
 * @property User $caUpdatedBy
 * @property User $caApprovedBy
 * @property ContAssessMarkAssignment $caAssignmentRel
 */
class ContinuousAssessment extends \yii\db\ActiveRecord
{
    public $studentList = NULL; //Variable that holds students and their corresponding mark. 
    // e.g $studentList[stdId] = mark   => $studentList[15]=>12;
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
    public static function tableName()
    {
        return 'continuous_assessment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ca_coff_relId', 'ca_semreg_relId', 'ca_std_relId', 'ca_assignment_relId', 'ca_created_at', 'ca_created_by', 'ca_updated_by', 'ca_updated_at'], 'required'],
            [['ca_coff_relId', 'ca_semreg_relId', 'ca_std_relId', 'ca_assignment_relId', 'ca_created_by', 'ca_updated_by', 'ca_approved', 'ca_approved_by'], 'integer'],
            [['ca_markObtained'], 'number'],
            [['ca_created_at', 'ca_updated_at', 'ca_approved_at'], 'safe'],
            [['ca_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::className(), 'targetAttribute' => ['ca_coff_relId' => 'id']],
            [['ca_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\SemesterRegistration2::className(), 'targetAttribute' => ['ca_semreg_relId' => 'id']],
            [['ca_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['ca_std_relId' => 'id']],
            [['ca_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ca_created_by' => 'id']],
            [['ca_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ca_updated_by' => 'id']],
            [['ca_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ca_approved_by' => 'id']],
            [['ca_assignment_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessMarkAssignment::className(), 'targetAttribute' => ['ca_assignment_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ca_coff_relId' => Yii::t('rms', 'Ca Coff Rel ID'),
            'ca_semreg_relId' => Yii::t('rms', 'Ca Semreg Rel ID'),
            'ca_std_relId' => Yii::t('rms', 'Ca Std Rel ID'),
            'ca_assignment_relId' => Yii::t('rms', 'Ca Assignment Rel ID'),
            'ca_markObtained' => Yii::t('rms', 'Ca Mark Obtained'),
            'ca_created_at' => Yii::t('rms', 'Ca Created At'),
            'ca_created_by' => Yii::t('rms', 'Ca Created By'),
            'ca_updated_by' => Yii::t('rms', 'Ca Updated By'),
            'ca_updated_at' => Yii::t('rms', 'Ca Updated At'),
            'ca_approved' => Yii::t('rms', 'Ca Approved'),
            'ca_approved_at' => Yii::t('rms', 'Ca Approved At'),
            'ca_approved_by' => Yii::t('rms', 'Ca Approved By'),
        ];
    }

    public function updateFinalExamMark($contAssObj,$afterMark){
        if ($contAssObj && count($contAssObj) > 0) {
            $upResult = Yii::$app->db->createCommand()
                ->update('continuous_assessment',
                    ['ca_markObtained'=>$afterMark],
                    ['id'=>$contAssObj->id])
                ->execute();
            if ($upResult) {
                $contAssessment =  $this->find()
                    ->where(['ca_semreg_relId'=>$contAssObj->ca_semreg_relId,'ca_std_relId'=>$contAssObj->ca_std_relId,'ca_coff_relId'=>$contAssObj->ca_coff_relId])
                    ->all();
            // return $contAssessment;
            return $this->calculateTotal($contAssessment);
            }else{
                return NULL;
            }
        }else{
            return NULL;
        }

    }

    public function calculateTotal($contAssessment){
        if ($contAssessment && count($contAssessment) > 0) {
            $totalObtained = 0;
            $totalAssigned = 0;

            foreach ($contAssessment as $key) {
                $maxPoint = ContAssessMarkAssignment::findOne(['id'=>$key->ca_assignment_relId])->ma_possiblePoint;
                $totalAssigned += $maxPoint;
                $totalObtained += $key->ca_markObtained;
            }

            // return $totalAssigned;
            if ($totalAssigned >= 100) {
                $srd2 = \frontend\models\SemesterRegistration2Detail::findOne(['srd2_semreg_relId'=>$contAssessment[0]['ca_semreg_relId']]);
                return $this->setLetterGrade($totalAssigned,$totalObtained,$contAssessment[0]['ca_std_relId'],$contAssessment[0]['ca_coff_relId'],$srd2->id);
            }else{
                return NULL;
            }
        }else{
            return NULL;
        }
    }

    public function setLetterGr3ade($totalAssigned, $totalObtained, $stdId, $coffDetId,$srd2){
        if ($totalAssigned >= 100 && !is_null($totalObtained)) {
            // read interval which contains the mark point obtained by the student.
            $ltrGrade = GradeScale::find()
                ->where(['<=','gs_min',$totalObtained])->andWhere(['>=','gs_max',$totalObtained])
                ->one();

            // $coffdet = CourseOffering2Detail::findOne(['id'=>$coffDetId]);
            if ($ltrGrade && count($ltrGrade) > 0) {
                // valid lGrade scale is found.
                // update semreg detail.
                $exists = \frontend\models\SemesterRegistration2Detail::find()
                    ->where(['id'=>$srd2])
                    ->andWhere(['OR',['NOT','srd2_grade_relId'=>$ltrGrade->id],['srd2_grade_relId'=>NULL]])
                    ->one();
                // var_dump($exists);die();
                if ($exists && count($exists) > 0) {
                    $result = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',['srd2_grade_relId'=>$ltrGrade->id],['id'=>$srd2])
                        ->execute();
                    if ($result) {
                        return $ltrGrade;
                    }else{return NULL;}
                }else{
                    return $ltrGrade;
                }
            }else{return NULL;}
        }
    }

    // find all assessments' instances made for this course
    public function getAssessmentInstances($courseId,$coffDetId){
        return $this->find()->where(['ca_coff_relId'=>$coffDetId])->distinct('ca_assignment_relId')->all();
    }

    // Get student''s mark obtained for the assessment instance.
    public function getStudentMark($courseId, $studentId,$catId,$assignmentId){
        return $this->find()
            ->where(['id'=>$catId,'ca_std_relId'=>$studentId,'ca_assignment_relId'=>$assignmentId])
            ->one();
     }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::className(), ['id' => 'ca_coff_relId']);
    }

    // get student semesetr registration record.
    public function getCaSemregRel2($stdId,$courseId){
        $this->__constructor();
        return \frontend\models\SemesterRegistration2::find()
            ->where(['semreg_acadYear_relId'=>$this->acadYearId,'semreg_semester_relId'=>$this->semId,'semreg_std_relId'=>$stdId])
            ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->andWhere(['srd2_course_relId'=>$courseId])
            ->one();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaSemregRel()
    {
        return $this->hasOne(\frontend\models\SemesterRegistration2::className(), ['id' => 'ca_semreg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'ca_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ca_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ca_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ca_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCaAssignmentRel()
    {
        return $this->hasOne(ContAssessMarkAssignment::className(), ['id' => 'ca_assignment_relId']);
    }
}
