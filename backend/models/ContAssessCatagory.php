<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "cont_assess_catagory".
 *
 * @property integer $id
 * @property integer $cat_course_relId
 * @property string $cat_name_relId
 * @property double $cat_maxPoint
 * @property integer $cat_created_by
 * @property string $cat_created_at
 * @property string $cat_updated_at
 * @property integer $cat_updated_by
 *
 * @property User $catCreatedBy
 * @property User $catUpdatedBy
 * @property Course $catCourseRel
 * @property ContAssessMarkAssignment[] $contAssessMarkAssignments
 */
class ContAssessCatagory extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cont_assess_catagory';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cat_course_relId', 'cat_name_relId', 'cat_maxPoint', 'cat_created_by', 'cat_created_at', 'cat_updated_at', 'cat_updated_by'], 'required'],
            [['cat_course_relId', 'cat_created_by', 'cat_updated_by'], 'integer'],
            [['cat_maxPoint'], 'number'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
            [['cat_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cat_created_by' => 'id']],
            [['cat_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cat_updated_by' => 'id']],
            [['cat_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['cat_course_relId' => 'id']],
            [['cat_name_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagoryName::className(), 'targetAttribute' => ['cat_name_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cat_course_relId' => Yii::t('rms', 'Course Title'),
            'cat_name_relId' => Yii::t('rms', 'Catagory Name'),
            'cat_maxPoint' => Yii::t('rms', 'Max. Total Point(%)'),
            'cat_created_by' => Yii::t('rms', 'Created By'),
            'cat_created_at' => Yii::t('rms', 'Created At'),
            'cat_updated_at' => Yii::t('rms', 'Updated At'),
            'cat_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function getDeptCourses(){
        if (Yii::$app->user->can('DepartmentHead')) {
            $instDept = Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one();
            $deptCourses = Course::find()->where(['course_dept_relId'=>$instDept->inst_dep_relId])->all();
            return $deptCourses;
        }else{
            return [];
        }
    }

    // Get any catagory made for this course
    public function getCourseCatagor($courseId){
        return $this->find()->where(['cat_course_relId'=>$courseId])->all();
    }

    public function remainingPoint($courseId){
        return 100 - $this->find()->where(['cat_course_relId'=>$courseId])->sum('cat_maxPoint');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cat_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cat_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'cat_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatNameRel()
    {
        return $this->hasOne(ContAssessCatagoryName::className(), ['id' => 'cat_name_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContAssessMarkAssignments()
    {
        return $this->hasMany(ContAssessMarkAssignment::className(), ['ma_catagory_relId' => 'id']);
    }
}
