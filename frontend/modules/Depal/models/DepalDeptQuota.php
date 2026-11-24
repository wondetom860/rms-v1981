<?php

namespace frontend\modules\Depal\models;

use Yii;
use backend\models\User;
use \backend\models\Department;

/**
 * This is the model class for table "depal_dept_quota".
 *
 * @property int $id
 * @property int $setting_relId
 * @property int $dept_relId
 * @property float $q_percentage
 * @property int $auto_allocated
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 * @property string|null $remark
 *
 * @property User $createdBy
 * @property Department $deptRel
 * @property DepalSetting $settingRel
 * @property User $updatedBy
 */
class DepalDeptQuota extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depal_dept_quota';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['setting_relId', 'dept_relId', 'q_percentage', 'created_by', 'created_at', 'data'], 'required'],
            [['setting_relId', 'dept_relId', 'created_by', 'created_at', 'updated_at', 'updated_by','auto_allocated'], 'integer'],
            [['q_percentage'], 'number'],
            [['data', 'remark'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['dept_relId' => 'id']],
            [['setting_relId'], 'exist', 'skipOnError' => true, 'targetClass' => DepalSetting::className(), 'targetAttribute' => ['setting_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'setting_relId' => Yii::t('rms', 'Setting Rel ID'),
            'dept_relId' => Yii::t('rms', 'Dept Rel ID'),
            'q_percentage' => Yii::t('rms', 'Q Percentage'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'remark' => Yii::t('rms', 'Remark'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'setting_relId'=>$this->setting_relId,
            'dept_relId'=>$this->dept_relId,
            'q_percentage'=>$this->q_percentage,
            'auto_allocated'=>intval($this->auto_allocated),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }


    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[DeptRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptRel()
    {
        return $this->hasOne(Department::className(), ['id' => 'dept_relId']);
    }

    /**
     * Gets query for [[SettingRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSettingRel()
    {
        return $this->hasOne(DepalSetting::className(), ['id' => 'setting_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
