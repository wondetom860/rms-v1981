<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "w_group".
 *
 * @property integer $id
 * @property string $group_name
 * @property string $remark
 *
 * @property WGroupSetup[] $wGroupSetups
 */
class WGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_name'], 'required'],
            [['remark'], 'string'],
            [['group_name'], 'string', 'max' => 10],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'group_name' => Yii::t('rms', 'Group Name'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWGroupSetups()
    {
        return $this->hasMany(WGroupSetup::class, ['gs_group_relId' => 'id']);
    }
}
