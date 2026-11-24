<?php

namespace backend\models;

use Yii;
use common\components\CommonControllerFunctions As CCF;
use yii\helpers\VarDumper;

/**
 * This is the model class for table "course_drop_history".
 *
 * @property integer $id
 * @property integer $cdh_srd2_relId
 * @property integer $cdh_std_relId
 * @property integer $cdh_course_relId
 * @property integer $cdh_acadYear_relId
 * @property integer $cdh_sem_relId
 * @property integer $cdh_group_relId
 * @property integer $cdh_clslvl_relId
 * @property integer $cdh_approved_by
 * @property integer $cdh_status
 * @property integer $cdh_restore_path  -- the path where the serialized data of the drop request is stored.
 * @property string $cdh_created_at
 * @property string $cdh_updated_at
 * @property integer $cdh_updated_by
 *
 * @property Student $cdhStdRel
 * @property Course $cdhCourseRel
 * @property SchoolYear $cdhAcadYearRel
 * @property Semester $cdhSemRel
 * @property WGroupAssignment $cdhGroupRel
 * @property ClassLevel $cdhClslvlRel
 * @property User $cdhApprovedBy
 * @property SemesterRegistration2Detail $cdhSrd2Rel
 */

/*
 * @property integer $id
 * @property integer $sa_acadYear_relId
 * @property integer $sa_sem_relId
 * @property integer $sa_adm_relId
 * @property integer $sa_prog_relId
 * @property integer $sa_dept_relId
 * @property integer $sa_clslvl_relId
 * @property integer $sa_spec_relId
 * @property integer $sa_inst_relId 
 */
class CourseDropHistory extends \yii\db\ActiveRecord
{

    public $possible = true;
    public $message = "";

    public static $_inst;
    public static $_advisingGsets;

