<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "setting_history_entry".
 *
 * @property int $id
 * @property string $setting_used_upto academic year structured entry for end date of the setting
 * @property string $file_path
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class SettingHistoryEntry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'setting_history_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['setting_used_upto', 'file_path', 'created_by', 'created_at', 'data'], 'required'],
            [['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['data'], 'string'],
            [['setting_used_upto'], 'string', 'max' => 15],
            [['file_path'], 'string', 'max' => 100],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'setting_used_upto' => Yii::t('rms', 'Setting Used Upto'),
            'file_path' => Yii::t('rms', 'File Path'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'setting_used_upto'=>$this->setting_used_upto,
            'file_path'=>$this->file_path,
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
