<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "payment_reason".
 *
 * @property integer $id
 * @property string $pr_desc
 * @property double $pr_amount
 * @property integer $remark
 *
 * @property SelfSponsoredPaymentHistory[] $selfSponsoredPaymentHistories
 */
class PaymentReason extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_reason';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pr_desc', 'pr_amount'], 'required'],
            [['pr_desc','remark'], 'string'],
            [['pr_amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pr_desc' => Yii::t('rms', 'Payment Reason Description'),
            'pr_amount' => Yii::t('rms', 'Amount'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSelfSponsoredPaymentHistories()
    {
        return $this->hasMany(SelfSponsoredPaymentHistory::className(), ['ssph_pymntRsn_relId' => 'id']);
    }
}
