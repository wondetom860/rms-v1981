<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "sec_spec_recomend".
 *
 * @property integer $id
 * @property integer $ssr_acadYear_relId
 * @property integer $ssr_sem_relId
 * @property integer $ssr_adm_relId
 * @property integer $ssr_prog_relId
 * @property integer $ssr_dept_relId
 * @property integer $ssr_clslvl_relId
 * @property integer $ssr_spec_relId
 * @property string $ssr_rec_spec
 * @property string $ssr_rec_section
 * @property integer $ssr_created_by
 * @property string $ssr_created_at
 * @property integer $ssr_updated_by
 * @property string $ssr_updated_at
 * @property string $ssr_stdList
 *
 * @property SchoolYear $ssrAcadYearRel
 * @property Admission $ssrAdmRel
 * @property ClassLevel $ssrClslvlRel
 * @property User $ssrCreatedBy
 * @property Department $ssrDeptRel
 * @property Program $ssrProgRel
 * @property Semester $ssrSemRel
 * @property Specialization $ssrSpecRel
 * @property User $ssrUpdatedBy
 */
class SecSpecRecomend extends \yii\db\ActiveRecord
{

    public $recommend = NULL;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'sec_spec_recomend';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ssr_acadYear_relId', 'ssr_sem_relId', 'ssr_adm_relId', 'ssr_prog_relId', 'ssr_dept_relId', 'ssr_clslvl_relId', 'ssr_created_by', 'ssr_created_at'], 'required'],
            [['ssr_acadYear_relId', 'ssr_sem_relId', 'ssr_adm_relId', 'ssr_prog_relId', 'ssr_dept_relId', 'ssr_clslvl_relId', 'ssr_spec_relId', 'ssr_created_by', 'ssr_updated_by','ssr_rec_spec',], 'integer'],
            [['ssr_created_at', 'ssr_updated_at'], 'safe'],
            [[ 'ssr_rec_section'], 'string', 'max' => 20],
            [['ssr_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['ssr_acadYear_relId' => 'id']],
            [['ssr_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::className(), 'targetAttribute' => ['ssr_adm_relId' => 'id']],
            [['ssr_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::className(), 'targetAttribute' => ['ssr_clslvl_relId' => 'id']],
            [['ssr_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ssr_created_by' => 'id']],
            [['ssr_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['ssr_dept_relId' => 'id']],
            [['ssr_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::className(), 'targetAttribute' => ['ssr_prog_relId' => 'id']],
            [['ssr_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['ssr_sem_relId' => 'id']],
            [['ssr_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::className(), 'targetAttribute' => ['ssr_spec_relId' => 'id']],
            [['ssr_rec_spec'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::className(), 'targetAttribute' => ['ssr_rec_spec' => 'id']],
            [['ssr_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['ssr_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ssr_acadYear_relId' => Yii::t('rms', 'Ssr Acad Year Rel ID'),
            'ssr_sem_relId' => Yii::t('rms', 'Ssr Sem Rel ID'),
            'ssr_adm_relId' => Yii::t('rms', 'Ssr Adm Rel ID'),
            'ssr_prog_relId' => Yii::t('rms', 'Ssr Prog Rel ID'),
            'ssr_dept_relId' => Yii::t('rms', 'Ssr Dept Rel ID'),
            'ssr_clslvl_relId' => Yii::t('rms', 'Ssr Clslvl Rel ID'),
            'ssr_spec_relId' => Yii::t('rms', 'Ssr Spec Rel ID'),
            'ssr_rec_spec' => Yii::t('rms', 'Ssr Rec Spec'),
            'ssr_rec_section' => Yii::t('rms', 'Ssr Rec Section'),
            'ssr_created_by' => Yii::t('rms', 'Ssr Created By'),
            'ssr_created_at' => Yii::t('rms', 'Ssr Created At'),
            'ssr_updated_by' => Yii::t('rms', 'Ssr Updated By'),
            'ssr_updated_at' => Yii::t('rms', 'Ssr Updated At'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'ssr_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrAdmRel()
    {
        return $this->hasOne(Admission::className(), ['id' => 'ssr_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrClslvlRel()
    {
        return $this->hasOne(ClassLevel::className(), ['id' => 'ssr_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ssr_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrDeptRel()
    {
        return $this->hasOne(Department::className(), ['id' => 'ssr_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrProgRel()
    {
        return $this->hasOne(Program::className(), ['id' => 'ssr_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'ssr_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrSpecRel()
    {
        return $this->hasOne(Specialization::className(), ['id' => 'ssr_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSsrUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'ssr_updated_by']);
    }
}
