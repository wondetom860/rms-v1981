<?php

namespace frontend\modules\CourseAssessment\models;

use backend\models\AddHistory;
use backend\models\Attendance;
use Yii;
use backend\models\CourseOffering2;
use backend\models\User;
// use \backend\models\ContAssessCatagory;
use backend\models\Course;
use backend\models\CourseCatalog;
use backend\models\Instructor;

use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "course_offering2_detail".
 *
 * @property int $id
 * @property int $coff2det_coff2_relId
 * @property int $coff2det_cocat_relId
 * @property int $coff2det_course_relId
 * @property int|null $coff2det_inst_relId
 * @property int|null $coff2det_TA_relId
 * @property int|null $coff2det_registrarApproved
 * @property string|null $coff2det_registrarApproved_at
 * @property int|null $coff2det_registrarApproved_by
 * @property int $coff2det_created_by
 * @property string $coff2det_created_at
 * @property string $coff2det_updated_at
 * @property int $coff2det_updated_by
 *
 * @property AddHistory[] $addHistories
 * @property Attendance[] $attendances
 * @property ContAssessCatagory2[] $contAssessCatagories
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property Instructor $coff2detTARel
 * @property CourseCatalog $coff2detCocatRel
 * @property CourseOffering2 $coff2detCoff2Rel
 * @property Course $coff2detCourseRel
 * @property User $coff2detCreatedBy
 * @property User $coff2detUpdatedBy
 * @property RemovedCourse[] $removedCourses
 */
class CourseOffering2Detail extends \yii\db\ActiveRecord
{

    public $dept = null;
    public $dept2 = null;
    public $admission = NULL;
    public $program = NULL;
    public $department = NULL;
    public $clslvl = NULL;
    public $spec = NULL;

    public $acadYear = NULL;
    public $sem = NULL;

    public $scheduled = 0;


    public $coff2det_dept_relId = NULL;
    public $courseTitle = NULL;
    public $coff2det_clslvl_relId = NULL;

    public $acadYearId;
    public $semId;

    public $inst;
    public $ta;

