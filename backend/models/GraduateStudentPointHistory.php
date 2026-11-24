<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduate_student_point_history".
 *
 * @property int $id
 * @property int $gph_gstd_relId
 * @property int $gph_registration_entry_relId
 * @property float $gph_SGPA
 * @property float $gph_CGPA
 * @property int $gph_credit_hour_enrolled
 * @property float $gph_average_point
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property string $data
 *
 * @property User $approvedBy
 * @property User $createdBy
 * @property GraduateStudent $gphGstdRel
 * @property User $updatedBy
 * @property GraduateStudentRegistrationEntryHistory $gphRegistrationEntryRel
 */




/*
    ALTER TABLE `graduate_student_point_history` CHANGE `gph_credit_hour_enrolled` `gph_credit_hour_enrolled` INT(2) NULL, CHANGE `gph_average_point` `gph_average_point` FLOAT(7,3) NULL;
*/

 

class GraduateStudentPointHistory extends \yii\db\ActiveRecord
{
    public $acadYear;
    public $semester;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'graduate_student_point_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gph_gstd_relId', 'gph_registration_entry_relId', 'gph_SGPA', 'gph_CGPA', 'created_at', 'created_by', 'data'], 'required'],
            [['gph_gstd_relId', 'gph_registration_entry_relId', 'gph_credit_hour_enrolled', 'created_by', 'updated_by', 'approved_by'], 'integer'],
            [['gph_SGPA', 'gph_CGPA', 'gph_average_point'], 'number'],
            [['created_at', 'updated_at', 'approved_at'], 'safe'],
            [['data'], 'string'],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approved_by' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['gph_gstd_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GraduateStudent::class, 'targetAttribute' => ['gph_gstd_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['gph_registration_entry_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GraduateStudentRegistrationEntryHistory::class, 'targetAttribute' => ['gph_registration_entry_relId' => 'id']],
            // [['acadYear'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['acadYear' => 'id']],
            // [['semester'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['semester' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gph_gstd_relId' => Yii::t('rms', 'Student Full Name'),
            // 'gph_registration_entry_relId' => Yii::t('rms', 'Registration Entry'),
            'acadYear' => Yii::t('rms', 'Academic Year'),
            'semester' => Yii::t('rms', 'Semester'),
            'gph_SGPA' => Yii::t('rms', 'SGPA'),
            'gph_CGPA' => Yii::t('rms', 'CGPA'),
            'gph_credit_hour_enrolled' => Yii::t('rms', 'Credit Hour Enrolled'),
            'gph_average_point' => Yii::t('rms', 'Average Point'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'approved_at' => Yii::t('rms', 'Approved At'),
            'approved_by' => Yii::t('rms', 'Approved By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'acadYear'=>$modelObj->acadYear,
            'semester'=>$modelObj->semester,
            'gph_gstd_relId'=>intval($modelObj->gph_gstd_relId),
            'gph_SGPA'=>floatval($modelObj->gph_SGPA),
            'gph_CGPA'=>floatval($modelObj->gph_CGPA),
            'gph_average_point'=>floatval($modelObj->gph_average_point),
            'gph_credit_hour_enrolled'=>intval($modelObj->gph_credit_hour_enrolled),
            'approved_by'=>intval($modelObj->approved_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
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
    public function getGphGstdRel()
    {
        return $this->hasOne(GraduateStudent::class, ['id' => 'gph_gstd_relId']);
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
    public function getGphRegistrationEntryRel()
    {
        return $this->hasOne(GraduateStudentRegistrationEntryHistory::class, ['id' => 'gph_registration_entry_relId']);
    }
}
