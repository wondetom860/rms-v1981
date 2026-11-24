<?php

namespace frontend\modules\GC\models;

use Yii;
use common\models\User;
use backend\models\Admission;
use backend\models\Program;
use backend\models\Department;
use backend\models\Specialization;
use backend\models\Student;
use backend\models\WGroupAssignment;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;

/**
 * This is the model class for table "gc_entry".
 *
 * @property int $id
 * @property int $admission_relId
 * @property int $program_relId
 * @property int $dept_relId
 * @property int|null $spec_relId
 * @property int $acad_year_relId
 * @property int $senate_approve_date
 * @property string|null $header_title
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property int|null $deleted_by
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AcAcademicYear $acadYearRel
 * @property Admission $admissionRel
 * @property User $createdBy
 * @property User $deletedBy
 * @property Department $deptRel
 * @property Program $programRel
 * @property Specialization $specRel
 * @property User $updatedBy
 * @property GcStudent[] $gcStudents
 */
class GcEntry extends \yii\db\ActiveRecord
{
    public $senate_approve_date_str;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gc_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['admission_relId', 'program_relId', 'dept_relId', 'acad_year_relId', 'senate_approve_date', 'created_by', 'data'], 'required'],
            [['admission_relId', 'program_relId', 'dept_relId', 'spec_relId', 'acad_year_relId', 'senate_approve_date', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['data','senate_approve_date_str'], 'string'],
            [['header_title'], 'string', 'max' => 255],
            [['acad_year_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['acad_year_relId' => 'id']],
            [['admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['admission_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dept_relId' => 'id']],
            [['program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['program_relId' => 'id']],
            [['spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['spec_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'admission_relId' => Yii::t('rms', 'Admission'),
            'program_relId' => Yii::t('rms', 'Program'),
            'dept_relId' => Yii::t('rms', 'Dept.'),
            'spec_relId' => Yii::t('rms', 'Spec.'),
            'acad_year_relId' => Yii::t('rms', 'Ac. Year'),
            'senate_approve_date' => Yii::t('rms', 'Senate Approve Date'),
            'senate_approve_date_str' => Yii::t('rms', 'Senate Approve Date'),
            'header_title' => Yii::t('rms', 'Header Title'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'deleted_at' => Yii::t('rms', 'Deleted At'),
            'deleted_by' => Yii::t('rms', 'Deleted By'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        // Get the current date and time
        $this->data = md5(serialize([
            'admission_relId' => $this->admission_relId,
            'program_relId' => $this->program_relId,
            'dept_relId' => $this->dept_relId,
            'spec_relId' => $this->spec_relId,
            'acad_year_relId' => $this->acad_year_relId,
            'senate_approve_date' => $this->senate_approve_date,
            'header_title' => $this->header_title,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]));
    }

    public function createGcStudents($gc_list)
    {
        $gc_stds = json_decode($gc_list, true);
        if ($gc_stds) {
            foreach ($gc_stds as $index => $stdId) {
                if (!$this->stdImported($stdId)) {
                    $student = Student::findOne($stdId);
                    $lastSemreg = $student->getLastSemreg();
                    $gps = $student->calculateTotalGPS();

                    if (!is_array($gps)) {
                        continue;
                    }

                    $model = new GcStudent();
                    $model->std_relId = $stdId;
                    $model->entry_relId = $this->id;
                    $model->cgpa = $lastSemreg->semreg_CGPA;
                    $model->sgpa = $lastSemreg->semreg_SGPA;
                    $model->major_gpa = $student->calculateMGPA();
                    $model->graduation_mode = 1;
                    $model->last_semreg_relId = $lastSemreg->id;
                    $model->total_credit_hr = $gps[1];
                    $model->total_gps = $gps[2];
                    $model->course_count = $gps[0];

                    $model->created_at = time();
                    $model->created_by = Yii::$app->user->identity->id;

                    $model->getData();

                    if ($model->save()) {
                        $gps = null;
                        // $student->std_semester_status = 'GRADUATED';
                        // $student->save(false);
                    } else {
                        return json_encode($model->errors);
                    }
                }
            }
            return 1;
        } else {
            return "GC list empty";
        }
    }

    private function stdImported($stdId)
    {
        return GcStudent::find()
            ->where(['std_relId' => $stdId])
            ->count() > 0;
    }

    public function getStudents()
    {
        $acp =  AcActivePrograms::find()
            ->where([
                'graduate_class' => 1,
                'admission_relId' => $this->admission_relId,
                'program_relId' => $this->program_relId,
                'dept_relId' => $this->dept_relId,
                'spec_relId' => $this->spec_relId,
                'academic_year_relId' => $this->acad_year_relId,
            ])
            ->one();
        if ($acp) {
            return WGroupAssignment::find()
                ->innerJoinWith('gasGsetRel')
                ->andWhere([
                    'gs_active_prog_relId' => $acp->id
                ])
                ->all();
        } else {
            return NULL;
        }
    }
    /**
     * Gets query for [[AcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'acad_year_relId']);
    }

    /**
     * Gets query for [[AdmissionRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAdmissionRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'admission_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[DeptRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'dept_relId']);
    }

    /**
     * Gets query for [[ProgramRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProgramRel()
    {
        return $this->hasOne(Program::class, ['id' => 'program_relId']);
    }

    /**
     * Gets query for [[SpecRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'spec_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[GcStudents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGcStudents()
    {
        return $this->hasMany(GcStudent::class, ['entry_relId' => 'id']);
    }
}
