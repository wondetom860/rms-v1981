<?php

namespace backend\models;
use \common\components\CommonControllerFunctions as CCF;
use Yii;

/**
 * This is the model class for table "cont_assess_mark_assignment".
 *
 * @property integer $id
 * @property integer $ma_catagory_relId
 * @property integer $cat_coff_relId
 * @property string $ma_instance
 * @property double $ma_possiblePoint
 * @property string $ma_created_at
 * @property integer $ma_created_by
 * @property integer $ma_updated_by
 * @property string $ma_updated_at
 *
 * @property ContAssessCatagory $maCatagoryRel
 * @property User $maCreatedBy
 * @property User $maUpdatedBy
 * @property CourseOffering2Detail $catCoffRel
 * @property ContinuousAssessment[] $continuousAssessments
 */
class ContAssessMarkAssignment extends \yii\db\ActiveRecord
{
    public $courseId;
    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'cont_assess_mark_assignment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ma_catagory_relId', 'cat_coff_relId', 'ma_instance', 'ma_possiblePoint', 'ma_created_at', 'ma_created_by', 'ma_updated_at'], 'required'],
            [['ma_catagory_relId', 'cat_coff_relId', 'ma_created_by', 'ma_updated_by'], 'integer'],
            [['ma_possiblePoint'], 'number'],
            [['ma_created_at', 'ma_updated_at'], 'safe'],
            [['ma_instance'], 'string', 'max' => 40],
            [['ma_catagory_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory::className(), 'targetAttribute' => ['ma_catagory_relId' => 'id']],
            [['ma_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ma_created_by' => 'id']],
            [['ma_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ma_updated_by' => 'id']],
            [['cat_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::className(), 'targetAttribute' => ['cat_coff_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ma_catagory_relId' => Yii::t('rms', 'Ass. Catagory'),
            'cat_coff_relId' => Yii::t('rms', 'Course'),
            'ma_instance' => Yii::t('rms', 'Catagory Instance'),
            'ma_possiblePoint' => Yii::t('rms', 'Possible Point'),
            'ma_created_at' => Yii::t('rms', 'Created At'),
            'ma_created_by' => Yii::t('rms', 'Created By'),
            'ma_updated_by' => Yii::t('rms', 'Updated By'),
            'ma_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function getInstCourse(){
        // get instructor ID:
        $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

        // initiate current year and semester.
        $this->__constructor();
        // get course assigned for the instructor.
        $coffs = CourseOffering2Detail::find()
            ->where(['coff2det_inst_relId'=> $inst->id])
            ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->andWhere(['coff2_acadYear_relId'=>$this->acadYearId,'coff2_sem_relId'=> $this->semId])
            ->groupBy('coff2det_course_relId')
            ->all();
        $cofArray = [];
        if ($coffs && count($coffs) > 0) {
            foreach ($coffs as $key) {
                $cofArray[] = [
                    'id'=>$key->id,
                    'course_title'=>Course::findOne($key->coff2det_course_relId)->course_title
                ];
            }
        }
        // var_dump($cofArray);die();
        return $cofArray;
    }

    // Get assessment undet the selected assessment catagory,
    public function getCourseAssessInstances($coursId,$coffdetId,$catagoryId){
        return $this->find()->where(['ma_catagory_relId'=>$catagoryId,'cat_coff_relId'=>$coffdetId])->all();
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaCatagoryRel()
    {
        return $this->hasOne(ContAssessCatagory::className(), ['id' => 'ma_catagory_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ma_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ma_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatCoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::className(), ['id' => 'cat_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessments()
    {
        return $this->hasMany(ContinuousAssessment::className(), ['ca_assignment_relId' => 'id']);
    }
}
