<?php

namespace common\modules\HRMS\models;

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
            [['catagory_name', 'created_by', 'created_at', 'data'], 'required'],
            [['created_by', 'updated_by'], 'integer'],
            [['created_at', 'updated_at'], 'safe'],
            [['data'], 'string'],
            [['catagory_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'catagory_name' => Yii::t('rms', 'Catagory Name'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }
}