    public $HoDDept;
    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_offering2_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['coff2det_coff2_relId', 'coff2det_cocat_relId', 'coff2det_course_relId', 'coff2det_created_by', 'coff2det_created_at', 'coff2det_updated_at', 'coff2det_updated_by'], 'required'],
            [['coff2det_coff2_relId', 'coff2det_cocat_relId', 'coff2det_course_relId', 'coff2det_inst_relId', 'coff2det_TA_relId', 'coff2det_registrarApproved', 'coff2det_registrarApproved_by', 'coff2det_created_by', 'coff2det_updated_by'], 'integer'],
            [['coff2det_registrarApproved_at', 'coff2det_created_at', 'coff2det_updated_at'], 'safe'],
            [['coff2det_TA_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['coff2det_TA_relId' => 'id']],
            [['coff2det_cocat_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseCatalog::class, 'targetAttribute' => ['coff2det_cocat_relId' => 'id']],
            [['coff2det_coff2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2::class, 'targetAttribute' => ['coff2det_coff2_relId' => 'id']],
            [['coff2det_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['coff2det_course_relId' => 'id']],
            [['coff2det_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff2det_created_by' => 'id']],
            [['coff2det_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['coff2det_updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'coff2det_coff2_relId' => Yii::t('rms', 'Coff2det Coff2 Rel ID'),
            'coff2det_cocat_relId' => Yii::t('rms', 'Coff2det Cocat Rel ID'),
            'coff2det_course_relId' => Yii::t('rms', 'Coff2det Course Rel ID'),
            'coff2det_inst_relId' => Yii::t('rms', 'Coff2det Inst Rel ID'),
            'coff2det_TA_relId' => Yii::t('rms', 'Coff2det Ta Rel ID'),
            'coff2det_registrarApproved' => Yii::t('rms', 'Coff2det Registrar Approved'),
            'coff2det_registrarApproved_at' => Yii::t('rms', 'Coff2det Registrar Approved At'),
            'coff2det_registrarApproved_by' => Yii::t('rms', 'Coff2det Registrar Approved By'),
            'coff2det_created_by' => Yii::t('rms', 'Coff2det Created By'),
            'coff2det_created_at' => Yii::t('rms', 'Coff2det Created At'),
            'coff2det_updated_at' => Yii::t('rms', 'Coff2det Updated At'),
            'coff2det_updated_by' => Yii::t('rms', 'Coff2det Updated By'),
        ];
    }
    

    public function getCatMaxPointSum(){
        if($this->coff2detCourseRel->course_type != 1){
            return 100;
        }
        $sum = ContAssessCatagory::find()
            ->where([
                'cat_acadYear_relId' => $this->coff2detCoff2Rel->coff2_acadYear_relId,
                'cat_sem_relId' => $this->coff2detCoff2Rel->coff2_sem_relId,
                'cat_course_relId' => $this->coff2det_course_relId,
            ])
            ->sum('cat_maxPoint');
        return $sum;
        // return $this->getContAssessCatagories()->andWhere(['cat_course_relId' => $this->coff2det_course_relId])->sum('cat_maxPoint');
    }

    public function checkRequired2($courseId)
    {
        $this->__constructor();
        // course Offering
        $courses = $this->find()
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where([
                'course.id' => $courseId,
            ])
            ->andWhere([
                'course_offering2.coff2_acadYear_relId' => $this->coff2detCoff2Rel->coff2_acadYear_relId,
                'course_offering2.coff2_sem_relId' => $this->coff2detCoff2Rel->coff2_sem_relId,
            ])
            ->groupBy('course_offering2_detail.coff2det_course_relId')
            ->all();

        $coffdets = [];
        if ($courses && count($courses) > 0) {
            foreach ($courses as $key) {
                $counts = $this->find()
                    ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    ->where([
                        'coff2det_course_relId' => $key->coff2det_course_relId,
                    ])
                    ->andWhere([
                        'course_offering2.coff2_acadYear_relId' => $this->coff2detCoff2Rel->coff2_acadYear_relId,
                        'course_offering2.coff2_sem_relId' => $this->coff2detCoff2Rel->coff2_sem_relId,
                    ])
                    ->groupBy('course_offering2_detail.coff2det_inst_relId')
                    ->count();
                if ($counts > 1) {
                    // this course has multiple insts.
                    $coffdets[] = $key;
                }
            }
        }

        return $coffdets;
    }

    public function checkRequired($courseId)
    {
        $this->__constructor();
        // course Offering
        $courses = $this->find()
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
            ->where([
                'course.id' => $courseId,
            ])
            ->andWhere([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
            ])
            ->groupBy('course_offering2_detail.coff2det_course_relId')
            ->all();

        $coffdets = [];
        if ($courses && count($courses) > 0) {
            foreach ($courses as $key) {
                $counts = $this->find()
                    ->innerJoin('course_offering2', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    ->where([
                        'coff2det_course_relId' => $key->coff2det_course_relId,
                    ])
                    ->andWhere([
                        'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                        'course_offering2.coff2_sem_relId' => $this->semId,
                    ])
                    ->groupBy('course_offering2_detail.coff2det_inst_relId')
                    ->count();
                if ($counts > 1) {
                    // this course has multiple insts.
                    $coffdets[] = $key;
                }
            }
        }

        return $coffdets;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddHistories()
    {
        return $this->hasMany(AddHistory::class, ['ah_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendances()
    {
        return $this->hasMany(Attendance::class, ['a_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessCatagories()
    {
        return $this->hasMany(ContAssessCatagory::class, ['cat_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment::class, ['ca_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detTARel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'coff2det_TA_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCocatRel()
    {
        return $this->hasOne(CourseCatalog::class, ['id' => 'coff2det_cocat_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCoff2Rel()
    {
        return $this->hasOne(CourseOffering2::class, ['id' => 'coff2det_coff2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCourseRel()
    {
        return $this->hasOne(\backend\models\Course::class, ['id' => 'coff2det_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff2det_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'coff2det_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRemovedCourses()
    {
        return $this->hasMany(\backend\models\RemovedCourse::class, ['rc_coff_relId' => 'id']);
    }
}
