<?php

namespace backend\models;

use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use Yii;
use \frontend\modules\AcademicCalendar\models\AcActivePrograms;

/**
 * This is the model class for table "course_offering_temp".
 *
 * @property integer $id
 * @property integer $cot_acadYear_relId
 * @property integer $cot_sem_relId
 * @property integer $cot_adm_relId
 * @property integer $cot_prog_relId
 * @property integer $cot_dept_relId
 * @property integer $cot_acProg_relId
 * @property integer $cot_clslvel_relId
 * @property integer $cot_spec_relId
 * @property string $cot_cocat_IDS
 * @property string $cot_created_at
 * @property integer $cot_created_by
 * @property string $cot_updated_at
 * @property integer $cot_updated_by
 * @property integer  $cot_locked
 * @property integer $cot_acProg_relId
 * @property integer  $cot_registrarApproved
 * @property integer  $cot_registrarApproved_at
 * @property integer  $cot_registrarApproved_by
 *
 * @property SchoolYear $cotAcadYearRel
 * @property Admission $cotAdmRel
 * @property ClassLevel $cotClslvelRel
 * @property User $cotCreatedBy
 * @property User $cotUpdatedBy
 * @property Department $cotDeptRel
 * @property Program $cotProgRel
 * @property Semester $cotSemRel//
 * @property Specialization $cotSpecRel
 * @property AcActivePrograms $cotAcProgRel
 */
