<?php

namespace frontend\modules\WithdrawalReadmission\models;

use Yii;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\Student;
use backend\models\User;
use backend\models\WithdrawalReason;

/**
 * This is the model class for table "w_student_readmission_history".
 *
 * @property int $id
 * @property int $srh_acadYear_relId
 * @property int $srh_sem_relId
 * @property int $srh_swh_relId
 * @property int $srh_std_relId
 * @property string $srh_return_back_on
 * @property int|null $srh_HOD_approved
 * @property string|null $srh_HOD_approved_at
 * @property int|null $srh_HOD_approved_by
 * @property int|null $srh_approved
 * @property string|null $srh_approved_at
 * @property int|null $srh_approved_by
 * @property string|null $srh_return_back_letter
 * @property string|null $remark_HOD
 * @property string|null $remark_registrar
 * @property string $srh_created_at
 * @property string|null $srh_updated_at
 * @property int $srh_created_by
 * @property int|null $srh_updated_by
 *
 * @property SchoolYear $srhAcadYearRel
 * @property User $srhApprovedBy
 * @property User $srhHODApprovedBy
 * @property Semester $srhSemRel
 * @property User $srhUpdatedBy
 * @property WStudentWithdrawalHistory $srhSwhRel
 */
class WStudentReadmissionHistory extends \yii\db\ActiveRecord
{

    public $std_name;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'w_student_readmission_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_return_back_on', 'srh_created_at', 'srh_created_by'], 'required'],
            [['srh_acadYear_relId', 'srh_sem_relId', 'srh_swh_relId', 'srh_std_relId', 'srh_HOD_approved', 'srh_HOD_approved_by', 'srh_approved', 'srh_approved_by', 'srh_created_by', 'srh_updated_by'], 'integer'],
            [['srh_return_back_on', 'srh_HOD_approved_at', 'srh_approved_at', 'srh_created_at', 'srh_updated_at'], 'safe'],
            [['remark_HOD', 'remark_registrar','std_name'], 'string'],
            [['srh_return_back_letter'], 'string', 'max' => 255],
            [['srh_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['srh_acadYear_relId' => 'id']],
            [['srh_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['srh_approved_by' => 'id']],
            [['srh_HOD_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['srh_HOD_approved_by' => 'id']],
            [['srh_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['srh_sem_relId' => 'id']],
            [['srh_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['srh_updated_by' => 'id']],
            [['srh_swh_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WStudentWithdrawalHistory::className(), 'targetAttribute' => ['srh_swh_relId' => 'id']],
            [['srh_std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['srh_std_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'std_name' => Yii::t('rms', 'Student Name'),
            'srh_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'srh_sem_relId' => Yii::t('rms', 'Semester'),
            'srh_swh_relId' => Yii::t('rms', 'Withdrawal Entry'),
            'srh_std_relId' => Yii::t('rms', 'Student Name'),
            'srh_return_back_on' => Yii::t('rms', 'Return Back On'),
            'srh_HOD_approved' => Yii::t('rms', 'Hod Approved'),
            'srh_HOD_approved_at' => Yii::t('rms', 'Hod Approved At'),
            'srh_HOD_approved_by' => Yii::t('rms', 'Hod Approved By'),
            'srh_approved' => Yii::t('rms', 'Approved'),
            'srh_approved_at' => Yii::t('rms', 'Approved At'),
            'srh_approved_by' => Yii::t('rms', 'Approved By'),
            'srh_return_back_letter' => Yii::t('rms', 'Return Back Letter'),
            'remark_HOD' => Yii::t('rms', 'Remark Hod'),
            'remark_registrar' => Yii::t('rms', 'Remark Registrar'),
            'srh_created_at' => Yii::t('rms', 'Created At'),
            'srh_updated_at' => Yii::t('rms', 'Updated At'),
            'srh_created_by' => Yii::t('rms', 'Created By'),
            'srh_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function getAvtiveSections(){
        // return all gsets created for the classlevel from whic he/she has left:
        // class level can be obtained from the swh entry:
        return $this->srhSwhRel->sectionRel ? 
        \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId' => $this->srh_acadYear_relId,
                'gs_sem_relId' => $this->srh_sem_relId,
                'gs_clslvl_relId' => $this->srhSwhRel->sectionRel->gasGsetRel->gs_clslvl_relId,
            ])
            ->all(): 
        \backend\models\WGroupSetup::find()
                ->where([
                    'gs_acadYear_relId' => $this->srh_acadYear_relId,
                    'gs_sem_relId' => $this->srh_sem_relId,
                ])
                ->all()
            ;
    }
    public function getJoinedSection(){
        // find the section to which the student is assigned after readmission.
        $modelgas = new \backend\models\WGroupAssignment();
        return $modelgas->getStudentGroupAssignment($this->srhAcadYearRel,$this->srhSemRel,$this->srhStdRel);
    }

    /**
     * Gets query for [[SrhAcadYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'srh_acadYear_relId']);
    }

    /**
     * Gets query for [[SrhApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'srh_approved_by']);
    }

    /**
     * Gets query for [[SrhHODApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhHODApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'srh_HOD_approved_by']);
    }

    /**
     * Gets query for [[SrhSemRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'srh_sem_relId']);
    }

    /**
     * Gets query for [[SrhUpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'srh_updated_by']);
    }

    /**
     * Gets query for [[SrhUpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'srh_std_relId']);
    }

    /**
     * Gets query for [[SrhSwhRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSrhSwhRel()
    {
        return $this->hasOne(WStudentWithdrawalHistory::className(), ['id' => 'srh_swh_relId']);
    }
}
