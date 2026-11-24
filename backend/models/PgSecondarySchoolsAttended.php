<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pg_secondary_schools_attended".
 *
 * @property integer $id
 * @property integer $pssa_pei_relId
 * @property string $pgssa_name_of_school
 * @property string $pgssa_location
 * @property string $pgssa_years_from
 * @property string $pgssa_years_to
 *
 * @property PgEducationInfo $pssaPeiRel
 */
class PgSecondarySchoolsAttended extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pg_secondary_schools_attended';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pssa_pei_relId'], 'integer'],
            [['pgssa_name_of_school', 'pgssa_location', 'pgssa_years_from', 'pgssa_years_to'], 'required'],
            [['pgssa_name_of_school', 'pgssa_location'], 'string'],
            [['pgssa_years_from', 'pgssa_years_to'], 'safe'],
            [['pssa_pei_relId'], 'exist', 'skipOnError' => true, 'targetClass' => PgEducationInfo::className(), 'targetAttribute' => ['pssa_pei_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pssa_pei_relId' => Yii::t('rms', 'Education Inforamtion ID'),
            'pgssa_name_of_school' => Yii::t('rms', 'Name Of School'),
            'pgssa_location' => Yii::t('rms', 'Location'),
            'pgssa_years_from' => Yii::t('rms', 'Years - From'),
            'pgssa_years_to' => Yii::t('rms', 'Years - To'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPssaPeiRel()
    {
        return $this->hasOne(PgEducationInfo::className(), ['id' => 'pssa_pei_relId']);
    }
}
