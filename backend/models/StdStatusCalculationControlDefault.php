<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "std_status_calculation_control_default".
 *
 * @property integer $id
 * @property integer $sssccd_adm_relId
 * @property integer $sssccd_prog_relId
 * @property integer $sssccd_sssn_relId
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
 * @property Program $sssccdProgRel
 * @property StudentSemesterStatusName $sssccdSssnRel
 * @property User $sssccdUpdatedBy
 * @property User $sssccdCreatedBy
 * @property ClassLevel $sssccdClslvlRel
 */
class StdStatusCalculationControlDefault extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'std_status_calculation_control_default';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sssccd_adm_relId', 'sssccd_prog_relId', 'sssccd_sssn_relId', 'sssccd_max_CGPA', 'sssccd_min_CGPA', 'sssccd_created_by', 'sssccd_created_at', 'sssccd_updated_at', 'sssccd_max_SGPA', 'sssccd_min_SGPA', 'sssccd_clslvl_relId'], 'required'],
            [['sssccd_adm_relId', 'sssccd_prog_relId', 'sssccd_sssn_relId', 'sssccd_created_by', 'sssccd_updated_by', 'sssccd_clslvl_relId'], 'integer'],
            [['sssccd_max_CGPA', 'sssccd_min_CGPA', 'sssccd_max_SGPA', 'sssccd_min_SGPA'], 'number'],
            [['sssccd_remark'], 'string'],
            [['sssccd_created_at', 'sssccd_updated_at','sssccdAdmRel.admission_name',], 'safe'],
            [['sssccd_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['sssccd_adm_relId' => 'id']],
            [['sssccd_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['sssccd_prog_relId' => 'id']],
            [['sssccd_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sssccd_sem_relId' => 'id']],
            [['sssccd_sssn_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentSemesterStatusName::class, 'targetAttribute' => ['sssccd_sssn_relId' => 'id']],
            [['sssccd_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccd_updated_by' => 'id']],
            [['sssccd_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccd_created_by' => 'id']],
            [['sssccd_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['sssccd_clslvl_relId' => 'id']],
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
            'sssccdAdmRel.admission_name' => Yii::t('rms','Admission'),
            'sssccdProgRel.program_name' =>'Program' ,
            'sssccdSemRel.semester_name' =>'Semester' ,
            'sssccdSssnRel.sssn_name' =>'Sem. Status' ,
            'sssccdClslvlRel.clslvl_num' =>'Year' ,
            'sssccdCreatedBy.username' => 'Creted By'
        ];
    }

    public function getControlSetup($gsetObject,$statusObject){

        $clslvlObject = $gsetObject->gsClslvlRel;
        if ($clslvlObject->clslvl_num > 3) {
            $clslvlObject = ClassLevel::findOne([
                'clslvl_admission_relId' => $gsetObject->gs_adm_relId,
                'clslvl_program_relId' => $gsetObject->gs_prog_relId,
                'clslvl_num' => 3,
            ]);
        }
        // check Ac control setup if any exists

        $setupAc = StdStatusCalculationControlAc::find()
            ->where(['OR',
                ['sssccAC_adm_relId' => $gsetObject->gs_adm_relId,'sssccAC_adm_relId' => NULL],
                ['sssccAC_prog_relId' => $gsetObject->gs_prog_relId,'sssccAC_prog_relId' => NULL],
                ['sssccAC_clslvl_relId' => $clslvlObject->id,'sssccAC_clslvl_relId' => NULL],
                ['sssccAC_sem_relId' => $gsetObject->gs_sem_relId,'sssccAC_sem_relId' => NULL],
            ])->andWhere(['sssccAC_sssn_relId' => $statusObject->id])
            ->andWhere(['sssccAC_acadYear_relId' => $gsetObject->gs_acadYear_relId])
            ->one();

        if ($setupAc) {
            return [$setupAc,'ac'];
        }

        // else if it doesn't exists, check default
        return [$this->find()->where(['OR',
            ['sssccd_adm_relId' => $gsetObject->gs_adm_relId,'sssccd_adm_relId' => NULL],
            ['sssccd_prog_relId' => $gsetObject->gs_prog_relId,'sssccd_prog_relId' => NULL],
            ['sssccd_clslvl_relId' => $clslvlObject->id,'sssccd_clslvl_relId' => NULL],
            ['sssccd_sem_relId' => $gsetObject->gs_sem_relId,'sssccd_sem_relId' => NULL],
        ])->andWhere(['sssccd_sssn_relId' => $statusObject->id])->one(),'d'];
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
    public function getSssccdSssnRel()
    {
        return $this->hasOne(StudentSemesterStatusName::class, ['id' => 'sssccd_sssn_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccdUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssccd_updated_by']);
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
    public function getSssccdClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'sssccd_clslvl_relId']);
    }
}
