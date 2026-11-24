<?php

namespace backend\models;

// use frontend\modules\AcStEva\models\AseEvaluationResult;
use Yii;
use common\components\CommonControllerFunctions as CCF;
use frontend\modules\AcStEva\models\AseEvaluationResult;

/**
 * This is the model class for table "semester_evaluation2".
 *
 * @property integer $id
 * @property integer $semev_acadYear_relId
 * @property integer $semev_sem_relId
 * @property integer $semev_group_relId
 * @property integer $semev_course_relId
 * @property integer $semev_evaluee_relId
 * @property integer $semev_evalType_relId
 * @property double $semev_averagePoint 
 *
 * @property SchoolYear $semevAcadYearRel
 * @property Course $semevCourseRel
 * @property SemesterEvaluationTypes $semevEvalTypeRel
 * @property User $semevEvalueeRel
 * @property WGroupSetup $semevGroupRel
 * @property SemesterEvaluation2Detail[] $semesterEvaluation2Details
 */
class SemesterEvaluation2 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'semester_evaluation2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['semev_acadYear_relId', 'semev_sem_relId', 'semev_evaluee_relId', 'semev_evalType_relId', 'semev_averagePoint'], 'required'],
            [['semev_acadYear_relId', 'semev_sem_relId', 'semev_course_relId', 'semev_evaluee_relId', 'semev_evalType_relId'], 'integer'],
            [['semev_averagePoint'], 'number'],
            [['semev_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['semev_acadYear_relId' => 'id']],

            [['semev_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['semev_sem_relId' => 'id']],

            [['semev_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['semev_course_relId' => 'id']],
            [['semev_evalType_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterEvaluationTypes::class, 'targetAttribute' => ['semev_evalType_relId' => 'id']],
            [['semev_evaluee_relId'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['semev_evaluee_relId' => 'id']],
            [['semev_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['semev_group_relId' => 'id']],
        ];
    }

    public function getAverage(){
        $totalEvaluators = $this->getSemesterEvaluation2Details()->andFilterWhere(['semev2det_completed' => 1])->count();
        // return $totalEvaluators == 0 ? " - " : sprintf("%0.2f",($this->semev_averagePoint / $totalEvaluators));
        $average = '-';
        if ($totalEvaluators > 0) {  
            if ($totalEvaluators > 1) {
                $average = $this->semev_averagePoint * ($this->semevEvalTypeRel->semev_type_maxPoint / $totalEvaluators);
            }else{
                $average = $this->semev_averagePoint;
            }
            $average = sprintf("%0.2f",$average);
        }
        return $average;
    }

    public function deletePossible(){
        // check if the calendar is active:
        $actionName = 'Semester evaluation';
        $action = AcademicCalanderAction::findOne(['aca_name'=>$actionName]);
        if ($action) {
            $schedule = CCF::getScheduleForAction(null,$action->id);
            if($schedule && count($schedule)>0){
                return $schedule->acad_acadYear_relId == $this->semev_acadYear_relId && $schedule->acad_sem_relId == $this->semev_sem_relId;
            }
        }
    }

    public function getCoffDets(){
        $inst = Instructor::findOne(['inst_user_relId' => $this->semev_evaluee_relId]);
        if (!isset($inst->id)) {
            return NULL;
        }
        return CourseOffering2Detail::find()
            ->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
            ->where([
                'coff2_acadYear_relId' => $this->semev_acadYear_relId,
                'coff2_sem_relId' => $this->semev_sem_relId,
            ])
            ->andWhere(['OR',['coff2det_inst_relId' => $inst->id],['coff2det_TA_relId' => $inst->id]])
            ->all();
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'semev_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'semev_sem_relId' => Yii::t('rms', 'Semester'),
            'semev_group_relId' => Yii::t('rms', 'Group'),
            'semev_course_relId' => Yii::t('rms', 'Course'),
            'semev_evaluee_relId' => Yii::t('rms', 'Evaluee'),
            'semev_evalType_relId' => Yii::t('rms', 'Eval. Type'),
            'semev_averagePoint' => Yii::t('rms', 'Average Pt.'),
        ];
    }

    // Checks if the specified student evaluated the instructor or not.
    public function checkIfEvaluated($groupId,$courseId,$evalueeId,$evaluatorId){
        $gset = WGroupSetup::findOne(['id'=>$groupId]);
        // check for older version of semester evaluation:
        $cnt = SemesterEvaluation2Detail::find()
            ->innerJoin('semester_evaluation2','semester_evaluation2.id=semester_evaluation2_detail.semev2det_semev_relId')
            ->where([
                'semev_acadYear_relId'=>$gset->gs_acadYear_relId,
                'semev_sem_relId'=>$gset->gs_sem_relId,
                'semev_evaluee_relId'=>$evalueeId,
                'semev_course_relId'=>$courseId,
                'semev_group_relId'=>$groupId,
                'semev2det_evaluator_relId'=>$evaluatorId,
                'semev2det_completed'=>1,
            ])
            ->count() > 0;
        if ($cnt) {
            return true;
        }else{
            // check for the new version of semester evaluation:
            $modelAseNewEvaluation = new AseEvaluationResult();
            return $modelAseNewEvaluation->checkIfEvaluated($gset,$courseId, $evalueeId, $evaluatorId);            
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'semev_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'semev_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'semev_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevEvalTypeRel()
    {
        return $this->hasOne(SemesterEvaluationTypes::class, ['id' => 'semev_evalType_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevEvalueeRel()
    {
        return $this->hasOne(User::class, ['id' => 'semev_evaluee_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemevGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'semev_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterEvaluation2Details()
    {
        return $this->hasMany(SemesterEvaluation2Detail::class, ['semev2det_semev_relId' => 'id']);
    }
}