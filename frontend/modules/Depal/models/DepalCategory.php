<?php

namespace frontend\modules\Depal\models;

use Yii;
use \backend\models\User;

/**
 * This is the model class for table "depal_category".
 *
 * @property int $id
 * @property string $category_name
 * @property string $comparison_type
 * @property float|null $lower_bound
 * @property float|null $upper_bound
 * @property string $attribute
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string $data
 *
 * @property DepalCategorizedCandidates[] $depalCategorizedCandidates
 * @property User $createdBy
 * @property User $updatedBy
 */
class DepalCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depal_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name', 'comparison_type', 'attribute', 'created_by', 'created_at', 'data'], 'required'],
            [['comparison_type', 'data'], 'string'],
            [['lower_bound', 'upper_bound'], 'number'],
            [['created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['category_name'], 'string', 'max' => 40],
            [['attribute'], 'string', 'max' => 20],
            [['category_name'], 'unique'],
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
            'category_name' => Yii::t('rms', 'Category Name'),
            'comparison_type' => Yii::t('rms', 'Comparison Type'),
            'lower_bound' => Yii::t('rms', 'Lower Bound'),
            'upper_bound' => Yii::t('rms', 'Upper Bound'),
            'attribute' => Yii::t('rms', 'Attribute'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'category_name'=>$this->category_name,
            'comparison_type'=>$this->comparison_type,
            'lower_bound'=>intval($this->lower_bound),
            'upper_bound'=>intval($this->upper_bound),
            'attribute'=>intval($this->attribute),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function alreadyExists(){
        return self::find()
            ->where([
                'category_name' => $this->category_name,
                'comparison_type' => $this->comparison_type,
                'lower_bound' => $this->lower_bound,
                'upper_bound' => $this->upper_bound,
                'attribute' => $this->attribute,
            ])
            ->count() > 0;
    }

    /**
     * Gets query for [[DepalCategorizedCandidates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepalCategorizedCandidates()
    {
        return $this->hasMany(DepalCategorizedCandidates::className(), ['category_relId' => 'id']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
