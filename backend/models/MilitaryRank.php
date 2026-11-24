<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "military_rank".
 *
 * @property int $id
 * @property int $mr_category_relId
 * @property string $mr_rank_name
 * @property int $mr_order_count
 * @property int $mr_duration_month
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property MilitaryRankCategory $mrCategoryRel
 * @property User $updatedBy
 */
class MilitaryRank extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'military_rank';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['mr_category_relId', 'mr_rank_name', 'mr_order_count', 'mr_duration_month', 'created_at', 'created_by', 'data','mr_accronym'], 'required'],
            [['mr_category_relId', 'mr_order_count', 'mr_duration_month', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['mr_rank_name'], 'string', 'max' => 100],
            [['mr_accronym'], 'string', 'max' => 15],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['mr_category_relId'], 'exist', 'skipOnError' => true, 'targetClass' => MilitaryRankCategory::className(), 'targetAttribute' => ['mr_category_relId' => 'id']],
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
            'mr_category_relId' => Yii::t('rms', 'Rank Category'),
            'mr_rank_name' => Yii::t('rms', 'Rank Name'),
            'mr_order_count' => Yii::t('rms', 'Order Count'),
            'mr_accronym' => Yii::t('rms', 'Rank Accronym'),
            'mr_duration_month' => Yii::t('rms', 'Duration(Month)'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'mr_rank_name'=>$modelObj->mr_rank_name,
            'mr_accronym'=>$modelObj->mr_accronym,
            'mr_category_relId'=>intval($modelObj->mr_category_relId),
            'mr_order_count'=>intval($modelObj->mr_order_count),
            'mr_duration_month'=>intval($modelObj->mr_duration_month),
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
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMrCategoryRel()
    {
        return $this->hasOne(MilitaryRankCategory::className(), ['id' => 'mr_category_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
