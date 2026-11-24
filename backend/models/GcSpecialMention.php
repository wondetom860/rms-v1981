<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "gc_special_mention".
 *
 * @property integer $id
 * @property double $gsm_maxCGPA
 * @property double $gsm_minCGPA
 * @property string $gsm_name
 * @property integer $gsm_created_by
 * @property string $gsm_created_at
 * @property string $gsm_updated_at
 * @property integer $gsm_updated_by
 *
 * @property User $gsmCreatedBy
 * @property User $gsmUpdatedBy
 */
class GcSpecialMention extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'gc_special_mention';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gsm_maxCGPA', 'gsm_minCGPA', 'gsm_name', 'gsm_created_by', 'gsm_created_at'], 'required'],
            [['gsm_maxCGPA', 'gsm_minCGPA'], 'number'],
            [['gsm_created_by', 'gsm_updated_by'], 'integer'],
            [['gsm_created_at', 'gsm_updated_at'], 'safe'],
            [['gsm_name'], 'string', 'max' => 200],
            [['gsm_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['gsm_created_by' => 'id']],
            [['gsm_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['gsm_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gsm_maxCGPA' => Yii::t('rms', 'Max. CGPA'),
            'gsm_minCGPA' => Yii::t('rms', 'Min. CGPA'),
            'gsm_name' => Yii::t('rms', 'Name'),
            'gsm_created_by' => Yii::t('rms', 'Created By'),
            'gsm_created_at' => Yii::t('rms', 'Gsm Created At'),
            'gsm_updated_at' => Yii::t('rms', 'Gsm Updated At'),
            'gsm_updated_by' => Yii::t('rms', 'Gsm Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsmCreatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'gsm_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsmUpdatedBy()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'gsm_updated_by']);
    }
}
