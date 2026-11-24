<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "graduation_eligiblity_master".
 *
 * @property integer $id
 * @property integer $ge_adm_relId
 * @property integer $ge_prog_relId
 * @property integer $ge_num_of_F
 * @property integer $ge_min_crdthr
 * @property integer $ge_course_work
 * @property integer $ge_research_work
 * @property integer $ge_no_of_course
 * @property integer $ge_no_of_seminar
 * @property integer $ge_minimum_duration
 * @property integer $ge_maximum_duration
 * @property double $ge_min_CGPA
 * @property string $ge_created_at
 * @property integer $ge_created_by
 * @property string $ge_updated_at
 * @property integer $ge_updated_by
 *
 * @property Admission $geAdmRel
 * @property Program $geProgRel
 * @property User $geUpdatedBy
 * @property User $geCreatedBy
 */
class GraduationEligiblityMaster extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'graduation_eligiblity_master';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ge_adm_relId', 'ge_prog_relId', 'ge_num_of_F', 'ge_min_crdthr', 'ge_course_work', 'ge_research_work', 'ge_no_of_course', 'ge_no_of_seminar', 'ge_minimum_duration', 'ge_maximum_duration', 'ge_created_by', 'ge_updated_by'], 'integer'],
            [['ge_min_CGPA'], 'number'],
            [['ge_created_at', 'ge_created_by'], 'required'],
            [['ge_created_at', 'ge_updated_at'], 'safe'],
            [['ge_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Admission::className(), 'targetAttribute' => ['ge_adm_relId' => 'id']],
            [['ge_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Program::className(), 'targetAttribute' => ['ge_prog_relId' => 'id']],
            [['ge_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::className(), 'targetAttribute' => ['ge_updated_by' => 'id']],
            [['ge_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\User::className(), 'targetAttribute' => ['ge_created_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ge_adm_relId' => Yii::t('rms', 'Admission'),
            'ge_prog_relId' => Yii::t('rms', 'Program'),
            'ge_num_of_F' => Yii::t('rms', 'Number Of F'),
            'ge_min_crdthr' => Yii::t('rms', 'Minimum Creditshr'),
            'ge_course_work' => Yii::t('rms', 'Though Course Work'),
            'ge_research_work' => Yii::t('rms', 'Though Research Work'),
            'ge_no_of_course' => Yii::t('rms', 'Minimum No Of Course'),
            'ge_no_of_seminar' => Yii::t('rms', 'Seminar or Lab Course'),
            'ge_minimum_duration' => Yii::t('rms', 'Minimum Duration'),
            'ge_maximum_duration' => Yii::t('rms', 'Maximum Duration'),
            'ge_min_CGPA' => Yii::t('rms', 'Min CGPA'),
            'ge_created_at' => Yii::t('rms', 'Ge Created At'),
            'ge_created_by' => Yii::t('rms', 'Ge Created By'),
            'ge_updated_at' => Yii::t('rms', 'Ge Updated At'),
            'ge_updated_by' => Yii::t('rms', 'Ge Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeAdmRel()
    {
        return $this->hasOne(Admission::className(), ['id' => 'ge_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeProgRel()
    {
        return $this->hasOne(Program::className(), ['id' => 'ge_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ge_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGeCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ge_created_by']);
    }
}
