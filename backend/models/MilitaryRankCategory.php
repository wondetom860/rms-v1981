<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "military_rank_category".
 *
 * @property int $id
 * @property string $catagory_name
 * @property int $created_by
 * @property string $created_at
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property MilitaryRank[] $militaryRanks
 * @property User $createdBy
 * @property User $updatedBy
 */
class MilitaryRankCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'military_rank_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['catagory_name', 'created_by', 'created_at', 'data'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['catagory_name'], 'string', 'max' => 100],
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
            'catagory_name' => Yii::t('rms', 'Rank Category'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }


    public function getData($modelObj){
        $mainData = (object)[
            'catagory_name'=>$modelObj->catagory_name,
            'updated_by'=>intval($modelObj->updated_by),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryRanks()
    {
        return $this->hasMany(MilitaryRank::className(), ['mr_category_relId' => 'id']);
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
