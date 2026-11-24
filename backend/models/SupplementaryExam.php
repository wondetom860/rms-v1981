<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "supplementary_exam".
 *
 * @property integer $id
 * @property integer $se_srd2_relId
 * @property integer $se_std_relId 
 * @property integer $se_prevGrade_relId
 * @property integer $se_newGrade_relId
 * @property string $se_created_at
 * @property integer $se_created_by
 * @property string $se_updated_at
 * @property integer $se_updated_by
 * @property integer $se_registrarApproved
 * @property string $se_registrarApproved_at
 * @property integer $se_registrarApproved_by
 * @property integer $se_hodApproved
 * @property string $se_hodApproved_at
 * @property integer $se_hodApproved_by
 *
 * @property User $seCreatedBy
 * @property User $seHodApprovedBy
 * @property GradeScale $seNewGradeRel
 * @property GradeScale $sePrevgradeRel
 * @property User $seRegistrarApprovedBy
 * @property SemesterRegistration2Detail $seSrd2Rel
 * @property Student $seStdRel
 * @property User $seUpdatedBy
 */
class SupplementaryExam extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'supplementary_exam';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['se_srd2_relId', 'se_std_relId', 'se_prevGrade_relId', 'se_created_at', 'se_created_by'], 'required'],
            [['se_srd2_relId', 'se_std_relId', 'se_prevGrade_relId', 'se_newGrade_relId', 'se_created_by', 'se_updated_by', 'se_registrarApproved', 'se_registrarApproved_by', 'se_hodApproved', 'se_hodApproved_by'], 'integer'],
            [['se_created_at', 'se_updated_at', 'se_registrarApproved_at', 'se_hodApproved_at'], 'safe'],
            [['se_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['se_created_by' => 'id']],
            [['se_hodApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['se_hodApproved_by' => 'id']],
            [['se_newGrade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['se_newGrade_relId' => 'id']],
            [['se_prevGrade_relId'], 'exist', 'skipOnError' => true, 'targetClass' => GradeScale::class, 'targetAttribute' => ['se_prevGrade_relId' => 'id']],
            [['se_registrarApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['se_registrarApproved_by' => 'id']],
            [['se_srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \frontend\models\SemesterRegistration2Detail::class, 'targetAttribute' => ['se_srd2_relId' => 'id']],
            [['se_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['se_std_relId' => 'id']],
            [['se_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['se_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'se_srd2_relId' => Yii::t('rms', 'Se Srd2 Rel ID'),
            'se_std_relId' => Yii::t('rms', 'Name'),
            'se_prevGrade_relId' => Yii::t('rms', 'Prev. Grade'),
            'se_newGrade_relId' => Yii::t('rms', 'New Grade'),
            'se_created_at' => Yii::t('rms', 'Se Created At'),
            'se_created_by' => Yii::t('rms', 'Se Created By'),
            'se_updated_at' => Yii::t('rms', 'Se Updated At'),
            'se_updated_by' => Yii::t('rms', 'Se Updated By'),
            'se_registrarApproved' => Yii::t('rms', 'Registrar Approved'),
            'se_registrarApproved_at' => Yii::t('rms', 'Se Registrar Approved At'),
            'se_registrarApproved_by' => Yii::t('rms', 'Se Registrar Approved By'),
            'se_hodApproved' => Yii::t('rms', 'Hod Approved'),
            'se_hodApproved_at' => Yii::t('rms', 'Se Hod Approved At'),
            'se_hodApproved_by' => Yii::t('rms', 'Se Hod Approved By'),
        ];
    }


    public function saveW($srd2Obj){
        $model = new $this;

        if ($this->isRequested($srd2Obj)) {
            return "Already requested, You have approval pending Supplementary Exam request for this course!.";
        }

        $model->se_std_relId = $srd2Obj->srd2SemregRel->semregStdRel->id;
        $model->se_srd2_relId = $srd2Obj->id;
        $model->se_prevGrade_relId = $srd2Obj->srd2_grade_relId;
        $model->se_created_by = Yii::$app->user->identity->id;
        $model->se_created_at =  date("Y-m-d h:i:s") ;

        return $model->save();
    }

    public function checkRequested($srd2Obj){
        return $this->find()
            ->where(['se_srd2_relId'=>$srd2Obj->id])
            ->andWhere(['OR',['se_registrarApproved'=>NULL],['se_hodApproved'=>NULL]])
            ->all();
    }

    public function isRequested($CCkey){
        $m = $this->checkRequested($CCkey);
        return ($m && count($m) > 0);
    }

    public function generateRegistrarRequestAlert(){

        return $this->find()->where(['se_registrarApproved'=>NULL])->count();
    }

    public function generateHodRequestAlert(){
        // the request should be forwarded to course's department Head.
        $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

        $requests = $this->find()
            ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.id = supplementary_exam.se_srd2_relId')
            ->innerJoin('course','course.id = semester_registration_2_detail.srd2_course_relId')
            ->where([
                'course_dept_relId' => $inst->inst_dep_relId,
                'se_registrarApproved'=>1
            ])
            ->andWhere(['se_hodApproved'=>NULL,])
            ->all();
        if ($requests && count($requests) > 0) {
            foreach ($requests as $SRkey) {
                // if required to direct te requests to students department HOD,
                // replace if condition with :
                // if($SRkey->seStdRel->std_dept_relId == $inst->inst_dep_relId){
                if ($SRkey->seSrd2Rel->srd2CourseRel->course_dept_relId == $inst->inst_dep_relId) {
                    // found One requests for SE of his / her dept course. enough for notifying...
                    return true;
                }
            }
        }
    }

    public function getApprovedRequests(){
        $inst = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
        $reqs  = [];
        $requests = $this->find()
            ->where([
                'se_registrarApproved'=>1,
                'se_hodApproved'=>1,
            ])
            ->all();
        if ($requests && count($requests) > 0) {
            $reqs  = [];
            foreach ($requests as $SRkey) {
                // if required to direct te requests to students department HOD,
                // replace if condition with :
                // if($SRkey->seStdRel->std_dept_relId == $inst->inst_dep_relId){
                if ($SRkey->seSrd2Rel->srd2CourseRel->course_dept_relId == $inst->inst_dep_relId) {
                    // found One requests for SE of his / her dept course. enough for notifying...
                    $reqs[]  = $SRkey;
                }
            }
        }
        return $reqs;
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'se_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeHodApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'se_hodApproved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeNewGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'se_newGrade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSePrevGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'se_prevGrade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeRegistrarApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'se_registrarApproved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeSrd2Rel()
    {
        return $this->hasOne(\frontend\models\SemesterRegistration2Detail::class, ['id' => 'se_srd2_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'se_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'se_updated_by']);
    }
}
