<?php

namespace backend\models;

use Yii;
use \frontend\models\SemesterRegistration2;

/**
 * This is the model class for table "school_year".
 *
 * @property integer $id
 * @property string $sch_year_desc
 * @property string $sch_year_status
 * @property integer $sch_created_by
 * @property string $sch_created_at
 * @property integer $sch_updated_by
 * @property string $sch_updated_at
 *
 * @property AcademicCalander[] $academicCalanders
 * @property CourseOffering[] $courseOfferings
 * @property User $schCreatedBy
 * @property User $schUpdatedBy
 * @property SemesterRegistration[] $semesterRegistrations
 * @property Student[] $students
 */
class SchoolYear extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'school_year';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sch_year_desc', 'sch_year_status', 'sch_created_at'], 'required'],
            [['sch_created_by', 'sch_updated_by','sch_bach_number'], 'integer'],
            [['sch_created_at', 'sch_updated_at'], 'safe'],
            [['sch_year_desc'], 'string', 'max' => 10],
            [['sch_year_desc'], 'unique'],
            [['sch_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sch_created_by' => 'id']],
            [['sch_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['sch_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sch_year_desc' => Yii::t('rms', 'School Year'),
            'sch_year_status' => Yii::t('rms', 'Status'),
            'sch_bach_number' => Yii::t('rms', 'Batch'),
            'sch_created_by' => Yii::t('rms', 'Created By'),
            'sch_created_at' => Yii::t('rms', 'Created At'),
            'sch_updated_by' => Yii::t('rms', 'Updated By'),
            'sch_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function updateBatch($id,$batchNum){
        return Yii::$app->db->createCommand()
            ->update('school_year',
                ['sch_bach_number'=>$batchNum],
                ['id'=>$id])
            ->execute();
    }

    public function alertBatchNumber($acadYearId){
        $batch = $this->findOne($acadYearId);
        if (is_null($batch->sch_bach_number)) {
            return true;
        }
    }

    public function studentAdmitted($acadYearId){
        return \frontend\models\SemesterRegistration2::findOne(['semreg_acadYear_relId'=>$acadYearId]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicCalanders()
    {
        return $this->hasMany(AcademicCalander::className(), ['acad_acadYear_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering::className(), ['coff_acadYear_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'sch_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSchUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'sch_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return $this->hasMany(SemesterRegistration2::className(), ['semreg_acadYear_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudents()
    {
        return $this->hasMany(Student::className(), ['std_acadYear_relId' => 'id']);
    }
}
