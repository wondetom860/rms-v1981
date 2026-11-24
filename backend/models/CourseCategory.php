<?php

namespace backend\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "course_category".
 *
 * @property int $id
 * @property string $catgeory_name
 * @property int|null $pass_grade_relId Minimum pass grade for courses under this category
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 */
class CourseCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pass_grade_relId', 'created_at', 'updated_at', 'created_by', 'updated_by'], 'integer'],
            [['data','catgeory_name'], 'required'],
            [['data'], 'string'],
            [['catgeory_name'], 'unique'],
            [['catgeory_name'], 'string',  'max' => 50],
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
            'catgeory_name' => Yii::t('rms', 'Category Name'),
            'pass_grade_relId' => Yii::t('rms', 'Min. Pass Grade'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'catgeory_name' => $this->catgeory_name,
            'pass_grade_relId' => $this->pass_grade_relId,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::class,
            BlameableBehavior::class
        ];
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    // pass_grade_relId
    /**
     * Gets query for [[PassgradeRel]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getPassGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'pass_grade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['course_category_relId' => 'id']);
    }


    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * {@inheritdoc}
     * @return CourseCategoryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CourseCategoryQuery(get_called_class());
    }
}
