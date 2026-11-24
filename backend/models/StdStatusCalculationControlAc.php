<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "std_status_calculation_control_ac".
 *
 * @property integer $id
 * @property integer $sssccAC_acadYear_relId
 * @property integer $sssccAC_Fs
 * @property integer $sssccAC_sem_relId
 * @property integer $sssccAC_adm_relId
 * @property integer $sssccAC_prog_relId
 * @property integer $sssccAC_sssn_relId
 * @property double $sssccAC_max_CGPA
 * @property double $sssccAC_min_CGPA
 * @property double $sssccAC_max_SGPA
 * @property double $sssccAC_min_SGPA
 * @property integer $sssccAC_clslvl_relId
 * @property string $sssccAC_remark
 * @property integer $sssccAC_created_by
 * @property string $sssccAC_created_at
 * @property string $sssccAC_updated_at
 * @property integer $sssccAC_updated_by
 *
 * @property SchoolYear $sssccACAcadYearRel
 * @property Semester $sssccACSemRel
 * @property Admission $sssccACAdmRel
 * @property Program $sssccACProgRel
 * @property ClassLevel $sssccACClslvlRel
 * @property User $sssccACCreatedBy
 * @property User $sssccACUpdatedBy
 * @property StudentSemesterStatusName $sssccACSssnRel
 */
class StdStatusCalculationControlAc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'std_status_calculation_control_ac';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sssccAC_acadYear_relId', 'sssccAC_Fs', 'sssccAC_sem_relId', 'sssccAC_adm_relId', 'sssccAC_prog_relId', 'sssccAC_sssn_relId', 'sssccAC_max_CGPA', 'sssccAC_min_CGPA', 'sssccAC_max_SGPA', 'sssccAC_min_SGPA', 'sssccAC_clslvl_relId', 'sssccAC_created_by', 'sssccAC_created_at', 'sssccAC_updated_at', 'sssccAC_updated_by'], 'required'],
            [['sssccAC_acadYear_relId', 'sssccAC_Fs', 'sssccAC_sem_relId', 'sssccAC_adm_relId', 'sssccAC_prog_relId', 'sssccAC_sssn_relId', 'sssccAC_clslvl_relId', 'sssccAC_created_by', 'sssccAC_updated_by'], 'integer'],
            [['sssccAC_max_CGPA', 'sssccAC_min_CGPA', 'sssccAC_max_SGPA', 'sssccAC_min_SGPA'], 'number'],
            [['sssccAC_remark'], 'string'],
            [['sssccAC_created_at', 'sssccAC_updated_at'], 'safe'],
            [['sssccAC_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['sssccAC_acadYear_relId' => 'id']],
            [['sssccAC_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sssccAC_sem_relId' => 'id']],
            [['sssccAC_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['sssccAC_adm_relId' => 'id']],
            [['sssccAC_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['sssccAC_prog_relId' => 'id']],
            [['sssccAC_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['sssccAC_clslvl_relId' => 'id']],
            [['sssccAC_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccAC_created_by' => 'id']],
            [['sssccAC_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['sssccAC_updated_by' => 'id']],
            [['sssccAC_sssn_relId'], 'exist', 'skipOnError' => true, 'targetClass' => StudentSemesterStatusName::class, 'targetAttribute' => ['sssccAC_sssn_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'sssccAC_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'sssccAC_Fs' => Yii::t('rms', ' Fs'),
            'sssccAC_sem_relId' => Yii::t('rms', 'Sem.'),
            'sssccAC_adm_relId' => Yii::t('rms', 'Adm.'),
            'sssccAC_prog_relId' => Yii::t('rms', 'Prog.'),
            'sssccAC_sssn_relId' => Yii::t('rms', 'Sssn.'),
            'sssccAC_max_CGPA' => Yii::t('rms', 'Max  CGPA'),
            'sssccAC_min_CGPA' => Yii::t('rms', 'Min  CGPA'),
            'sssccAC_max_SGPA' => Yii::t('rms', 'Max  SGPA'),
            'sssccAC_min_SGPA' => Yii::t('rms', 'Min  SGPA'),
            'sssccAC_clslvl_relId' => Yii::t('rms', 'Clslvl.'),
            'sssccAC_remark' => Yii::t('rms', 'Remark'),
            'sssccAC_created_by' => Yii::t('rms', 'Created By'),
            'sssccAC_created_at' => Yii::t('rms', 'Created At'),
            'sssccAC_updated_at' => Yii::t('rms', 'Updated At'),
            'sssccAC_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'sssccAC_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'sssccAC_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'sssccAC_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'sssccAC_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'sssccAC_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssccAC_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'sssccAC_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSssccACSssnRel()
    {
        return $this->hasOne(StudentSemesterStatusName::class, ['id' => 'sssccAC_sssn_relId']);
    }
}
