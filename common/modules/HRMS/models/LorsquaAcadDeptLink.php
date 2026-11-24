<?php

namespace common\modules\HRMS\models;

use Yii;

/**
 * This is the model class for table "lorsqua_acad_dept_link".
 *
 * @property int $id
 * @property int $position_detail_relId
 * @property string $acad_dept_id
 * @property int $created_by
 * @property string $created_at
 * @property int|null $updated_by
 * @property string|null $updated_at
 * @property string $data
 *
 * @property LoqPositionDetail $positionDetailRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class LorsquaAcadDeptLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lorsqua_acad_dept_link';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_hrms');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['position_detail_relId', 'acad_dept_id', 'created_by', 'created_at', 'data'], 'required'],
            [['position_detail_relId', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['acad_dept_id'], 'string', 'max' => 4],
            [['position_detail_relId'], 'exist', 'skipOnError' => true, 'targetClass' => LoqPositionDetail::className(), 'targetAttribute' => ['position_detail_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
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
            'position_detail_relId' => Yii::t('rms', 'Position Detail Rel ID'),
            'acad_dept_id' => Yii::t('rms', 'Acad Dept ID'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPositionDetailRel()
    {
        return $this->hasOne(LoqPositionDetail::className(), ['id' => 'position_detail_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
