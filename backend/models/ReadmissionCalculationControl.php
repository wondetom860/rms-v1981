<?php

namespace backend\models;
use common\components\CommonControllerFunctions as CCF;

use Yii;

/**
 * This is the model class for table "readmission_calculation_control".
 *
 * @property integer $id
 * @property integer $sssccd_adm_relId
 * @property integer $sssccd_prog_relId
 * @property integer $sssccd_sem_relId
 * @property double $sssccd_max_CGPA
 * @property double $sssccd_min_CGPA
 * @property string $sssccd_remark
 * @property integer $sssccd_created_by
 * @property string $sssccd_created_at
 * @property string $sssccd_updated_at
 * @property integer $sssccd_updated_by
 * @property double $sssccd_max_SGPA
 * @property double $sssccd_min_SGPA
 * @property integer $sssccd_clslvl_relId
 *
 * @property Admission $sssccdAdmRel
 * @property ClassLevel $sssccdClslvlRel
 * @property User $sssccdCreatedBy
 * @property Program $sssccdProgRel
 * @property Semester $sssccdSemRel
 * @property User $sssccdUpdatedBy
 */
class ReadmissionCalculationControl extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'readmission_calculation_control';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sssccd_adm_relId', 'sssccd_prog_relId', 'sssccd_sem_relId', 'sssccd_max_CGPA', 'sssccd_min_CGPA', 'sssccd_created_by', 'sssccd_created_at', 'sssccd_updated_at', 'sssccd_max_SGPA', 'sssccd_min_SGPA', 'sssccd_clslvl_relId'], 'required'],
            [['sssccd_adm_relId', 'sssccd_prog_relId', 'sssccd_sem_relId', 'sssccd_created_by', 'sssccd_updated_by', 'sssccd_clslvl_relId'], 'integer'],
            [['sssccd_max_CGPA', 'sssccd_min_CGPA', 'sssccd_max_SGPA', 'sssccd_min_SGPA'], 'number'],
            [['sssccd_remark'], 'string'],
            [['sssccd_created_at', 'sssccd_updated_at'], 'safe'],
            [['sssccd_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['sssccd_adm_relId' => 'id']],
            [['sssccd_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['sssccd_clslvl_relId' => 'id']],
            [['sssccd_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccd_created_by' => 'id']],
            [['sssccd_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['sssccd_prog_relId' => 'id']],
            [['sssccd_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sssccd_sem_relId' => 'id']],
            [['sssccd_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccd_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
     public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sssccd_adm_relId' => Yii::t('rms', 'Admission'),
            'sssccd_prog_relId' => Yii::t('rms', 'Program'),
            'sssccd_sem_relId' => Yii::t('rms', 'Semester'),
            'sssccd_sssn_relId' => Yii::t('rms', 'Sem. Status'),
            'sssccd_max_CGPA' => Yii::t('rms', 'Max. CGPA'),
            'sssccd_min_CGPA' => Yii::t('rms', 'Min. CGPA'),
            'sssccd_remark' => Yii::t('rms', 'Remark'),
            'sssccd_created_by' => Yii::t('rms', 'Created By'),
            'sssccd_created_at' => Yii::t('rms', 'Created At'),
            'sssccd_updated_at' => Yii::t('rms', 'Updated At'),
            'sssccd_updated_by' => Yii::t('rms', 'Updated By'),
            'sssccd_max_SGPA' => Yii::t('rms', 'Max. SGPA'),
            'sssccd_min_SGPA' => Yii::t('rms', 'Min. SGPA'),
            'sssccd_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'sssccdClslvlRel.clslvl_num' => Yii::t('rms', 'Class Level'),
            'sssccdAdmRel.admission_name' => 'Admission',
            'sssccdProgRel.program_name' =>'Program' ,
            // 'sssccdClslvlRel.clslvl_num' =>'Year' ,
            'sssccdCreatedBy.username' => 'Creted By'
        ];
    }

    public function checkREControl($stdObj,$gsetObj,$thiss=NULL){//this indicates the application is for this semester.
        $gsetObjNew = new WGroupSetup();
        $semregObj = new \frontend\models\SemesterRegistration2();
        $srd2Obj = new \frontend\models\SemesterRegistration2Detail();

        if (CCF::specialCaseCheckP($gsetObj,$stdObj)) {
            if (is_null($thiss)) {
                return false;
            }
            
        }
            
        $prevGset = is_null($thiss)?$gsetObjNew->getPrevStudentGset($stdObj,$gsetObj):$gsetObj;

        // var_dump( $prevGset);die();
        if ($prevGset) {
            // get Semester status of the student in that semsetr.

            // if student have semester status of ACADEMIC_DISMISSAL :: he can Apply for withdrawal
            $prevSemStatus = $srd2Obj->getSemregRecord($stdObj->id,$prevGset->gs_clslvl_relId,$prevGset->gs_sem_relId,$prevGset,1,1);

            if (isset($prevSemStatus[5]) && ($prevSemStatus[5] == 2 || $prevSemStatus[5] == 1)) {
                if ($prevSemStatus[5] == 2) {
                    // $this->semester student status is warning... 
                    // chech if it is consucative... warning - > warning => academic dismissal
                    $contWarning = $srd2Obj->checkContinousWrning($gsetObjNew->getPrevStudentGset($stdObj,$gsetObj),$stdObj);
                    if (!$contWarning) {
                        return "You have semsetr status of <b>PASS</b>, You can NOT apply for withdrawal with this status";
                    }
                }
                // in case if decided to allow students with semsetr status of 'WARNING' to request withdrawal,
                // check this OR case above.
            }


            $psemStatus = $semregObj->getSemesterGrade($stdObj,$prevGset);
            if ($psemStatus) {
                $CGPA = $psemStatus->semreg_CGPA;
                $SGPA = $psemStatus->semreg_SGPA;
                // getSemregRecord($stdId,$clslvlId,$semesterId,$cGset,$forChange = NULL)

                $mappedClslvl = CCF::mapClassLevelP($prevGset->gsClslvlRel,$prevGset->gsSemRel);

                // echo "<pre>";
                // var_dump($prevGset->gsClslvlRel,$prevGset->gsSemRel);
                // echo "<hr>";
                // var_dump($mappedClslvl);
                // die();
                if (CCF::specialCaseCheckP($prevGset)) {
                    $control = $this->find()
                        ->where([
                            'sssccd_adm_relId'=>$prevGset->gs_adm_relId,
                            'sssccd_prog_relId'=>$prevGset->gs_prog_relId,
                            'sssccd_sem_relId'=>$mappedClslvl[1]->id,
                            'sssccd_clslvl_relId'=>$mappedClslvl[0]->id,
                        ])
                        ->andWhere(['>=','sssccd_max_SGPA',$SGPA])
                        ->andWhere(['<=','sssccd_min_SGPA',$SGPA])
                        ->one();
                }else{
                    $control = $this->find()
                        ->where([
                            'sssccd_adm_relId'=>$prevGset->gs_adm_relId,
                            'sssccd_prog_relId'=>$prevGset->gs_prog_relId,
                            'sssccd_sem_relId'=>$mappedClslvl[1]->id,
                            'sssccd_clslvl_relId'=>$mappedClslvl[0]->id,
                        ])
                        ->andWhere(['>=','sssccd_max_SGPA',$SGPA])
                        ->andWhere(['<=','sssccd_min_SGPA',$SGPA])
                        ->andWhere(['>=','sssccd_max_CGPA',$CGPA])
                        ->andWhere(['<=','sssccd_min_CGPA',$CGPA])
                        ->one();
                }
                if ($control) {
                    // $RPGCase = strtoupper($prevGset->gsProgRel->program_name == "MASTERS") && strtoupper($prevSemStatus[2]) == 'PENDING';

                    // echo "<pre>";
                    // var_dump($prevSemStatus[5]);
                    // die();

                    if ($prevSemStatus[5] == 3 || $prevSemStatus[5] == 2) {//both warning and dismissal handles for interval case
                        //fail case bu allowed to readmit.
                       // return 1; 
                       if (Yii::$app->user->can('Student')) {

                            $wdReason = WithdrawalReason::findOne([
                                'withdrawalres_type'=>'NORMAL',
                                'withdrawalres_description'=>'Academic Dismissal(R)',
                            ]);
                            if (!($wdReason)) {
                                die("Configuration Error!, Withdrawal Reason Named [Academic Dismissal(R)] not found.");
                            }

                            $exist = WStudentWithdrawalHistory::find()
                                ->where([
                                    'swh_created_by'=>Yii::$app->user->identity->id,
                                    'swh_acadYear_relId'=>$prevGset->gs_acadYear_relId,
                                    'swh_sem_relId'=>$prevGset->gs_sem_relId,
                                    'swh_std_relId'=>$stdObj->id,
                                    'swh_reason_relId'=>$wdReason->id,                                    
                                ])
                                ->andWhere(['OR',['swh_lastApproved'=>NULL,],['swh_lastApproved'=>1]])
                                ->andWhere(['OR',['swh_HOD_approved'=>NULL,],['swh_HOD_approved'=>1]])
                                ->one();
                            if ($exist) {
                                return $exist;
                            }
                            $modelWD = new WStudentWithdrawalHistory();
                            $modelWD->swh_std_relId = $stdObj->id;
                            $modelWD->swh_acadYear_relId = $prevGset->gs_acadYear_relId;
                            $modelWD->swh_sem_relId = $prevGset->gs_sem_relId;

                            $modelWD->swh_created_by = Yii::$app->user->identity->id;
                            $modelWD->swh_created_at = date('Y-m-d h:i:s');
                            $modelWD->swh_course_removed = 0;
                            $modelWD->swh_stdSigned = 0;
                            $modelWD->swh_reason_relId = $wdReason->id;

                            $modelWD->swh_HOD_approved = NULL;
                            $modelWD->swh_lastApproved = NULL;

                            if ($modelWD->save()) {
                                return $modelWD;
                            }

                        echo "<pre>";
                        var_dump($modelWD->getErrors());
                        die();
                       }
                    }else{
                        return "You are Not Eligible for requesting Withdrawal. Please contact registrar if this is error(W_8089).";
                    }                    
                }else{
                    // echo "<pre>";
                    // var_dump($psemStatus);die();
                    return "Minimum requirement for readmission is NOT satisfied.";
                }                    
            }else{
                return "Semester Grade Calculation NOT completed yet.";
            }
        }else{
            return "Semester status Uknown.";
        }
    }

    public function checkIfReadmissionPossible($semregModel){
        // check against readmision control:
        $gset = $semregModel->semregStdRel->stdGroupReal;
        $mappedClslvl = CCF::mapClassLevelP($gset->gsClslvlRel,$gset->gsSemRel);
        if (CCF::specialCaseCheckP($gset)) {
            $control = $this->find()
                ->where([
                    'sssccd_adm_relId'=>$gset->gs_adm_relId,
                    'sssccd_prog_relId'=>$gset->gs_prog_relId,
                    'sssccd_sem_relId'=>$mappedClslvl[1]->id,
                    'sssccd_clslvl_relId'=>$mappedClslvl[0]->id,
                ])
                ->andWhere(['>=','sssccd_max_SGPA',$semregModel->semreg_SGPA])
                ->andWhere(['<=','sssccd_min_SGPA',$semregModel->semreg_SGPA])
                ->one();
        }else{
            $control = $this->find()
                ->where([
                    'sssccd_adm_relId'=>$gset->gs_adm_relId,
                    'sssccd_prog_relId'=>$gset->gs_prog_relId,
                    'sssccd_sem_relId'=>$mappedClslvl[1]->id,
                    'sssccd_clslvl_relId'=>$mappedClslvl[0]->id,
                ])
                ->andWhere(['>=','sssccd_max_SGPA',$semregModel->semreg_SGPA])
                ->andWhere(['<=','sssccd_min_SGPA',$semregModel->semreg_SGPA])
                ->andWhere(['>=','sssccd_max_CGPA',$semregModel->semreg_CGPA])
                ->andWhere(['<=','sssccd_min_CGPA',$semregModel->semreg_CGPA])
                ->one();
        }

        if ($control) {
            \Yii::$app->session->setFlash('type','info');
            \Yii::$app->session->setFlash('message','Course enrolled will NOT be removed. >> Completed semester.');
        }else{
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message',"SGPA and/ CGPA is out of allowed readmission interval. SGPA: {$semregModel->semreg_SGPA}, CGPA: {$semregModel->semreg_CGPA}");
        }
        return !is_null($control);

    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'sssccd_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'sssccd_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssccd_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'sssccd_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'sssccd_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssccd_updated_by']);
    }
}