    public $stdId,$first_name,$courseTitle,$maxCredit,$minCredit,$collectedCredit,$pendingCredit,$dropableCreditHrs;
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
        return 'course_drop_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cdh_srd2_relId', 'cdh_std_relId', 'cdh_course_relId', 'cdh_acadYear_relId', 'cdh_sem_relId', 'cdh_clslvl_relId', 'cdh_created_at'], 'required'],
            [['cdh_srd2_relId', 'cdh_std_relId', 'cdh_course_relId', 'cdh_acadYear_relId', 'cdh_sem_relId', 'cdh_group_relId', 'cdh_clslvl_relId', 'cdh_approved_by', 'cdh_status', 'cdh_updated_by'], 'integer'],
            [['cdh_created_at', 'cdh_updated_at','cdh_restore_path'], 'safe'],
            [['cdh_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['cdh_std_relId' => 'id']],
            [['cdh_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['cdh_course_relId' => 'id']],
            [['cdh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['cdh_acadYear_relId' => 'id']],
            [['cdh_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['cdh_sem_relId' => 'id']],
            [['cdh_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupAssignment::class, 'targetAttribute' => ['cdh_group_relId' => 'id']],
            [['cdh_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['cdh_clslvl_relId' => 'id']],
            [['cdh_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cdh_approved_by' => 'id']],
            [['cdh_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\SemesterRegistration2Detail::class, 'targetAttribute' => ['cdh_srd2_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cdh_srd2_relId' => Yii::t('rms', 'Student Name'),
            'cdh_std_relId' => Yii::t('rms', 'Reg. Id No.'),
            'stdId' => Yii::t('rms', 'Reg. Id No.'),
            'first_name' => Yii::t('rms', 'Student Name'),
            'cdh_course_relId' => Yii::t('rms', 'Course'),
            'courseTitle' => Yii::t('rms', 'Course Title'),
            'cdh_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'cdh_sem_relId' => Yii::t('rms', 'Semester'),
            'cdh_group_relId' => Yii::t('rms', 'Group'),
            'cdh_clslvl_relId' => Yii::t('rms', 'Class Year'),
            'cdh_approved_by' => Yii::t('rms', 'Approved By'),
            'cdh_status' => Yii::t('rms', 'Drop Status'),
            'cdh_created_at' => Yii::t('rms', 'Created At'),
            'cdh_updated_at' => Yii::t('rms', 'Updated At'),
            'cdh_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function isAdvisor(){
        $inst = Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

        if ($inst) {
            $modelAdvisor = new SemesterAdvisor();
            $gsets = $modelAdvisor->getAdvisingGsets($this->cdh_acadYear_relId,$this->cdh_sem_relId,NULL,NULL,$inst->id);
            if($this->cdh_group_relId){
                return in_array($this->cdhGroupRel->gas_gset_relId,$gsets);
            }
            return in_array($this->cdhStdRel->std_group_realId, $gsets);
        }
    }

    public function restoreBackRegistration(){
        if (!is_null($this->cdh_restore_path)) {
            $srd2 = unserialize(file_get_contents("reports/removed_files/".$this->cdh_restore_path));
            $srd2->save();
            return 1;
        }else{
            throw new \Exception("Can NOT be processed", 1);
        }
    }

    public function dropPossible($studentObj=null){
        $studentObj = $studentObj ? $studentObj :Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

        if ($studentObj) {
            $this->__constructor();

            $gset = $this->currentStudentGroup($studentObj);
            $gset = $gset ? $gset : $studentObj->stdGroupReal;//if not assigned to any group in this semester, take last group in which the student is assigned.

            $coursesEnrolled = CCF::getStudentGroupCoursesP($gset->id,$studentObj->id,$this->semId,$gset->gs_clslvl_relId,$gset->gs_acadYear_relId);
            $creditsCollected = 0;
            $addCrdtSum = 0;
            if ($coursesEnrolled) {
              // Sum up the credits collected in this semsetr.
              foreach ($coursesEnrolled as $key) {
                $creditsCollected += $key->srd2CourseRel->course_crdthr;
              }
            }else{
              $creditsCollected = 0;
            }

            $maxAllowedCedit = 0; //default maxCredit for semester registration is 19.

            $maxAllowedObject = CCF::getMaxAllowedCreditHrPerSemester($gset);

            if ($maxAllowedObject) {
              $this->maxCredit = max($maxAllowedObject->src_max_crdthr,$maxAllowedObject->src_min_crdthr);
              $this->minCredit = min($maxAllowedObject->src_max_crdthr,$maxAllowedObject->src_min_crdthr);
            }

            $this->pendingCredit = 0;

            $approvalPendingRequests = self::find()->where(['cdh_std_relId' => $studentObj->id,'cdh_status' => NULL])->all();
            if ($approvalPendingRequests && count($approvalPendingRequests) > 0) {
                foreach ($approvalPendingRequests as $keyCdh) {
                    $this->pendingCredit += $keyCdh->cdhCourseRel->course_crdthr;
                }
            }

            $this->collectedCredit = $creditsCollected;

            $this->dropableCreditHrs = ($this->collectedCredit-$this->pendingCredit) - $this->minCredit;

            if ( $this->dropableCreditHrs < 0) {
                $this->message = "Below Allowed Credit Hour";
                $this->possible = false;
            }else{
                $this->possible = $this->ScheduleOpen($gset);
            }

            return $this->possible;

        }else{
            $this->message = "Information will be available for student only";
            return false;
        }
    }

    protected function currentStudentGroup($stdObject){
      $this->__constructor();
      $gset = \backend\models\WGroupSetup::find()
        ->where([
          'gs_acadYear_relId'=>$this->acadYearId,
          'gs_sem_relId'=>$this->semId,
          'gs_adm_relId'=>$stdObject->std_admission_relId,
          'gs_prog_relId'=>$stdObject->std_prog_relId,
        ])
        ->innerJoin('w_group_assignment','w_group_setup.id = w_group_assignment.gas_gset_relId')
        ->andWhere(['w_group_assignment.gas_std_relId'=>$stdObject->id])
            ->with('wGroupAssignments')
        ->one();
      return $gset;
    }

    private function ScheduleOpen($gset){
        $actionName = 'Course drop';
        $action = AcademicCalanderAction::findOne(['aca_name'=>$actionName]);
        if ($action) {
            $schedule = CCF::getScheduleForAction($gset,$action->id);
            if (!($schedule && count($schedule))) {
                $this->message = "Schedule is not open for {$actionName}";
                return false;
            }else{
                $this->message = "Schedule is open";
                return true;
            }
        }else{
            $this->message = "Unregistered action {$actionName}";
            return false;
        }
    }


    public function dropApproved($courseId,$stdId,$acadYearId,$semId){
        return $this->find()
            ->where([
                'cdh_course_relId'=>$courseId,
                'cdh_std_relId'=>$stdId,
                'cdh_acadYear_relId'=>$acadYearId,
                'cdh_sem_relId'=>$semId,
            ])
            ->andWhere([
                'cdh_status'=>1,
            ])
            ->one();
    }

    public static function getHodAlertNotification(){
        if (self::$_inst) {
            if (self::$_advisingGsets) {
                
            }else{
                $semesterAdvisorModel = new SemesterAdvisor();
                self::$_advisingGsets = $semesterAdvisorModel->getAdvisingGsetIds(self::$_inst->id);
            }
        }else{
            self::$_inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $semesterAdvisorModel = new SemesterAdvisor();
            self::$_advisingGsets = $semesterAdvisorModel->getAdvisingGsetIds(self::$_inst->id);

        }

        $title = "Course Drop Request";
        $link = "/course-drop-history/index";
        $count = self::find()
            ->innerJoin('student','student.id = course_drop_history.cdh_std_relId')
            ->innerJoin('course','course.id = course_drop_history.cdh_course_relId')
            ->where([
                'cdh_status'=>NULL,
            ])
            ->andWhere(['OR',
                ['course.course_dept_relId'=>self::$_inst->inst_dep_relId,],
                ['student.std_group_realId'=>self::$_advisingGsets,]
            ])
            ->count();
        $ex = \yii\helpers\ArrayHelper::keyExists($title,\Yii::$app->params['notifications'],false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title'=>$title,
                'count'=>$count,
                'link'=>$link
            ];
        }
    }
    

    public static function getInstAlertNotification(){
        if (self::$_inst) {
            if (self::$_advisingGsets) {
                
            }else{
                $semesterAdvisorModel = new SemesterAdvisor();
                self::$_advisingGsets = $semesterAdvisorModel->getAdvisingGsetIds(self::$_inst->id);
            }
        }else{
            self::$_inst = Instructor::findOne(['inst_user_relId' => \Yii::$app->user->identity->id]);
            $semesterAdvisorModel = new SemesterAdvisor();
            self::$_advisingGsets = $semesterAdvisorModel->getAdvisingGsetIds(self::$_inst->id);

        }

        $title = "Course Drop Request";
        $link = "/course-drop-history/index";
        $count = self::find()
            ->innerJoin('student','student.id = course_drop_history.cdh_std_relId')
            ->where([
                'cdh_status'=>NULL,
            ])
            ->andWhere(['student.std_group_realId'=>self::$_advisingGsets])
            ->count();
        $ex = \yii\helpers\ArrayHelper::keyExists($title,\Yii::$app->params['notifications'],false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title'=>$title,
                'count'=>$count,
                'link'=>$link
            ];
        }
    }

    public function getBatchRequests($semesterAdvisorObj){
        return $this->find()
            ->innerJoin('student','student.id = course_drop_history.cdh_std_relId')
            ->where([
                'cdh_status'=>NULL,
                'cdh_acadYear_relId'=>$semesterAdvisorObj->sa_acadYear_relId,
                'cdh_sem_relId'=>$semesterAdvisorObj->sa_sem_relId,
                'cdh_clslvl_relId' => $semesterAdvisorObj->sa_clslvl_relId,
            ])
            ->andWhere([
                'student.std_admission_relid'=>$semesterAdvisorObj->sa_adm_relId,
                'student.std_prog_relid'=>$semesterAdvisorObj->sa_prog_relId,
                'student.std_dept_relid'=>$semesterAdvisorObj->sa_dept_relId,
            ])
            ->all();
    }

    public function getDroppedCourses($semregObj){
        return $this->find()
            ->where([
                    'cdh_acadYear_relId' => $semregObj->semreg_acadYear_relId,
                    'cdh_sem_relId' => $semregObj->semreg_semester_relId,
                    'cdh_std_relId' => $semregObj->semreg_std_relId
                ])
            ->all();
    }

    public static function getStudentlert(){

        $title = "Course Drop Request";
        $link = "/course-drop-history/index";
        $count = self::find()->innerJoin('student','student.id=course_drop_history.cdh_std_relId')
            ->andWhere(['cdh_status' => NULL,'student.std_user_relId' => \Yii::$app->user->identity->id])->count();
        
        $ex = \yii\helpers\ArrayHelper::keyExists($title,\Yii::$app->params['notifications'],false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title'=>$title,
                'count'=>$count,
                'link'=>$link
            ];
        }
    }

    public static function getAlertNotification(){

        $title = "Course Drop Request";
        $link = "/course-drop-history/index";
        $count = self::find()->andWhere(['cdh_status' => NULL])->count();

        $ex = \yii\helpers\ArrayHelper::keyExists($title,\Yii::$app->params['notifications'],false);
        if (!$ex || Yii::$app->params['notifications'][$title]['count'] != $count && $count > 0) {
            Yii::$app->params['notifications']['count'] += $ex ? ($count - Yii::$app->params['notifications'][$title]['count']) : $count;
            Yii::$app->params['notifications'][$title] = [
                'title'=>$title,
                'count'=>$count,
                'link'=>$link
            ];
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'cdh_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'cdh_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'cdh_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'cdh_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhGroupRel()
    {
        return $this->hasOne(WGroupAssignment::class, ['id' => 'cdh_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'cdh_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cdh_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCdhSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'cdh_srd2_relId']);
    }
}
