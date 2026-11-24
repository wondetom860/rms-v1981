<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "post_secondary_education".
 *
 * @property integer $id
 * @property integer $pse_ei_relId
 * @property string $pse_institute_name
 * @property integer $pse_nationality_relId
 * @property string $pse_from_year
 * @property integer $pse_to_year
 * @property double $pse_credit_earned
 * @property double $pse_CGPA_obtained
 * @property string $pse_certificate_earned
 * @property integer $pse_created_by
 * @property integer $pse_updated_by
 * @property string $pse_created_at
 * @property string $pse_updated_at
 *
 * @property User $pseCreatedBy
 * @property Nationality $pseNationalityRel
 * @property User $pseUpdatedBy
 * @property EducationInformation $pseEiRel
 */
class PostSecondaryEducation extends \yii\db\ActiveRecord
{

    public $attendedPSE;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'post_secondary_education';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pse_institute_name', 'pse_nationality_relId', 'pse_from_year', 'pse_to_year', 'pse_credit_earned', 'pse_CGPA_obtained',], 'required'],
            [['pse_CGPA_obtained'], 'double' ,'max'=>4.00],
            [['pse_CGPA_obtained'], 'double' ,'min'=>0.00],
            [['attendedPSE'],'integer'],
            ['attendedPSE','default', 'value'=>0],
            [['pse_nationality_relId', 'pse_to_year',], 'integer'],
            [['pse_institute_name', 'pse_certificate_earned'], 'string'],
            [['pse_from_year', 'pse_created_at', 'pse_updated_at'], 'safe'],
            [['pse_credit_earned', 'pse_CGPA_obtained'], 'number'],
            [['pse_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pse_created_by' => 'id']],
            [['pse_nationality_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Nationality::className(), 'targetAttribute' => ['pse_nationality_relId' => 'id']],
            [['pse_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pse_updated_by' => 'id']],
            [['pse_ei_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EducationInformation::className(), 'targetAttribute' => ['pse_ei_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pse_ei_relId' => Yii::t('rms', 'Education'),
            'pse_institute_name' => Yii::t('rms', 'Institute Name'),
            'pse_nationality_relId' => Yii::t('rms', 'Nationality'),
            'pse_from_year' => Yii::t('rms', 'From Year'),
            'pse_to_year' => Yii::t('rms', 'To'),
            'pse_credit_earned' => Yii::t('rms', 'Credit Earned'),
            'pse_CGPA_obtained' => Yii::t('rms', 'Cgpa'),
            'pse_certificate_earned' => Yii::t('rms', 'Certificate Earned'),
            'pse_created_by' => Yii::t('rms', 'Pse Created By'),
            'pse_updated_by' => Yii::t('rms', 'Pse Updated By'),
            'pse_created_at' => Yii::t('rms', 'Pse Created At'),
            'pse_updated_at' => Yii::t('rms', 'Pse Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPseCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pse_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPseNationalityRel()
    {
        return $this->hasOne(Nationality::className(), ['id' => 'pse_nationality_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPseUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pse_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPseEiRel()
    {
        return $this->hasOne(EducationInformation::className(), ['id' => 'pse_ei_relId']);
    }
}
