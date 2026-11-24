<?php

namespace frontend\modules\AcademicCalendar\models;

use Yii;
use common\models\AuthOrg8n;
use backend\models\User;
use backend\models\SchoolYear;
use backend\models\Semester;

/**
 * This is the model class for table "ac_academic_year".
 *
 * @property int $id
 * @property int $college_relId
 * @property string $school_year
 * @property string $semester
 * @property string $season
 * @property int $start_date
 * @property int $end_date
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string|null $remark
 * @property string $data
 *
 * @property AuthOrg8n $collegeRel
 * @property User $createdBy
 * @property User $updatedBy
 * @property AcActivePrograms[] $acActivePrograms
 */
class AcAcademicYear extends \yii\db\ActiveRecord
{
    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 0;
    public const STATUS_UPNEXT = -1;

    public $statuses = [0 => 'Closed', 1 => 'Active', -1 => 'Up Next'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ac_academic_year';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['college_relId', 'school_year', 'semester', 'season', 'start_date', 'end_date', 'created_by', 'created_at', 'data'], 'required'],
            [['college_relId', 'created_by', 'created_at', 'updated_by', 'updated_at', 'status'], 'integer'],
            [['season', 'remark', 'data', 'start_date', 'end_date',], 'string'],
            [['school_year', 'semester'], 'string', 'max' => 7],
            [['college_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AuthOrg8n::class, 'targetAttribute' => ['college_relId' => 'id']],
            [['school_year'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['school_year' => 'sch_year_desc']],
            [['semester'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['semester' => 'semester_name']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'college_relId' => Yii::t('rms', 'College Name'),
            'school_year' => Yii::t('rms', 'School Year'),
            'semester' => Yii::t('rms', 'Semester'),
            'season' => Yii::t('rms', 'Season'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'remark' => Yii::t('rms', 'Remark'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getGraduateStudentList()
    {
        $school_year = $this->school_year;
        $semester = $this->semester;

        $sql = "
            SELECT
                student.id as ID,
                student.std_regId as REGID,
                concat(first_name,' ',middle_name,' ',last_name) as FULL_NAME,
                department.dept_code as DEPARTMENT,
                gender as GENDER,
                user.user_type as PROFESSION,
                program.program_name as PROGRAM,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'A+' THEN 1 END) AS GR_A_PLUS,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'A' THEN 1 END) AS GR_A,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'A-' THEN 1 END) AS GR_A_MINUS,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'B+' THEN 1 END) AS GR_B_PLUS,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'B' THEN 1 END) AS GR_B,
                COUNT(CASE WHEN grade_scale.gs_ltr_grade = 'B-' THEN 1 END) AS GR_B_MINUS,
                count(distinct(srd2_course_relId)) as TOTAL_NUMBER_OF_COURSES_ENROLLED,
                sum(course_crdthr) as TOTAL_CREDIT_HOUR,
                max(semreg_gradePoint_sum) as GPSUM,
                format((max(semreg_gradePoint_sum)/sum(CASE WHEN srd2_corptd_flag = 0 THEN course_crdthr END)),3) as CGPA,
                max(clslvl_num) as CLSLVL
            FROM
                semester_registration_2
                INNER JOIN semester_registration_2_detail ON semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId
                INNER JOIN class_level ON class_level.id=semester_registration_2.semreg_clslvl_relId
                INNER JOIN student ON student.id=semester_registration_2.semreg_std_relId
                INNER JOIN user ON user.id=student.std_user_relId
                INNER JOIN grade_scale ON grade_scale.id=semester_registration_2_detail.srd2_grade_relId
                INNER JOIN department ON department.id=student.std_dept_relId
                INNER JOIN course on course.id=semester_registration_2_detail.srd2_course_relId
                INNER JOIN w_group_setup ON w_group_setup.id=student.std_group_realId
                INNER JOIN ac_active_programs ON ac_active_programs.id=w_group_setup.gs_active_prog_relId
                INNER JOIN program ON program.id=student.std_prog_relId
                INNER JOIN ac_academic_year ON ac_academic_year.id=ac_active_programs.academic_year_relId
            WHERE
                semester_registration_2.semreg_last_ssn_relId IS NOT NULL AND
                ac_active_programs.graduate_class=1 AND
                ac_academic_year.school_year= :acy AND
                ac_academic_year.semester= :sem
            GROUP BY
                semreg_std_relId
            ORDER BY 
                PROGRAM DESC,
                CGPA DESC,
                GR_A_PLUS DESC,
                GR_A DESC,
                GR_A_MINUS DESC,
                GR_B_PLUS DESC,
                GR_B_MINUS DESC,
                GR_B DESC
        ";
        return Yii::$app->db->createCommand($sql)->bindParam(':acy', $school_year)->bindParam(':sem', $semester)->queryAll();
    }

    public static function getRecentAcademciYear()
    { //returns academic year entry just before $this.
        // ->where(' id <> '.$this->id)
        $acys = self::find()
            ->orderBy([
                'school_year' => SORT_ASC,
                'semester' => SORT_ASC,
            ])
            ->all();
        if ($acys) {
            return $acys[count($acys) - 1];
        }
        return NULL;
    }
    public function getImmidiatePrevAcademicYear()
    { //returns academic year entry just before $this.
        // ->where(' id <> '.$this->id)
        $acys = self::find()
            ->orderBy([
                'school_year' => SORT_ASC,
                'semester' => SORT_ASC,
            ])
            ->all();
        if ($acys && count($acys) > 0) {
            for ($i = 1; $i < count($acys); $i++) {
                if ($acys[$i]->school_year == $this->school_year && $acys[$i]->semester == $this->semester) {
                    return $acys[$i - 1];
                }
            }

            return "Previos academic year entry not found.";
        }

        return NULL;
    }

    public function getCollegeAddress()
    {
        return $this->collegeRel->getCollegeAddress();
    }

    public function getActiveAcademicYear($withLink = 1)
    {
        $activeAcademicYears = self::find()->where(['status' => self::STATUS_ACTIVE])->all();
        if (is_null($withLink)) {
            // caller function wants to further use it.
            return $activeAcademicYears;
        }
        if ($activeAcademicYears && count($activeAcademicYears) > 0) {
            $acYears = '';
            $type = 'success';
            foreach ($activeAcademicYears as $acYear) {
                if (Yii::$app->user->can('Admin')) {
                    $link = "<a class='btn btn-sm btn-info' href='/AcademicCalendar/ac-academic-year/view?id={$acYear->id}'>" . $acYear->getAcHeader() . "</a>&nbsp;&nbsp;";
                } else {
                    $link = "<span>" . $acYear->getAcHeader() . "</span>&nbsp;&nbsp;";
                }
                $acYears .= $link;
            }
        } else {
            $type = 'danger';
            $acYears = "Academic Calendar Uknown";
        }

        return ['type' => $type, 'message' => $acYears];
    }

    public function getAcHeader()
    {
        // 
        return $this->school_year . "-" . $this->semester . " ({$this->season})";
    }

    public function getSchedule()
    {
        return date('d M, Y', $this->start_date) . " - " . date('d M, Y', $this->end_date);
    }

    public function getStatus()
    {
        $st = "";
        $s = 1;
        if (time() > $this->end_date) {
            $st = "Closed";
            $s = 0;
        } else if (time() < $this->start_date) {
            $st = "Up Next";
            $s = -1;
        } else {
            $st = "Active";
        }

        if ($this->status != $s) {
            $this->status = $s;
            $this->update(false);
        }

        return $st;
    }

    public function checIfExists($id = null)
    {
        return $id ?
            self::find()->where([
                'college_relId' => $this->college_relId,
                'school_year' => $this->school_year,
                'semester' => $this->semester,
                'season' => $this->season
            ])->andWhere(" id <> $id ")->count() > 0
            :
            self::find()->where([
                'college_relId' => $this->college_relId,
                'school_year' => $this->school_year,
                'semester' => $this->semester,
                'season' => $this->season
            ])->count() > 0;
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'school_year' => $this->school_year,
            'season' => $this->season,
            'start_date' => intval($this->start_date),
            'end_date' => intval($this->end_date),
            'college_relId' => intval($this->college_relId),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCollegeRel()
    {
        return $this->hasOne(AuthOrg8n::class, ['id' => 'college_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchoolYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['sch_year_desc' => 'school_year']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRel()
    {
        return $this->hasOne(Semester::class, ['semester_name' => 'semester']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcActivePrograms()
    {
        return $this->hasMany(AcActivePrograms::class, ['academic_year_relId' => 'id']);
    }
}
