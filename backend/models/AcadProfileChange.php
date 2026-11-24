<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "acad_profile_change".
 *
 * @property integer $id
 * @property integer $apc_std_relId
 * @property string $apc_changed
 * @property integer $apc_prevDept_relId
 * @property integer $apc_prevSpec_relId
 * @property integer $apc_newDept_relId
 * @property integer $apc_newSpec_relId
 * @property integer $apc_registrarApproved
 * @property string $apc_registrarApproved_at
 * @property integer $apc_registrarApproved_by
 * @property string $apc_created_at
 * @property integer $apc_created_by
 * @property string $apc_updated_at
 * @property integer $apc_updated_by
 *
 * @property User $apcCreatedBy
 * @property Department $apcNewDeptRel
 * @property Specialization $apcNewSpecRel
 * @property Department $apcPrevDeptRel
 * @property Specialization $apcPrevSpecRel
 * @property User $apcRegistrarApprovedBy
 * @property User $apcUpdatedBy
 */
class AcadProfileChange extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'acad_profile_change';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['apc_std_relId', 'apc_created_at', 'apc_created_by'], 'required'],
            [['apc_std_relId', 'apc_prevDept_relId', 'apc_prevSpec_relId', 'apc_newDept_relId', 'apc_newSpec_relId', 'apc_registrarApproved', 'apc_registrarApproved_by', 'apc_created_by', 'apc_updated_by'], 'integer'],
            [['apc_changed'], 'string'],
            [['apc_registrarApproved_at', 'apc_created_at', 'apc_updated_at'], 'safe'],
            [['apc_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['apc_created_by' => 'id']],
            [['apc_newDept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['apc_newDept_relId' => 'id']],
            [['apc_newSpec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::className(), 'targetAttribute' => ['apc_newSpec_relId' => 'id']],
            [['apc_prevDept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['apc_prevDept_relId' => 'id']],
            [['apc_prevSpec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::className(), 'targetAttribute' => ['apc_prevSpec_relId' => 'id']],
            [['apc_registrarApproved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['apc_registrarApproved_by' => 'id']],
            [['apc_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['apc_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'apc_std_relId' => Yii::t('rms', 'Apc Std Rel ID'),
            'apc_changed' => Yii::t('rms', 'Apc Changed'),
            'apc_prevDept_relId' => Yii::t('rms', 'Apc Prev Dept Rel ID'),
            'apc_prevSpec_relId' => Yii::t('rms', 'Apc Prev Spec Rel ID'),
            'apc_newDept_relId' => Yii::t('rms', 'Apc New Dept Rel ID'),
            'apc_newSpec_relId' => Yii::t('rms', 'Apc New Spec Rel ID'),
            'apc_registrarApproved' => Yii::t('rms', 'Apc Registrar Approved'),
            'apc_registrarApproved_at' => Yii::t('rms', 'Apc Registrar Approved At'),
            'apc_registrarApproved_by' => Yii::t('rms', 'Apc Registrar Approved By'),
            'apc_created_at' => Yii::t('rms', 'Apc Created At'),
            'apc_created_by' => Yii::t('rms', 'Apc Created By'),
            'apc_updated_at' => Yii::t('rms', 'Apc Updated At'),
            'apc_updated_by' => Yii::t('rms', 'Apc Updated By'),
        ];
    }

    public function getRegistrarAlert(){
        return $this->find()
            ->where(['apc_registrarApproved'=>NULL])
            ->count();
    }

    public function getAll($changed){
        $changed = $changed==1?'dept':($changed == 2?'spec':'spon');
        return $this->find()->where(['apc_changed'=>$changed])->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'apc_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcNewDeptRel()
    {
        return $this->hasOne(Department::className(), ['id' => 'apc_newDept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcPrevGroupRel()
    {
        return $this->hasOne(WGroupSetup::className(), ['id' => 'apc_prevGroup_relId']);
    }/**
     * @return \yii\db\ActiveQuery
     */
    public function getApcNewGroupRel()
    {
        return $this->hasOne(WGroupSetup::className(), ['id' => 'apc_newGroup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcStdRel()
    {
        return $this->hasOne(Student::className(), ['id' => 'apc_std_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcNewSpecRel()
    {
        return $this->hasOne(Specialization::className(), ['id' => 'apc_newSpec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcPrevDeptRel()
    {
        return $this->hasOne(Department::className(), ['id' => 'apc_prevDept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcPrevSpecRel()
    {
        return $this->hasOne(Specialization::className(), ['id' => 'apc_prevSpec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcRegistrarApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'apc_registrarApproved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApcUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'apc_updated_by']);
    }
}
