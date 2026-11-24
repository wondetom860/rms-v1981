<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "employment_type".
 *
 * @property int $id
 * @property string $et_name
 * @property int|null $cont_renewal_interval_month
 * @property string $et_created_at
 * @property int $et_created_by
 * @property string|null $et_updated_at
 * @property int|null $et_updated_by
 * @property string|null $remarks
 * @property string $data
 *
 * @property User $etUpdatedBy
 * @property User $etCreatedBy
 */
class EmploymentType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'employment_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['et_name', 'et_created_at', 'et_created_by', 'data'], 'required'],
            [['cont_renewal_interval_month', 'et_created_by', 'et_updated_by','et_created_at', 'et_updated_at'], 'integer'],
            [['remarks', 'data'], 'string'],
            [['et_name'], 'string', 'max' => 255],
            [['et_name'], 'unique'],
            [['et_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['et_updated_by' => 'id']],
            [['et_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['et_created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'et_name' => Yii::t('rms', 'Emplyment Type'),
            'cont_renewal_interval_month' => Yii::t('rms', 'Contract duration'),
            'et_created_at' => Yii::t('rms', 'Created At'),
            'et_created_by' => Yii::t('rms', 'Created By'),
            'et_updated_at' => Yii::t('rms', 'Updated At'),
            'et_updated_by' => Yii::t('rms', 'Updated By'),
            'remarks' => Yii::t('rms', 'Remarks'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'et_name'=>$this->et_name,
            'cont_renewal_interval_month'=>$this->cont_renewal_interval_month,
            'remarks'=>$this->remarks,
            'et_created_by'=>intval($this->et_created_by),
            'et_updated_by'=>intval($this->et_updated_by),
        ]));
    }

    /**
     * Gets query for [[EtUpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEtUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'et_updated_by']);
    }

    /**
     * Gets query for [[EtCreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEtCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'et_created_by']);
    }
}
