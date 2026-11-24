<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "w_student_readmission_history".
 *
 * @property integer $id
 * @property integer $srh_acadYear_relId
 * @property integer $srh_sem_relId
 * @property integer $srh_swh_relId
 * @property integer $srh_std_relId
 * @property string $srh_return_back_on
 * @property integer $srh_HOD_approved
 * @property string $srh_HOD_approved_at 
 * @property integer $srh_HOD_approved_by
 * @property integer $srh_approved
 * @property string $srh_approved_at
 * @property integer $srh_approved_by
 * @property string $srh_return_back_letter
 * @property string $remark_HOD
 * @property string $remark_registrar
 */
class WStudentReadmissionHistory extends \yii\db\ActiveRecord
{

    public $applyForm;
    public $readmisionApplicationFile;
    public $file;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_student_readmission_history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_return_back_on'], 'required'],
            [['srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_HOD_approved', 'srh_HOD_approved_by', 'srh_approved', 'srh_approved_by','srh_created_by','srh_updated_by'], 'integer'],
            [['srh_return_back_on', 'srh_updated_at','srh_created_at', 'srh_HOD_approved_at', 'srh_approved_at'], 'safe'],
            [['remark_HOD', 'remark_registrar'], 'string'],
            [['srh_return_back_letter'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'srh_acadYear_relId' => Yii::t('rms', 'Acad Year'),
            'srh_sem_relId' => Yii::t('rms', 'Semester'),
            'srh_swh_relId' => Yii::t('rms', 'Withdrawal Entry'),
            'srh_std_relId' => Yii::t('rms', 'Student ID'),
            'srh_return_back_on' => Yii::t('rms', 'Return Back On'),
            'srh_HOD_approved' => Yii::t('rms', 'HOD. Approved'),
            'srh_HOD_approved_at' => Yii::t('rms', 'HOD. Approved At'),
            'srh_HOD_approved_by' => Yii::t('rms', 'HOD. Approved By'),
            'srh_approved' => Yii::t('rms', 'Approved'),
            'srh_approved_at' => Yii::t('rms', 'Approved At'),
            'srh_approved_by' => Yii::t('rms', 'Approved By'),
            'srh_return_back_letter' => Yii::t('rms', 'Return Back Letter'),
            'remark_HOD' => Yii::t('rms', 'Remark  HOD'),
            'remark_registrar' => Yii::t('rms', 'Remark Registrar'),
        ];
    }

    public function checkReadmitted($cGset,$std){
        // check if the student is reradmitted for soecified semester adn Academic year.

        return $this->find()
            ->where([
                'srh_acadYear_relId'=>$cGset->gs_acadYear_relId,
                'srh_sem_relId'=>$cGset->gs_sem_relId,
                'srh_std_relId'=>$std->id,
            ])
            ->andWhere([
                'srh_approved'=>1,
                'srh_HOD_approved'=>1,
            ])
            ->one();
    }

    public function checkHodRequest(){
        if (Yii::$app->user->can('DepartmentHead')) {
            $instdept = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            return $this->find()
                ->innerJoin('student','student.std_dept_relId = w_student_readmission_history.srh_std_relId')
                ->where([
                    'srh_approved'=>1,
                    'srh_HOD_approved'=>NULL,
                ])
                ->andWhere(['std_dept_relId'=>$instdept])
                ->count();
        }
    }

    public function hasValidWithdrawalrequest($stdObj){
        $swhObj = new WStudentWithdrawalHistory();

        if ($swhObj->hasValidWithdrawal($stdObj)) {
            return $this;
        }else{
            return NULL;
        }
        
    }

    public function checkRegistrarRequest(){
        if (Yii::$app->user->can('Registrar')) {
            return $this->find()
                ->where([
                    'srh_approved'=>NULL,
                ])
                ->count();
        }
    } 
}
