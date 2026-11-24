<?php

namespace frontend\modules\GC\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "gc_special_mention".
 *
 * @property int $id
 * @property float $gsm_maxCGPA
 * @property float $gsm_minCGPA
 * @property string $gsm_name
 * @property int $gsm_created_by
 * @property string $gsm_created_at
 * @property string|null $gsm_updated_at
 * @property int|null $gsm_updated_by
 *
 * @property User $gsmCreatedBy
 * @property User $gsmUpdatedBy
 */
class GcSpecialMention extends \yii\db\ActiveRecord

{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'gc_special_mention';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['gsm_maxCGPA', 'gsm_minCGPA', 'gsm_name', 'gsm_created_by', 'gsm_created_at'], 'required'],
            [['gsm_maxCGPA', 'gsm_minCGPA'], 'number'],
            [['gsm_created_by', 'gsm_updated_by'], 'integer'],
            [['gsm_created_at', 'gsm_updated_at'], 'safe'],
            [['gsm_name'], 'string', 'max' => 200],
            [['gsm_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gsm_created_by' => 'id']],
            [['gsm_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['gsm_updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gsm_maxCGPA' => Yii::t('rms', 'Max CGPA'),
            'gsm_minCGPA' => Yii::t('rms', 'Min CGPA'),
            'gsm_name' => Yii::t('rms', 'Title'),
            'gsm_created_by' => Yii::t('rms', 'Created By'),
            'gsm_created_at' => Yii::t('rms', 'Created At'),
            'gsm_updated_at' => Yii::t('rms', 'Updated At'),
            'gsm_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * Gets query for [[GsmCreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGsmCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gsm_created_by']);
    }

    /**
     * Gets query for [[GsmUpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGsmUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'gsm_updated_by']);
    }
}
