<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pg_post_secondary_school".
 *
 * @property integer $id
 * @property integer $ppss_pei_relId
 * @property string $pgpss_institute_name
 * @property string $pgpss_location
 * @property string $pgpss_deploma_awarded
 * @property string $pgpss_date_of_award
 *
 * @property PgEducationInfo $ppssPeiRel
 */
class PgPostSecondarySchool extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $modelPSE_attended = 1;
    public static function tableName()
    {
        return 'pg_post_secondary_school';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ppss_pei_relId'], 'integer'],
            [['pgpss_institute_name', 'pgpss_location', 'pgpss_deploma_awarded', 'pgpss_date_of_award'], 'required'],
            [['pgpss_institute_name', 'pgpss_location', 'pgpss_deploma_awarded'], 'string'],
            [['pgpss_date_of_award'], 'safe'],
            [['ppss_pei_relId'], 'exist', 'skipOnError' => true, 'targetClass' => PgEducationInfo::className(), 'targetAttribute' => ['ppss_pei_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ppss_pei_relId' => Yii::t('rms', 'Ppss Pei Rel ID'),
            'pgpss_institute_name' => Yii::t('rms', 'Pgpss Institute Name'),
            'pgpss_location' => Yii::t('rms', 'Pgpss Location'),
            'pgpss_deploma_awarded' => Yii::t('rms', 'Pgpss Deploma Awarded'),
            'pgpss_date_of_award' => Yii::t('rms', 'Pgpss Date Of Award'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPpssPeiRel()
    {
        return $this->hasOne(PgEducationInfo::className(), ['id' => 'ppss_pei_relId']);
    }
}
