<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "student_group".
 *
 * @property integer $id
 * @property integer $group_name
 * @property integer $group_created_by
 * @property string $group_created_at
 * @property integer $group_updated_by
 * @property string $group_updated_at
 *
 * @property User $groupCreatedBy
 * @property User $groupUpdatedBy
 */
class StudentGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'student_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['group_name', 'group_created_by', 'group_created_at', 'group_updated_by', 'group_updated_at'], 'required'],
            [['group_created_by', 'group_updated_by'], 'integer'],
            [['group_created_at', 'group_updated_at'], 'safe'],
            [['group_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['group_created_by' => 'id']],
            [['group_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['group_updated_by' => 'id']],
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
            'group_created_by' => Yii::t('rms', 'Group Created By'),
            'group_created_at' => Yii::t('rms', 'Group Created At'),
            'group_updated_by' => Yii::t('rms', 'Group Updated By'),
            'group_updated_at' => Yii::t('rms', 'Group Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'group_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroupUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'group_updated_by']);
    }
}
