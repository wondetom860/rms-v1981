<?php

namespace frontend\modules\CourseAssessment\models;

use Yii;
use \backend\models\User;

/**
 * This is the model class for table "instance_mark_assignment".
 *
 * @property int $id
 * @property int $ima_catagorySetup_relId
 * @property float $ima_possiblePoint
 * @property string $ima_instanceName
 * @property int $ima_created_by
 * @property string $ima_created_at
 * @property int|null $ima_updated_by
 * @property string|null $ima_updated_at
 * @property int|null $ima_approved
 * @property string|null $ima_approved_at
 * @property int|null $ima_approved_by
 * @property int $ima_assessed
 *
 * @property ContinuousAssessment2[] $continuousAssessment2s
 * @property ContAssessCatagory2 $imaCatagorySetupRel
 * @property User $imaUpdatedBy
 * @property User $imaCreatedBy
 */
class InstanceMarkAssignment extends \yii\db\ActiveRecord
{

    public $old_ima_possiblePoint;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instance_mark_assignment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ima_catagorySetup_relId', 'ima_possiblePoint', 'ima_instanceName', 'ima_created_by', 'ima_created_at'], 'required'],
            [['ima_catagorySetup_relId', 'ima_created_by', 'ima_updated_by', 'ima_approved', 'ima_approved_by', 'ima_assessed'], 'integer'],
            [['ima_possiblePoint','old_ima_possiblePoint'], 'number'],
            [['ima_created_at', 'ima_updated_at', 'ima_approved_at'], 'safe'],
            [['ima_instanceName'], 'string', 'max' => 50],
            [['ima_catagorySetup_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ContAssessCatagory::class, 'targetAttribute' => ['ima_catagorySetup_relId' => 'id']],
            [['ima_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ima_updated_by' => 'id']],
            [['ima_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['ima_created_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'ima_catagorySetup_relId' => Yii::t('rms', 'Ima Catagory Setup Rel ID'),
            'ima_possiblePoint' => Yii::t('rms', 'Ima Possible Point'),
            'ima_instanceName' => Yii::t('rms', 'Ima Instance Name'),
            'ima_created_by' => Yii::t('rms', 'Ima Created By'),
            'ima_created_at' => Yii::t('rms', 'Ima Created At'),
            'ima_updated_by' => Yii::t('rms', 'Ima Updated By'),
            'ima_updated_at' => Yii::t('rms', 'Ima Updated At'),
            'ima_approved' => Yii::t('rms', 'Ima Approved'),
            'ima_approved_at' => Yii::t('rms', 'Ima Approved At'),
            'ima_approved_by' => Yii::t('rms', 'Ima Approved By'),
            'ima_assessed' => Yii::t('rms', 'Ima Assessed'),
        ];
    }

    public function getInstanceNameRecom(){
        $instances = $this->imaCatagorySetupRel->getInstanceMarkAssignments()->andWhere(['ima_catagorySetup_relId' => $this->ima_catagorySetup_relId])->count();

        return $this->imaCatagorySetupRel->catCatagoryRel->catagory_name . '-' . ($instances + 1);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContinuousAssessment2s()
    {
        return $this->hasMany(ContinuousAssessment::class, ['ca_markAssignment_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaCatagorySetupRel()
    {
        return $this->hasOne(ContAssessCatagory::class, ['id' => 'ima_catagorySetup_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ima_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImaCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'ima_created_by']);
    }
}
