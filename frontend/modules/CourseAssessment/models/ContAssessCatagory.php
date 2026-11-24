<?php

namespace frontend\modules\CourseAssessment\models;

use Yii;
use \backend\models\User;
use \backend\models\Course;
// use \backend\models\CourseOffering2Detail;
use \backend\models\Semester;
use \backend\models\SchoolYear;
use \backend\models\CatagoryName2;
use frontend\modules\Reassessment\Reassessment;

/**
 * This is the model class for table "cont_assess_catagory2".
 *
 * @property int $id
 * @property int $cat_acadYear_relId
 * @property int $cat_sem_relId
 * @property int $cat_coff_relId
 * @property int $cat_course_relId
 * @property int $cat_catagory_relId
 * @property float $cat_maxPoint
 * @property string $cat_created_at
 * @property int $cat_created_by
 * @property int|null $cat_updated_by
 * @property string|null $cat_updated_at
 *
 * @property CatagoryName2 $catCatagoryRel
 * @property CourseOffering2Detail $catCoffRel
 * @property User $catCreatedBy
 * @property User $catUpdatedBy
 * @property Course $catCourseRel
 * @property SchoolYear $catAcadYearRel
 * @property Semester $catSemRel
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property InstanceMarkAssignment[] $instanceMarkAssignments
 * @property ReassessmentHistory[] $reassessmentHistories
 */
class ContAssessCatagory extends \yii\db\ActiveRecord
{
    public $_old_cat_maxPoint;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cont_assess_catagory2';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['cat_acadYear_relId', 'cat_sem_relId', 'cat_coff_relId', 'cat_course_relId', 'cat_catagory_relId', 'cat_maxPoint', 'cat_created_at', 'cat_created_by'], 'required'],
            [['cat_acadYear_relId', 'cat_sem_relId', 'cat_coff_relId', 'cat_course_relId', 'cat_catagory_relId', 'cat_created_by', 'cat_updated_by'], 'integer'],
            [['cat_maxPoint','_old_cat_maxPoint'], 'number'],
            [['cat_created_at', 'cat_updated_at'], 'safe'],
            [['cat_catagory_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CatagoryName2::class, 'targetAttribute' => ['cat_catagory_relId' => 'id']],
            [['cat_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['cat_coff_relId' => 'id']],
            [['cat_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cat_created_by' => 'id']],
            [['cat_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cat_updated_by' => 'id']],
            [['cat_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['cat_course_relId' => 'id']],
            [['cat_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['cat_acadYear_relId' => 'id']],
            [['cat_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['cat_sem_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cat_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'cat_sem_relId' => Yii::t('rms', 'Semester`'),
            'cat_coff_relId' => Yii::t('rms', 'Offered to'),
            'cat_course_relId' => Yii::t('rms', 'Course'),
            'cat_catagory_relId' => Yii::t('rms', 'Assess.t Categ.'),
            'cat_maxPoint' => Yii::t('rms', 'Max Point'),
            'cat_created_at' => Yii::t('rms', 'Created At'),
            'cat_created_by' => Yii::t('rms', 'Created By'),
            'cat_updated_by' => Yii::t('rms', 'Updated By'),
            'cat_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function instantaitedPoint(){
        return $this->getInstanceMarkAssignments()->sum('ima_possiblePoint');
    }

    public function getRemainingPoint(){
        $totalPoint = 0;
        $totalPoint = 100;
        $totalPoint = $totalPoint - $this->getTotalPoint();
        return $totalPoint;
    }

    private function getTotalPoint(){
        $allocatedTotalPoint = 0;
        $allocatedTotalPoint = $this->catCoffRel->getContAssessCatagories()->andWhere(['cat_coff_relId' => $this->cat_coff_relId])->sum('cat_maxPoint');
        return $allocatedTotalPoint;
    }

    public function getRemainingWeight(){
        $totalWeight = 0;
        $totalWeight = $this->cat_maxPoint;
        $totalWeight = $totalWeight - $this->getTotalWeight();
        return $totalWeight;
    }

    private function getTotalWeight(){
        $totalWeight = 0;
        $totalWeight = InstanceMarkAssignment::find()->where(['ima_catagorySetup_relId' => $this->id])->sum('ima_possiblePoint');
        return $totalWeight;
    }

    public function getCatagoryName(){
        return $this->catCatagoryRel->catagory_name;
    }

    public function getCatNameIds(){
        return $this->catCoffRel->getContAssessCatagories()->andWhere(['cat_coff_relId' => $this->cat_coff_relId])->select('cat_catagory_relId')->column();

        // var_dump($nameIds);exit;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCatagoryRel()
    {
        return $this->hasOne(CatagoryName2::class, ['id' => 'cat_catagory_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'cat_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cat_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cat_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'cat_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'cat_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'cat_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment::class, ['ca_catagorySetup_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInstanceMarkAssignments()
    {
        return $this->hasMany(InstanceMarkAssignment::class, ['ima_catagorySetup_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getReassessmentHistories()
    {
        return $this->hasMany(Reassessment::class, ['ras_contAssCatag_relId' => 'id']);
    }
}
