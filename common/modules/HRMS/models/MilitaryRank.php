<?php

namespace common\modules\HRMS\models;

use Yii;

/**
 * This is the model class for table "military_rank".
 *
 * @property int $id
 * @property int $mr_category_relId
 * @property string $mr_rank_name
 * @property string $mr_accronym
 * @property string $mr_rank_name_am
 * @property string $mr_accronym_am
 * @property int $mr_order_count
 * @property int $mr_duration_month
 * @property int $mr_advance_interval_month
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 * @property float|null $mr_retirement_age
 *
 * @property LoqPositionDetail[] $loqPositionDetails
 * @property MilitaryEmployeeSalaryEntry[] $militaryEmployeeSalaryEntries
 * @property MilitaryProfileHistory[] $militaryProfileHistories
 * @property MilitaryProfileHistory[] $militaryProfileHistories0
 * @property MilitaryRankHierarchyCategory[] $militaryRankHierarchyCategories
 * @property MprlHeader[] $mprlHeaders
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
            [['mr_category_relId', 'mr_rank_name', 'mr_accronym', 'mr_rank_name_am', 'mr_accronym_am', 'mr_order_count', 'mr_duration_month', 'created_at', 'created_by', 'data'], 'required'],
            [['mr_category_relId', 'mr_order_count', 'mr_duration_month', 'mr_advance_interval_month', 'created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['mr_retirement_age'], 'number'],
            [['mr_rank_name', 'mr_rank_name_am'], 'string', 'max' => 100],
            [['mr_accronym', 'mr_accronym_am'], 'string', 'max' => 15],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'mr_category_relId' => Yii::t('rms', 'Category'),
            'mr_rank_name' => Yii::t('rms', 'Rank Name'),
            'mr_accronym' => Yii::t('rms', 'Accronym'),
            'mr_rank_name_am' => Yii::t('rms', 'Rank Name'),
            'mr_accronym_am' => Yii::t('rms', 'Accronym'),
            'mr_order_count' => Yii::t('rms', 'Order Count'),
            'mr_duration_month' => Yii::t('rms', 'Duration Month'),
            'mr_advance_interval_month' => Yii::t('rms', 'Advance Interval Month'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'mr_retirement_age' => Yii::t('rms', 'Mr Retirement Age'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLoqPositionDetails()
    {
        return $this->hasMany(LoqPositionDetail::className(), ['preq_military_rank' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryEmployeeSalaryEntries()
    {
        return $this->hasMany(MilitaryEmployeeSalaryEntry::className(), ['rank_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryProfileHistories()
    {
        return $this->hasMany(MilitaryProfileHistory::className(), ['mph_immidiate_prev_rank_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryProfileHistories0()
    {
        return $this->hasMany(MilitaryProfileHistory::className(), ['mph_rank_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMilitaryRankHierarchyCategories()
    {
        return $this->hasMany(MilitaryRankHierarchyCategory::className(), ['rank_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMprlHeaders()
    {
        return $this->hasMany(MprlHeader::className(), ['rank_relId' => 'id']);
    }
}
