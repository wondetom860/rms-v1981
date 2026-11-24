<?php

namespace frontend\modules\GC\models;

use backend\models\SemesterRegistration2;
use backend\models\Student;
use Yii;
use common\models\User;
// SemesterRegistration2Detail

/**
 * This is the model class for table "gc_student".
 *
 * @property int $id
 * @property int $entry_relId
 * @property int|null $graduation_mode 1:normal,2:delayed
 * @property int $total_credit_hr
 * @property int $course_count
 * @property float $total_gps
 * @property int $std_relId
 * @property float $cgpa
 * @property float|null $sgpa
 * @property float|null $major_gpa
 * @property int|null $last_semreg_relId
 * @property int|null $reg_approved
 * @property int|null $reg_approved_at
 * @property int|null $reg_approved_by
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
 * @property GcEntry $entryRel
 * @property SemesterRegistration2Detail $lastSrd2Rel
 * @property User $regApprovedBy
 * @property User $updatedBy
 */
class GcStudent extends \yii\db\ActiveRecord

{
    public $_graduation_modes = [
        1 => 'Normal',
        2 => 'Delayed'
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gc_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entry_relId', 'total_credit_hr', 'course_count', 'total_gps', 'cgpa', 'created_by', 'data', 'std_relId'], 'required'],
            [['entry_relId', 'graduation_mode', 'total_credit_hr', 'course_count', 'last_semreg_relId', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by', 'std_relId'], 'integer'],
            [['total_gps', 'cgpa', 'sgpa', 'major_gpa'], 'number'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['entry_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GcEntry::class, 'targetAttribute' => ['entry_relId' => 'id']],
            [['last_semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['last_semreg_relId' => 'id']],
            [['reg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reg_approved_by' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
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
            'entry_relId' => Yii::t('rms', 'GC Entry'),
            'std_relId' => Yii::t('rms', 'Student'),
            'graduation_mode' => Yii::t('rms', 'Graduation Mode'),
            'total_credit_hr' => Yii::t('rms', 'Total Credit Hr'),
            'course_count' => Yii::t('rms', 'Course Count'),
            'total_gps' => Yii::t('rms', 'Total Gps'),
            'cgpa' => Yii::t('rms', 'CGPA'),
            'sgpa' => Yii::t('rms', 'SGPA'),
            'major_gpa' => Yii::t('rms', 'Major GPA'),
            'last_semreg_relId' => Yii::t('rms', 'Last Registration'),
            'reg_approved' => Yii::t('rms', 'Reg Approved'),
            'reg_approved_at' => Yii::t('rms', 'Reg Approved At'),
            'reg_approved_by' => Yii::t('rms', 'Reg Approved By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'deleted_at' => Yii::t('rms', 'Deleted At'),
            'deleted_by' => Yii::t('rms', 'Deleted By'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function approve($status)
    {
        if (Yii::$app->user->can('Registrar')) {
            $msg = '';
            $transaction = Yii::$app->db->beginTransaction();
            $this->reg_approved = $status;
            $this->reg_approved_at = time();
            $this->reg_approved_by = Yii::$app->user->identity->id;
            $this->getData();

            if ($this->save()) {
                $student = $this->stdRel;
                $student->std_semester_status = ($status == 1) ? 'GRADUATED' : 'ACTIVE';
                if ($student->save(false)) {
                    $transaction->commit();
                    return 1;
                } else {
                    $msg = json_encode($student->errors);
                }
            }
            $msg .= ": 2) " . json_encode($this->errors);
            $transaction->rollBack();
            return $msg;
        }
    }

    public function getData()
    {
        // Get the current date and time
        $this->data = md5(serialize([
            'entry_relId' => $this->entry_relId,
            'std_relId' => $this->std_relId,
            'graduation_mode' => $this->graduation_mode,
            'total_credit_hr' => $this->total_credit_hr,
            'course_count' => $this->course_count,
            'total_gps' => $this->total_gps,
            'cgpa' => $this->cgpa,
            'sgpa' => $this->sgpa,
            'last_semreg_relId' => $this->last_semreg_relId,
            'reg_approved' => $this->reg_approved,
            'major_gpa' => $this->major_gpa,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]));
    }

    public static function alreadyImported($stdId)
    {
        return self::find()->where(['std_relId' => $stdId])->count() > 0;
    }

    public function getGraduationMode()
    {
        return $this->_graduation_modes[$this->graduation_mode];
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
     * Gets query for [[EntryRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntryRel()
    {
        return $this->hasOne(GcEntry::class, ['id' => 'entry_relId']);
    }


    /**
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
    }


    /**
     * Gets query for [[LastSemregRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLastSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'last_semreg_relId']);
    }

    /**
     * Gets query for [[RegApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'reg_approved_by']);
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