class CourseOfferingTemp extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_offering_temp';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cot_acadYear_relId', 'cot_sem_relId', 'cot_adm_relId', 'cot_prog_relId', 'cot_dept_relId', 'cot_clslvel_relId', 'cot_cocat_IDS', 'cot_created_at', 'cot_created_by'], 'required'],
            [['cot_acadYear_relId', 'cot_sem_relId', 'cot_adm_relId', 'cot_prog_relId', 'cot_dept_relId', 'cot_clslvel_relId', 'cot_spec_relId', 'cot_created_by', 'cot_updated_by', 'cot_acProg_relId'], 'integer'],
            [['cot_cocat_IDS'], 'string'],
            [['cot_created_at', 'cot_updated_at'], 'safe'],
            [['cot_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['cot_acadYear_relId' => 'id']],
            [['cot_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['cot_adm_relId' => 'id']],
            [['cot_acProg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::class, 'targetAttribute' => ['cot_acProg_relId' => 'id']],
            [['cot_clslvel_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['cot_clslvel_relId' => 'id']],
            [['cot_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cot_created_by' => 'id']],
            [['cot_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cot_updated_by' => 'id']],
            [['cot_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['cot_dept_relId' => 'id']],
            [['cot_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['cot_prog_relId' => 'id']],
            [['cot_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['cot_sem_relId' => 'id']],
            [['cot_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['cot_spec_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cot_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'cot_sem_relId' => Yii::t('rms', 'Semester'),
            'cot_adm_relId' => Yii::t('rms', 'Admission'),
            'cot_prog_relId' => Yii::t('rms', 'Program'),
            'cot_dept_relId' => Yii::t('rms', 'Department'),
            'cot_clslvel_relId' => Yii::t('rms', 'Class Year'),
            'cot_spec_relId' => Yii::t('rms', 'Specialization'),
            'cot_cocat_IDS' => Yii::t('rms', 'Courses'),
            'cot_created_at' => Yii::t('rms', 'Created At'),
            'cot_created_by' => Yii::t('rms', 'Created By'),
            'cot_updated_at' => Yii::t('rms', 'Updated At'),
            'cot_updated_by' => Yii::t('rms', 'Updated By'),
            'cot_acProg_relId' => Yii::t('rms', 'Acad. Program'),
        ];
    }

    public function linkAcProg()
    {
        $acp = AcActivePrograms::find()
            ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
            ->where([
                'school_year' => $this->cotAcadYearRel->sch_year_desc,
                'semester' => $this->cotSemRel->semester_name,
                'admission_relId' => $this->cot_adm_relId,
                'program_relId' => $this->cot_prog_relId,
                'dept_relId' => $this->cot_dept_relId,
                'class_level_relId' => $this->cot_clslvel_relId,
            ]);
        if ($this->cot_spec_relId) {
            $acp = $acp->andWhere(['spec_relId' => $this->cot_spec_relId]);
        }
        if ($acp->count()  == 1) {
            $acp = $acp->all()[0];
            $this->cot_acProg_relId = $acp->id;
            // $this->getData();
            if ($this->update()) {
                return true;
            } else {
                return json_encode($this->errors);
            }
        } else if ($acp->count() > 1) {
            return "More than one active program found";
        } else {
            return false;
        }
    }
    public static function updateAcProgLink($acProgModel)
    {
        // var_dump($acProgModel);exit;
        $temp = self::find()
            ->where([
                'cot_acadYear_relId' => $acProgModel->academicYearRel->schoolYearRel->id,
                'cot_sem_relId' => $acProgModel->academicYearRel->semesterRel->id,
                'cot_adm_relId' => $acProgModel->admission_relId,
                'cot_prog_relId' => $acProgModel->program_relId,
                'cot_dept_relId' => $acProgModel->dept_relId,
                'cot_clslvel_relId' => $acProgModel->class_level_relId,
            ]);
        if ($acProgModel->spec_relId) {
            $temp = $temp->andWhere(['cot_spec_relId' => $acProgModel->spec_relId]);
        }
        $temp = $temp->all();
        $cc =  count(array($temp));
        if ($temp) {
            $temp = $temp[0];
            if ($temp->cot_acProg_relId != $acProgModel->id) {
                $temp->cot_acProg_relId = $acProgModel->id;
                if ($temp->save(false)) {
                    return true;
                }
            } else {
                return true;
            }
        } else if ($cc > 1) {
            return "More than one offering found";
        } else {
            return false;
        }
    }

    public function createAcp()
    {
        $exc = $this->checkIfAcpExists();
        if ($exc) {
            var_dump($exc);
        } else {
            $acAcademicYear = AcAcademicYear::find()
                ->where([
                    'school_year' => $this->cotAcadYearRel->sch_year_desc,
                    'semester' => $this->cotSemRel->semester_name
                ])
                ->one();
            if ($acAcademicYear) {
                $model = new AcActivePrograms();
                $model->academic_year_relId = $acAcademicYear->id;
                $model->admission_relId = $this->cot_adm_relId;
                $model->program_relId = $this->cot_prog_relId;
                $model->dept_relId = $this->cot_dept_relId;
                $model->class_level_relId = $this->cot_clslvel_relId;
                $model->semester_relId = $this->cot_sem_relId;
                $model->spec_relId = $this->cot_spec_relId;

                if ($model->checkIfExists()) {
                    $acpModel = $model->findAcpByCotemp($this, $acAcademicYear);
                    if ($acpModel) {
                        return count($acpModel);
                    }
                } else {
                    $model->created_at = time();
                    $model->created_by = Yii::$app->user->id;
                    $model->getData();
                    if ($model->save()) {
                        $this->cot_acProg_relId = $model->id;
                        if ($this->update()) {
                            return 1;
                        } else {
                            return json_encode($this->errors);
                        }
                    } else {
                        return json_encode($model->errors);
                    }
                }
            } else {
                return "Academic Year Not Scheduled.";
            }
        }
    }

    private function checkIfAcpExists()
    {
        return AcActivePrograms::find()
            ->innerJoin('ac_academic_year', 'ac_academic_year.id=ac_active_programs.academic_year_relId')
            ->where([
                'ac_academic_year.school_year' => $this->cotAcadYearRel->sch_year_desc,
                'ac_academic_year.semester' => $this->cotSemRel->semester_name,
                'admission_relId' => $this->cot_adm_relId,
                'program_relId' => $this->cot_prog_relId,
                'dept_relId' => $this->cot_dept_relId,
                'class_level_relid' => $this->cot_clslvel_relId,
                'spec_relId' => $this->cot_spec_relId
            ])
            ->count();
    }

    public function checkIfOffered($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId, $forOffering = NULL)
    {
        if (!is_null($forOffering)) { //before distributing courses to groups, it should have to be approved or NOT_REJECTED
            return $this->find()
                ->where([
                    'cot_acadYear_relId' => $acadYearId,
                    'cot_sem_relId' => $semId,
                    'cot_adm_relId' => $admId,
                    'cot_prog_relId' => $progId,
                    'cot_dept_relId' => $deptId,
                ])
                ->andWhere(['OR', ['cot_clslvel_relId' => $clslvlId,], ['cot_clslvel_relId' => NULL]])
                ->andWhere(['OR', ['cot_spec_relId' => $specId], ['cot_spec_relId' => NULL]])
                ->andWhere(['OR', ['IS NOT', 'cot_registrarApproved', NULL], ['<>', 'cot_registrarApproved', 0]])
                ->andWhere([
                    'IS NOT',
                    'cot_cocat_IDS',
                    NULL
                ])
                ->count();
        }
        return $this->find()
            ->where([
                'cot_acadYear_relId' => $acadYearId,
                'cot_sem_relId' => $semId,
                'cot_adm_relId' => $admId,
                'cot_prog_relId' => $progId,
                'cot_dept_relId' => $deptId,
            ])
            ->andWhere(['OR', ['cot_clslvel_relId' => $clslvlId,], ['cot_clslvel_relId' => NULL]]) //why this condition?
            ->andWhere(['OR', ['cot_spec_relId' => $specId], ['cot_spec_relId' => NULL]])
            ->andWhere([
                'IS NOT',
                'cot_cocat_IDS',
                NULL
            ])
            ->count();
        // 
    }

    public function getOfferedCourses($acadYearId, $semId, $admId, $progId, $deptId, $clslvlId, $specId, $forOffering = NULL)
    {
        $courses = NULL;
        if (!is_null($forOffering)) { //if for distributing, registrar must approve the offering prior.
            $courses = $this->find()
                ->where([
                    'cot_acadYear_relId' => $acadYearId,
                    'cot_sem_relId' => $semId,
                    'cot_adm_relId' => $admId,
                    'cot_prog_relId' => $progId,
                    'cot_dept_relId' => $deptId,
                ])
                ->andWhere(['OR', ['cot_clslvel_relId' => $clslvlId,], ['cot_clslvel_relId' => NULL]])
                ->andWhere(['OR', ['IS NOT', 'cot_registrarApproved', NULL], ['<>', 'cot_registrarApproved', 0]])
                ->andWhere(['OR', ['cot_spec_relId' => $specId], ['cot_spec_relId' => NULL]])
                ->andWhere([
                    'IS NOT',
                    'cot_cocat_IDS',
                    NULL
                ])
                ->one();
        } else {
            $courses = $this->find()
                ->where([
                    'cot_acadYear_relId' => $acadYearId,
                    'cot_sem_relId' => $semId,
                    'cot_adm_relId' => $admId,
                    'cot_prog_relId' => $progId,
                    'cot_dept_relId' => $deptId,
                ])
                ->andWhere(['OR', ['cot_clslvel_relId' => $clslvlId,], ['cot_clslvel_relId' => NULL]])
                ->andWhere(['OR', ['cot_spec_relId' => $specId], ['cot_spec_relId' => NULL]])
                ->andWhere([
                    'IS NOT',
                    'cot_cocat_IDS',
                    NULL
                ])
                ->one();
        }
        if ($courses) {
            // die(1);
            return [json_decode($courses->cot_cocat_IDS), $courses->id, $courses->cot_locked];
        }
        return NULL;
    }

    public function getUnApprovedOfferings()
    {
        return $this->find()
            ->where([
                'cot_registrarApproved' => NULL,
            ])
            ->all();
    }

    public function getRegistrarApproveRequest()
    {
        return $this->find()
            ->where([
                'cot_registrarApproved' => NULL,
            ])
            ->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'cot_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'cot_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotClslvelRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'cot_clslvel_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotAcProgRel()
    {
        return $this->hasOne(AcActivePrograms::class, ['id' => 'cot_acProg_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cot_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cot_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'cot_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'cot_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'cot_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCotSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'cot_spec_relId']);
    }
}
