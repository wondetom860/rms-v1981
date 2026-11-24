<?php

namespace frontend\modules\ExitExam\models;

use Yii;
use common\models\User;

/**
 * This is the model class for table "et_setting".
 *
 * @property int $id
 * @property string $setting_header_info
 * @property int|null $max_trails
 * @property float|null $pass_mark
 * @property int|null $active_status
 * @property int|null $locked
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 */
class EtSetting extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'et_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['setting_header_info', 'created_at', 'created_by', 'data'], 'required'],
            [['max_trails', 'active_status', 'locked', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['pass_mark'], 'number'],
            [['data'], 'string'],
            [['setting_header_info'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'setting_header_info' => Yii::t('rms', 'Setting Heading'),
            'max_trails' => Yii::t('rms', 'Max Trails'),
            'pass_mark' => Yii::t('rms', 'Pass Mark'),
            'active_status' => Yii::t('rms', 'Active Status'),
            'locked' => Yii::t('rms', 'Locked'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        // Get the current date and time
        $this->data = md5(serialize([
            'setting_header_info' => $this->setting_header_info,
            'locked' => $this->locked,
            'created_at' => $this->created_at,
            'created_by' => $this->created_by,
            'max_trails' => $this->max_trails,
            'pass_mark' => $this->pass_mark,
            'active_status' => $this->active_status,
        ]));
    }

    /**
     * Gets query for [[EtTests]].
     *
     * @return \yii\db\ActiveQuery|EtTestQuery
     */
    public function getEtTests()
    {
        return $this->hasMany(EtElligiblityTest::class, ['setting_relId' => 'id']);
    }


    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

/**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }


    /**
     * {@inheritdoc}
     * @return EtSettingQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EtSettingQuery(get_called_class());
    }
}
