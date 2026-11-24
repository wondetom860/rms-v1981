<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "secondary_school_attended".
 *
 * @property integer $id
 * @property integer $ssa_ei_relId
 * @property string $ssa_school_name
 * @property string $ssa_town
 * @property string $ssa_year
 * @property string $ssa_gradeLevels
 * @property integer $ssa_created_by
 * @property string $ssa_created_at
 * @property integer $ssa_updated_by
 * @property string $ssa_updated_at
 *
 * @property User $ssaCreatedBy
 * @property User $ssaUpdatedBy
 * @property EducationInformation $ssaEiRel
 */
class SecondarySchoolAttended extends \yii\db\ActiveRecord
{

    public $ar_request;
    public $std_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'secondary_school_attended';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ssa_school_name', 'ssa_town', 'ssa_year', 'ssa_gradeLevels'], 'required'],
            [['ssa_created_by', 'ssa_updated_by', 'std_id', 'ar_request'], 'integer'],
            [['ssa_school_name', 'ssa_town'], 'string'],
            [['ssa_year', 'ssa_created_at', 'ssa_updated_at'], 'safe'],
            [['ssa_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ssa_created_by' => 'id']],
            [['ssa_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ssa_updated_by' => 'id']],
            // [['ssa_ei_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EducationInformation::class, 'targetAttribute' => ['ssa_ei_relId' => 'id']],
        ];
    }

    public function suchExists($id = null)
    {
        return $id ? self::find()
            ->where([
                'ssa_ei_relId' => $this->ssa_ei_relId,
                'ssa_school_name' => $this->ssa_school_name,
                'ssa_gradeLevels' => $this->ssa_gradeLevels
            ])
            ->andWhere(' id <>  ' . $id)
            ->count() > 0 :  self::find()
            ->where([
                'ssa_ei_relId' => $this->ssa_ei_relId,
                'ssa_school_name' => $this->ssa_school_name,
                'ssa_gradeLevels' => $this->ssa_gradeLevels
            ])
            ->count() > 0;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ssa_ei_relId' => Yii::t('rms', 'Education Info. Entry'),
            'ssa_school_name' => Yii::t('rms', 'School Name'),
            'ssa_town' => Yii::t('rms', 'Town'),
            'ssa_year' => Yii::t('rms', 'Year'),
            'ssa_gradeLevels' => Yii::t('rms', 'Grade Levels'),
            'ssa_created_by' => Yii::t('rms', 'Created By'),
            'ssa_created_at' => Yii::t('rms', 'Created At'),
            'ssa_updated_by' => Yii::t('rms', 'Updated By'),
            'ssa_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsaCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ssa_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsaUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ssa_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsaEiRel()
    {
        return $this->hasOne(EducationInformation::class, ['id' => 'ssa_ei_relId']);
    }
}
