<?php

namespace frontend\modules\TRANSTUD\models;

use backend\models\ClassLevel;
use Yii;

use common\models\User;
use backend\models\Student;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;

/**
 * This is the model class for table "transfer_student".
 *
 * @property int $id
 * @property int $std_relId
 * @property string $transfered_from
 * @property string $year_admitted
 * @property int $year_transfered link to school year id
 * @property int $class_level_joined link to class Year id
 * @property float $sgpa
 * @property float $cgpa
 * @property string|null $exempted_course_ids
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property int|null $approved_by
 * @property int|null $approved
 * @property int|null $approved_at
 * @property int|null $deleted_by
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property User $deletedBy
 * @property Student $stdRel
 * @property User $updatedBy
 * @property AcAcademicYear $yearTransfered
 * @property ExemptedCourse $exemptedCourses
 */
class TransferStudent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'transfer_student';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['std_relId', 'transfered_from', 'year_transfered', 'created_by', 'data','class_level_joined'], 'required'],
            [['std_relId', 'year_transfered', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by','class_level_joined','approved_by','approved_at','approved'], 'integer'],
            [['sgpa', 'cgpa'], 'number'],
            [['exempted_course_ids', 'data'], 'string'],
            [['transfered_from', 'year_admitted'], 'string', 'max' => 255],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],//
            [['year_transfered'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['year_transfered' => 'id']],
            [['class_level_joined'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['class_level_joined' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'std_relId' => Yii::t('rms', 'Reg. ID'),
            'transfered_from' => Yii::t('rms', 'Transfered From'),
            'year_admitted' => Yii::t('rms', 'Year Admitted'),
            'year_transfered' => Yii::t('rms', 'Year Transfered'),
            'class_level_joined' => Yii::t('rms', 'Class Level Joined'),
            'sgpa' => Yii::t('rms', 'SGPA'),
            'cgpa' => Yii::t('rms', 'CGPA'),
            'exempted_course_ids' => Yii::t('rms', 'Exempted Course'),
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
            'std_relId' => $this->std_relId,
            'transfered_from' => $this->transfered_from,
            'year_admitted' => $this->year_admitted,
            'year_transfered' => $this->year_transfered,
            'class_level_joined' => $this->class_level_joined,
            'sgpa' => $this->sgpa,
            'cgpa' => $this->cgpa,
            'exempted_course_ids' => $this->exempted_course_ids,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
        ]));
    }

    public function approveTsRecord($option){
        $transaction = Yii::$app->db->beginTransaction();
        $error = 0;
        $option = $option == 1 ? 1 : NULL;
        try {
            $this->approved_by = Yii::$app->user->id;
            $this->approved_at = time();
            $this->approved = $option;
            $this->getData();
            if($this->save(false)){
                $exemptedCourses = $this->exemptedCourses;
                if($exemptedCourses){
                    foreach ($exemptedCourses as $exemptedCourse) {
                        $exemptedCourse->approved = $this->approved;
                        $exemptedCourse->getData();
                        if(!$exemptedCourse->save(false)){
                            // Yii::error("Error saving exempted course: " . json_encode($exemptedCourse->getErrors()), __METHOD__);
                            // var_dump($exemptedCourse->getErrors());exit;
                            $error = 1;
                            break;
                        }
                    }
                    if($error == 0){
                        $transaction->commit();
                        return true;
                    }
                }
            }
        }
        catch (\Exception $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());exit;
            Yii::error("Error approving transfer student record: " . $e->getMessage(), __METHOD__);
            return false;
        }
    }

    public static function getExemptedCourseIds($stdId)
    {
        $exemptedCourses = ExemptedCourse::find()
            ->innerJoinWith('tsRel')
            ->where([
                'std_relId' => $stdId,
                'approved' => 1,
            ])
            ->all();
        if ($exemptedCourses) {
            $courseIds = [];
            foreach ($exemptedCourses as $exemptedCourse) {
                $courseIds[] = json_decode($exemptedCourse->equivalent_course_ids, true);
            }
            return json_encode($courseIds);
        }
        return null;
    }

    public function courseEexmpted($courseId)
    {
        $exemptedCourses = $this->exemptedCourses;
        if ($exemptedCourses) {
            foreach ($exemptedCourses as $exemptedCourse) {
                if (in_array($courseId, json_decode($exemptedCourse->equivalent_course_ids, true))) {
                    return true;
                }
            }
        }
        return false;
    }

    public function getTransferStudents()
    {
        $stds = Student::find()->where(['entry_type' => 2])->all();

        $stdModel = [];
        // var_dump($stds);exit;
        if ($stds) {
            foreach ($stds as $std) {
                if ($this->checkIfExists($std->id)) {
                    continue;
                } else {
                    $stdModel[] = [
                        'id' => $std->id,
                        'full_name' => "{$std->std_regId} : {$std->getFullName()}",
                    ];
                }
            }
        }
        // var_dump($stdModel);exit;
        return $stdModel;
    }
    public function checkIfExists($stdId, $id = null)
    {
        return $id ?  TransferStudent::find()
            ->where([
                'std_relId' => $stdId
            ])
            ->andWhere(' id <>  ' . $id)
            ->one() :
            TransferStudent::find()
            ->where([
                'std_relId' => $stdId
            ])
            ->one();
    }

    public function getTsHeader()
    {
        $std = $this->stdRel;
        return "{$std->std_regId} : {$std->getFullName()}";
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
     * Gets query for [[ClassLevelJoined]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getClassLevelJoined(){//classLevelJoined{
        return $this->hasOne(ClassLevel::class, ['id' => 'class_level_joined']);
    }
    // class_level_joined

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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[YearTransfered]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getYearTransfered()//yearTransfered
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'year_transfered']);
    }

    /**
     * Gets query for [[ExemptedCourses]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExemptedCourses()
    {
        return $this->hasMany(ExemptedCourse::class, ['ts_relid' => 'id']);
    }
}
