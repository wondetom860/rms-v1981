<?php

namespace frontend\modules\TRANSTUD\models;

use backend\models\SemesterRegistration2Detail;
use backend\models\WGroupAssignment;
use Yii;
use common\models\User;

/**
 * This is the model class for table "exempted_course".
 *
 * @property int $id
 * @property int $ts_relid
 * @property string $course_code
 * @property string $course_name
 * @property int $course_hours
 * @property string $grade_obtained
 * @property float $grade_point
 * @property float $grade_point_sum
 * @property string|null $equivalent_course_ids
 * @property int $approved 0: not approved, 1: approved
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property int|null $deleted_by
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property User $deletedBy
 * @property User $updatedBy
 */
class ExemptedCourse extends \yii\db\ActiveRecord

{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'exempted_course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ts_relid', 'course_code', 'course_name', 'course_hours', 'grade_obtained', 'created_by', 'data'], 'required'],
            [['ts_relid', 'course_hours', 'approved', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['grade_point', 'grade_point_sum'], 'number'],
            [['equivalent_course_ids', 'data'], 'string'],
            [['course_code'], 'string', 'max' => 20],
            [['course_name'], 'string', 'max' => 255],
            [['grade_obtained'], 'string', 'max' => 3],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['ts_relid'], 'exist', 'skipOnError' => true, 'targetClass' => TransferStudent::class, 'targetAttribute' => ['ts_relid' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ts_relid' => Yii::t('rms', 'Tranfer Info.'),
            'course_code' => Yii::t('rms', 'Course Code'),
            'course_name' => Yii::t('rms', 'Course Name'),
            'course_hours' => Yii::t('rms', 'Course Hours'),
            'grade_obtained' => Yii::t('rms', 'Grade Obtained'),
            'grade_point' => Yii::t('rms', 'Grade Scale'),
            'grade_point_sum' => Yii::t('rms', 'Grade Point Sum'),
            'equivalent_course_ids' => Yii::t('rms', 'Equivalent Course Ids'),
            'approved' => Yii::t('rms', 'Approved'),
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
            'ts_relid' => $this->ts_relid,
            'course_code' => $this->course_code,
            'course_name' => $this->course_name,
            'course_hours' => $this->course_hours,
            'grade_obtained' => $this->grade_obtained,
            'grade_point' => $this->grade_point,
            'equivalent_course_ids' => $this->equivalent_course_ids,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]));
    }

    public function getCoursesBySection()
    {
        $std = $this->tsRel->stdRel;
        $topScore = NULL;
        $courses = [];
        $gAssinments = WGroupAssignment::find()
            ->innerJoinWith('gasGsetRel')
            ->andWhere([
                'gs_dept_relId' => $std->std_dept_relId,
                'gs_prog_relId' => $std->std_prog_relId,
                'gs_clslvl_relId' => $this->tsRel->class_level_joined,
            ])
            ->all();
        foreach ($gAssinments as $gAssinment) {
            $sstd = $gAssinment->gasStdRel;
            $lastSemreg = $sstd->getLastSemReg();
            if (null === $topScore) {
                $topScore = $lastSemreg;
            } else {
                $topScore = ($lastSemreg->semreg_CGPA > $topScore->semreg_CGPA) ? $lastSemreg : $topScore;
            }
        }

        if ($topScore) {
            $srd2s = SemesterRegistration2Detail::find()
                ->innerJoinWith('srd2SemregRel')
                ->where(['semreg_std_relId' => $topScore->semreg_std_relId])
                ->all();
            foreach ($srd2s as $srd2) {
                $courses[] = $srd2->srd2CourseRel;
            }
        }
        return $courses;
    }

    public function checkIfExists($id = null)
    {
        return $id ?  self::find()
            ->where([
                'ts_relid' => $this->ts_relid,
                'course_code' => $this->course_code,
                'course_name' => $this->course_name
            ])
            ->andWhere(' id <>  ' . $id)
            ->one()
            :
            self::find()
            ->where([
                'ts_relid' => $this->ts_relid,
                'course_code' => $this->course_code,
                'course_name' => $this->course_name
            ])
            ->one();
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
     * Gets query for [[TsRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTsRel()
    {
        return $this->hasOne(TransferStudent::class, ['id' => 'ts_relid']);
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
}
