<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pg_education_info".
 *
 * @property integer $id
 * @property string $pgei_UG_university_name
 * @property double $pgei_CGPA
 * @property string $pgei_field_of_study
 * @property string $pgei_major
 * @property string $pgei_minor
 * @property string $pgei_work_experience
 * @property string $pgei_reserches
 * @property string $pgei_awards
 *
 * @property PgPostSecondarySchool[] $pgPostSecondarySchools
 * @property PgSecondarySchoolsAttended[] $pgSecondarySchoolsAttendeds
 */
class PgEducationInfo extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pg_education_info';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pgei_UG_university_name', 'pgei_CGPA', 'pgei_field_of_study', 'pgei_work_experience', 'pgei_reserches', 'pgei_awards'], 'required'],
            [['pgei_UG_university_name', 'pgei_field_of_study', 'pgei_major', 'pgei_minor', 'pgei_work_experience', 'pgei_reserches', 'pgei_awards'], 'string'],
            [['pgei_CGPA'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pgei_UG_university_name' => Yii::t('rms', 'Undegraduate University : University Name'),
            'pgei_CGPA' => Yii::t('rms', 'CGPA'),
            'pgei_field_of_study' => Yii::t('rms', 'Field Of Study'),
            'pgei_major' => Yii::t('rms', 'Major'),
            'pgei_minor' => Yii::t('rms', 'Minor'),
            'pgei_work_experience' => Yii::t('rms', 'WORK EXPERIENCE'),
            'pgei_reserches' => Yii::t('rms', 'RESEARCH'),
            'pgei_awards' => Yii::t('rms', 'AWARDS'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgPostSecondarySchools()
    {
        return $this->hasMany(PgPostSecondarySchool::className(), ['ppss_pei_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPgSecondarySchoolsAttendeds()
    {
        return $this->hasMany(PgSecondarySchoolsAttended::className(), ['pssa_pei_relId' => 'id']);
    }
}
