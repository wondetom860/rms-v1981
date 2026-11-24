<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;
/**
 * This is the model class for table "cont_assess_catagory2".
 *
 * @property integer $id
 * @property integer $cat_coff_relId
 * @property integer $cat_acadYear_relId
 * @property integer $cat_sem_relId
 * @property integer $cat_catagory_relId
 * @property double $cat_maxPoint
 * @property string $cat_created_at
 * @property integer $cat_created_by
 * @property integer $cat_updated_by
 * @property string $cat_updated_at
 *
 * @property CatagoryName2 $catCatagoryRel
 * @property CourseOffering2Detail $catCoffRel
 * @property User $catCreatedBy
 * @property User $catUpdatedBy
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property InstanceMarkAssignment[] $instanceMarkAssignments
 */
class ContAssessCatagory2 extends \yii\db\ActiveRecord
{

    public $courseId;
    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cont_assess_catagory2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_coff_relId', 'cat_catagory_relId', 'cat_maxPoint', 'cat_created_at', 'cat_created_by'], 'required'],
            [['cat_coff_relId', 'cat_catagory_relId', 'cat_created_by', 'cat_updated_by'], 'integer'],
            [['cat_maxPoint'], 'number'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
            [['cat_catagory_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CatagoryName2::class, 'targetAttribute' => ['cat_catagory_relId' => 'id']],
            [['cat_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['cat_coff_relId' => 'id']],

            [['cat_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['cat_acadYear_relId' => 'id']],

            [['cat_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['cat_sem_relId' => 'id']],

            [['cat_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['cat_course_relId' => 'id']],
            [['cat_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cat_created_by' => 'id']],
            [['cat_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cat_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cat_coff_relId' => Yii::t('rms', 'Course Offering ID'),
            'cat_catagory_relId' => Yii::t('rms', 'Asess. Catagory'),
            'cat_maxPoint' => Yii::t('rms', 'Max Point'),
            'cat_course_relId' => Yii::t('rms', 'Course Title'),
            'cat_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'cat_sem_relId' => Yii::t('rms', 'Semester'),
            'cat_created_at' => Yii::t('rms', 'Created At'),
            'cat_created_by' => Yii::t('rms', 'Created By'),
            'cat_updated_by' => Yii::t('rms', 'Updated By'),
            'cat_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function getDeptCourses(){
        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one();
            // $deptCourses = Course::find()->where(['course_dept_relId'=>$instDept->inst_dep_relId])->all();
            
            // initiate current year and semester.
            $this->__constructor();
            // get course assigned for the instructor.
            $coffs = CourseOffering2Detail::find()
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->innerJoin('course','course_offering2_detail.coff2det_course_relId = course.id')
                ->innerJoin('course_catalog','course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
                ->where(['OR',
                    ['course.course_dept_relId'=>$instDept->inst_dep_relId],
                    ['coff2det_inst_relId'=> $instDept->id],
                ])
                ->andWhere(['cocat_courseProject'=>0])
                ->andWhere(['coff2_acadYear_relId'=>$this->acadYearId,'coff2_sem_relId'=> $this->semId])
                ->groupBy('coff2det_course_relId')
                ->all();
            $cofArray = [];
            if ($coffs && count($coffs) > 0) {
                foreach ($coffs as $key) {
                    $course22 = Course::findOne($key->coff2det_course_relId);
                    $cofArray[] = [
                        'id'=>$key->coff2det_course_relId,
                        'course_title'=>$key->coff2detCourseRel->course_title,
                        'coffdetId'=>$key->id,
                    ];
                }
            }
            return $cofArray;
        }else if(Yii::$app->user->can('Instructor')){
            $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

            // initiate current year and semester.
            $this->__constructor();
            // get course assigned for the instructor.
            $coffs = CourseOffering2Detail::find()
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andWhere([
                    'coff2det_inst_relId'=> $inst->id,
                    'coff2_acadYear_relId'=>$this->acadYearId,
                    'coff2_sem_relId'=> $this->semId
                ])
                ->groupBy('coff2det_course_relId')
                ->all();
            $cofArray = [];
            if ($coffs && count($coffs) > 0) {
                foreach ($coffs as $key) {
                    $course22 = Course::findOne($key->coff2det_course_relId);
                    $cofArray[] = [
                        'id'=>$course22->id,
                        'course_title'=>$course22->course_title,
                        'coffdetId'=>$key->id,
                    ];
                }
            }
            return $cofArray;
        }else{
            return [];
        }
    }

    public function getInstanceName($catName){
        $CatagoryName = CatagoryName2::find()->innerJoin('cont_assess_catagory2','cont_assess_catagory2.cat_catagory_relId = catagory_name2.id')->andWhere(['catagory_name2.catagory_name'=>$catName])->One();
        return $CatagoryName.'_'.$this->find()->count();
    }

    // check if the course is ofered for the instructor,
    private function checkIfOffered($courseId){
        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
            $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

            // initiate current year and semester.
            $this->__constructor();
            // get course assigned for the instructor.
            $coffs = CourseOffering2Detail::find()
                ->where(['coff2det_inst_relId'=> $inst->id])
                ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                ->andWhere(['coff2_acadYear_relId'=>$this->acadYearId,'coff2_sem_relId'=> $this->semId])
                ->andWhere(['coff2det_course_relId'=>$courseId])
                ->count();
            return $coffs > 0;
        }
        return NULL;
    }

    // Get any catagory made for this course
    public function getCourseCatagor($courseId,$coffId){
        $this->__constructor();
        if (Yii::$app->user->can('DepartmentHead')) {

            if ($this->checkIfOffered($courseId)) {
                return $this->find()
                ->where([
                        'cat_course_relId'=>$courseId,
                        'cat_acadYear_relId'=>$this->acadYearId,
                        'cat_sem_relId'=>$this->semId,
                        'cat_created_by'=>Yii::$app->user->identity->id])
                ->all();
            }
            return $this->find()->innerJoin('catagory_name2','cont_assess_catagory2.cat_catagory_relId = catagory_name2.id')
            ->where([
                'cat_course_relId'=>$courseId,
                'cat_acadYear_relId'=>$this->acadYearId,
                'cat_sem_relId'=>$this->semId,])
            ->all();
        }else if(Yii::$app->user->can('Instructor')){
            return $this->find()
                ->where([
                    'cat_course_relId'=>$courseId,
                    'cat_acadYear_relId'=>$this->acadYearId,
                    'cat_sem_relId'=>$this->semId,
                ])
                ->all();
        }
    }

    // get course assess catagory configuration
    public function getCourseCatagory($courseId,$coff2detId = null,$semregObj = null){
        // var_dump($courseId,$coff2detId,$semregObj);exit;
        if ($coff2detId) {
            $coff2det = CourseOffering2Detail::findOne($coff2detId);
            return $this->find()
            ->where([
                'cat_course_relId'=>$courseId,
                'cat_acadYear_relId'=>$coff2det->coff2detCoff2Rel->coff2_acadYear_relId,
                'cat_sem_relId'=>$coff2det->coff2detCoff2Rel->coff2_sem_relId,
            ])
            ->orderBy('cat_maxPoint')
            ->all();
        }
        if (!$semregObj) {
            $this->__constructor(); 
        }else{ 
            $this->acadYearId = $semregObj->semreg_acadYear_relId; 
            $this->semId = $semregObj->semreg_semester_relId;
        }
        return $this->find()
            ->where([
                'cat_course_relId'=>$courseId,
                'cat_acadYear_relId'=>$this->acadYearId,
                'cat_sem_relId'=>$this->semId,
            ])
            ->orderBy('cat_maxPoint')
            ->all();
            // : $this->find()
            // ->where([
            //     'cat_course_relId'=>$courseId,
            //     'cat_acadYear_relId'=>$this->acadYearId,
            //     'cat_sem_relId'=>$this->semId,
            // ])
            // ->orderBy('cat_maxPoint')
            // ->all();
    }

    // Get remaining poit
    public function remainingPoint($courseId,$coffId,$max = NULL){
        $this->__constructor();
        $tot = $this->find()
            ->where([
                'cat_course_relId'=>$courseId,
                'cat_acadYear_relId'=>$this->acadYearId,
                'cat_sem_relId'=>$this->semId,
            ])
            ->sum('cat_maxPoint');
        if(is_null($max)){
            return 100 - $tot;
        }else{
            return $max - $tot;
        }
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCatagoryRel()
    {
        return $this->hasOne(CatagoryName2::class, ['id' => 'cat_catagory_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'cat_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'cat_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cat_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cat_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'cat_acadYear_relId']);
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'cat_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment2::class, ['ca_catagorySetup_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstanceMarkAssignments()
    {
        return $this->hasMany(InstanceMarkAssignment::class, ['ima_catagorySetup_relId' => 'id'])->orderBy('ima_possiblePoint');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReassessmentHistories()
    {
        return $this->hasMany(ReassessmentHistory::class, ['ras_contAssCatag_relId' => 'id']);
    }
}
