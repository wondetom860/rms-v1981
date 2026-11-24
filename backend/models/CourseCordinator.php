<?php

namespace backend\models;

use Yii;
use \common\components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "course_cordinator".
 *
 * @property integer $id
 * @property integer $cc_acadYear_relId
 * @property integer $cc_sem_relId
 * @property integer $cc_adm_relId
 * @property integer $cc_course_relId
 * @property integer $cc_inst_relId
 * @property integer $cc_created_by
 * @property integer $cc_updated_by
 * @property string $cc_created_at
 * @property string $cc_updated_at
 *
 * @property SchoolYear $ccAcadYearRel
 * @property Admission $ccAdmRel
 * @property User $ccCreatedBy
 * @property Instructor $ccInstRel
 * @property Semester $ccSemRel
 * @property User $ccUpdatedBy
 * @property Course $ccCourseRel
 */
class CourseCordinator extends \yii\db\ActiveRecord
{

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_cordinator';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['cc_acadYear_relId', 'cc_sem_relId', 'cc_adm_relId', 'cc_course_relId', 'cc_inst_relId', 'cc_created_by', 'cc_created_at'], 'required'],
            [['cc_acadYear_relId', 'cc_sem_relId', 'cc_adm_relId', 'cc_course_relId', 'cc_inst_relId', 'cc_created_by', 'cc_updated_by'], 'integer'],
            [['cc_created_at', 'cc_updated_at'], 'safe'],
            [['cc_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['cc_acadYear_relId' => 'id']],
            [['cc_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::className(), 'targetAttribute' => ['cc_adm_relId' => 'id']],
            [['cc_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cc_created_by' => 'id']],
            [['cc_inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::className(), 'targetAttribute' => ['cc_inst_relId' => 'id']],
            [['cc_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['cc_sem_relId' => 'id']],
            [['cc_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['cc_updated_by' => 'id']],
            [['cc_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['cc_course_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cc_acadYear_relId' => Yii::t('rms', 'Cc Acad Year Rel ID'),
            'cc_sem_relId' => Yii::t('rms', 'Cc Sem Rel ID'),
            'cc_adm_relId' => Yii::t('rms', 'Cc Adm Rel ID'),
            'cc_course_relId' => Yii::t('rms', 'Cc Course Rel ID'),
            'cc_inst_relId' => Yii::t('rms', 'Cc Inst Rel ID'),
            'cc_created_by' => Yii::t('rms', 'Cc Created By'),
            'cc_updated_by' => Yii::t('rms', 'Cc Updated By'),
            'cc_created_at' => Yii::t('rms', 'Cc Created At'),
            'cc_updated_at' => Yii::t('rms', 'Cc Updated At'),
        ];
    }

    public function checkIfExists($courseId){
        $this->__constructor();
        return $this->find()
            ->where([
                'cc_acadYear_relId'=>$this->acadYearId,
                'cc_sem_relId'=>$this->semId,
                'cc_course_relId' => $courseId,
                ])
            ->one();
    }


    public static function isCordinator($courseId,$acadYearId,$semId){
        if(\Yii::$app->user->can('Instructor') || \Yii::$app->user->can('DepartmentHead')){
            // $this->__constructor();
            $ccc = new \backend\models\CourseOffering2Detail();
            if (!$ccc->checkRequired($courseId)) {
                return true;
            }
            $instId = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id]);
            return CourseCordinator::find()
                ->where([
                    'cc_acadYear_relId'=>$acadYearId,
                    'cc_sem_relId'=>$semId,
                ])
                ->andWhere([
                    'cc_course_relId'=>$courseId,
                    'cc_inst_relId'=>$instId ->id,
                ])
                ->count() > 0;
        }
        return false;
    }

    public static function getCordinator($courseId,$acadYearId,$semId){//returns the INST_ID if the course is offered for multiple groups
        $ccc = new \backend\models\CourseOffering2Detail();
        if (!$ccc->checkRequired($courseId)) {//checi if offered for multiple
            return NULL;
        }
        //$instId = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id]);
        return CourseCordinator::find()
            ->where([
                'cc_acadYear_relId'=>$acadYearId,
                'cc_sem_relId'=>$semId,
            ])
            ->andWhere([
                'cc_course_relId'=>$courseId,
            ])
            ->one();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'cc_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcAdmRel()
    {
        return $this->hasOne(Admission::className(), ['id' => 'cc_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cc_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcInstRel()
    {
        return $this->hasOne(Instructor::className(), ['id' => 'cc_inst_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'cc_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'cc_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'cc_course_relId']);
    }
}
