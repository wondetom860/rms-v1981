<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "ethnic_group".
 *
 * @property int $id
 * @property string $name
 * @property int $region_relId
 * @property int $remark
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property Region $regionRel
 * @property User $updatedBy
 */
class EthnicGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ethnic_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'region_relId', 'created_by', 'created_at', 'data'], 'required'],
            [['region_relId', 'remark', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['name'], 'string', 'max' => 30],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['region_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Region::className(), 'targetAttribute' => ['region_relId' => 'id']],
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
            'name' => Yii::t('rms', 'Ethnic Group'),
            'region_relId' => Yii::t('rms', 'Region/State'),
            'remark' => Yii::t('rms', 'Remark'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $mainData = (object)[
            'name'=>$this->name,
            'region_relId'=>intval($this->region_relId),
            'updated_by'=>intval($this->updated_by),
            'updated_at'=>intval($this->updated_at),
        ];
        $this->data = md5(serialize($mainData));
        return;
    }

    public function exists(){
        return $this->find()->where(['region_relId' => $this->region_relId,'name' => $this->name])->count() > 0;
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[RegionRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegionRel()
    {
        return $this->hasOne(Region::className(), ['id' => 'region_relId']);
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\backend\models\User::className(), ['eth_group_relId' => 'id']);
    }
}
