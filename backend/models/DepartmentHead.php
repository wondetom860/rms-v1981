<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "department_head".
 *
 * @property integer $id
 * @property integer $dh_inst_relId
 * @property integer $dh_dept_relId
 * @property string $dh_assig_date
 * @property string $dh_terminated_date
 * @property integer $dh_status
 * @property integer $created_by
 * @property string $created_at
 * @property integer $updated_by
 * @property string $updated_at
 *
 * @property User $createdBy
 * @property Department $dhDeptRel
 * @property Instructor $dhInstRel
 * @property User $updatedBy
 */
class DepartmentHead extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $dhUserRelId;
    public $fullName;
    public static function tableName()
    {
        return 'department_head';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dh_inst_relId', 'dh_dept_relId', 'dh_assig_date', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'required'],
            [['dh_inst_relId', 'dh_dept_relId', 'dh_status', 'created_by', 'updated_by'], 'integer'],
            [['dh_assig_date', 'dh_terminated_date', 'created_at', 'updated_at'], 'safe'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['dh_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dh_dept_relId' => 'id']],
            [['dh_inst_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['dh_inst_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'dh_inst_relId' => Yii::t('rms', 'Full Name'),
            'dh_dept_relId' => Yii::t('rms', 'Department'),
            'dh_assig_date' => Yii::t('rms', 'Assign Date'),
            'dh_terminated_date' => Yii::t('rms', 'Terminated Date'),
            'dh_status' => Yii::t('rms', 'Current Status'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created On'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated On'),
        ];
    }

    public function cleanCorrect(){
        if ($this->dh_status == 1) {
            $auth = \Yii::$app->authManager;
            $role = $auth->getRole('DepartmentHead');
            $role = $role ? : $auth->getPermission('DepartmentHead');
            $others = self::find()
                ->where(['dh_dept_relId' => $this->dh_dept_relId])
                ->andWhere(" id <> {$this->id} ")
                ->all();
            if ($others && count($others) > 0) {
                foreach ($others as $hod) {
                    if ($hod->dh_status == 1) {
                        $hod->dh_status = 0;
                        $hod->dh_terminated_date = date('Y-m-d h:i:s');
                        $hod->update();
                    }

                    $auth->revoke($role,$hod->dhInstRel->inst_user_relId);
                }
            }

            if (is_null($auth->getAssignment('DepartmentHead', $this->dhInstRel->inst_user_relId))) {
                $auth->assign($role,$this->dhInstRel->inst_user_relId);
            }            
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDhDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'dh_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDhInstRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'dh_inst_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
