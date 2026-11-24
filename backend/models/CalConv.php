<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cal_conv".
 *
 * @property integer $id
 * @property integer $cc_ger_cal
 * @property integer $cc_et_cal
 * @property string $cc_by
 */
class CalConv extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cal_conv';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cc_ger_cal', 'cc_et_cal'], 'required'],
            [['cc_ger_cal', 'cc_et_cal'], 'integer'],
            [['cc_by'], 'string', 'max' => 25],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cc_ger_cal' => Yii::t('rms', 'Cc Ger Cal'),
            'cc_et_cal' => Yii::t('rms', 'Cc Et Cal'),
            'cc_by' => Yii::t('rms', 'Cc By'),
        ];
    }
}
