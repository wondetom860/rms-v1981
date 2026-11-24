<?php

namespace backend\models;

use \common\components\CommonControllerFunctions as CCF;

use Yii;

/**
 * This is the model class for table "re_registration".
 *
 * @property int $id
 * @property string $prev_semreg
 * @property string $prev_srd2
 * @property int $std_relId
 * @property int $prev_gas_relId
 * @property int|null $new_gas_relId
 * @property int $course_relId
 * @property int|null $new_coff2det_relId
 * @property int $trail_count
 * @property int|null $reg_approved
 * @property int|null $reg_approved_at
 * @property int|null $reg_approved_by
 * @property int|null $dept_approved
 * @property int|null $dept_approved_at
 * @property int|null $dept_approved_by
 * @property int|null $admin_processed
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property int|null $deleted_at
 * @property int|null $deleted_by
 * @property int $created_by
 * @property int|null $updated_by
 * @property string $data
 *
 * @property Course $courseRel
 * @property User $createdBy
 * @property User $deletedBy
 * @property User $deptApprovedBy
 * @property CourseOffering2Detail $newCoff2detRel
 * @property WGroupSetup $newGasRel
 * @property WGroupSetup $prevGasRel
 * @property User $regApprovedBy
 * @property Student $stdRel
 * @property User $updatedBy
 */
class ReRegistration extends \yii\db\ActiveRecord
{
    public $std_regId;
    public $acadYearId;
    public $semId;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 're_registration';
    }

    protected function __constructor()
    {
        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['prev_semreg', 'prev_srd2', 'std_relId', 'prev_gas_relId', 'course_relId', 'trail_count', 'created_by', 'data'], 'required'],
            [['std_relId', 'prev_gas_relId', 'new_gas_relId', 'course_relId', 'new_coff2det_relId', 'trail_count', 'reg_approved', 'reg_approved_at', 'reg_approved_by', 'dept_approved', 'dept_approved_at', 'dept_approved_by', 'admin_processed', 'created_at', 'updated_at', 'deleted_at', 'deleted_by', 'created_by', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['prev_semreg', 'prev_srd2'], 'string'],
            [['course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['course_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['deleted_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['deleted_by' => 'id']],
            [['dept_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['dept_approved_by' => 'id']],
            [['new_coff2det_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['new_coff2det_relId' => 'id']],
            [['new_gas_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['new_gas_relId' => 'id']],
            [['prev_gas_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['prev_gas_relId' => 'id']],
            [['reg_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['reg_approved_by' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'prev_semreg' => Yii::t('rms', 'Prev. Registration Entry'),
            'prev_srd2' => Yii::t('rms', 'Prev Srd2'),
            'std_relId' => Yii::t('rms', 'Student Name'),
            'prev_gas_relId' => Yii::t('rms', 'Prev. Section'),
            'new_gas_relId' => Yii::t('rms', 'New Section'),
            'course_relId' => Yii::t('rms', 'Course'),
            'new_coff2det_relId' => Yii::t('rms', 'Course Offering'),
            'trail_count' => Yii::t('rms', 'Trail Count'),
            'reg_approved' => Yii::t('rms', 'Reg Approved'),
            'reg_approved_at' => Yii::t('rms', 'Reg Approved At'),
            'reg_approved_by' => Yii::t('rms', 'Reg Approved By'),
            'dept_approved' => Yii::t('rms', 'Dept Approved'),
            'dept_approved_at' => Yii::t('rms', 'Dept Approved At'),
            'dept_approved_by' => Yii::t('rms', 'Dept Approved By'),
            'admin_processed' => Yii::t('rms', 'Admin Processed'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'deleted_at' => Yii::t('rms', 'Deleted At'),
            'deleted_by' => Yii::t('rms', 'Deleted By'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function setCoff2det($offering_detail_id)
    {
        $this->__constructor();
        $offeringDetail = CourseOffering2Detail::findOne($offering_detail_id);
        if ($offeringDetail->coff2detCoff2Rel->coff2_acadYear_relId == $this->acadYearId && $offeringDetail->coff2detCoff2Rel->coff2_sem_relId == $this->semId) {
            $this->new_coff2det_relId = $offering_detail_id;
            $this->new_gas_relId = $offeringDetail->coff2detCoff2Rel->coff2_group_relId;
            $this->updated_at = time();
            $this->updated_by = Yii::$app->user->identity->id;
            $this->admin_processed = 1;
            $this->getdata();
            if ($this->save()) {
                return 1;
            } else {
                json_encode($this->errors);
            }
        } else {
            return "Invalid Selection";
        }
    }

    public function getOfferings()
    {
        $this->__constructor();
        return CourseOffering2Detail::find()
            ->innerJoinWith('coff2detCoff2Rel')
            ->andWhere([
                'coff2_acadYear_relId' => $this->acadYearId,
                'coff2_sem_relId' => $this->semId,
                'coff2det_course_relId' => $this->course_relId
            ])->all();
    }
    private function updateSemreg()
    {
        $srd2 = unserialize($this->prev_srd2);
        $srd2Obj = SemesterRegistration2Detail::findOne($srd2->id);

        if ($srd2Obj) {
            if (($srd2Obj->srd2_grade_relId == NULL) || (in_array($srd2Obj->srd2_grade_relId, GradeScale::getInvalidGradeIds()))) {
                $transaction = Yii::$app->db->beginTransaction();
                $coff2det = $this->newCoff2detRel;
                $semreg = $srd2Obj->srd2SemregRel;
                $semreg->semreg_acadYear_relId = $coff2det->coff2detCoff2Rel->coff2_acadYear_relId;
                $semreg->semreg_semester_relId = $coff2det->coff2detCoff2Rel->coff2_sem_relId;
                $semreg->semreg_updated_at = date('Y-m-d');
                $semreg->semreg_updated_by = Yii::$app->user->identity->id;
                $semreg->semreg_re_registered = 1;
                if ($semreg->save()) {
                    $pgset = $srd2Obj->srd2_group_relId;
                    $srd2Obj->srd2_grade_relId = NULL;
                    $srd2Obj->srd2_group_relId = $this->new_gas_relId;
                    $srd2Obj->srd2_updated_by = Yii::$app->user->identity->id;
                    $srd2Obj->srd2_updated_at = date('Y-m-d');
                    if ($srd2Obj->save()) {
                        $gas = new WGroupAssignment();
                        if($gas->find()->where(['gas_std_relId' => $semreg->semreg_std_relId,'gas_gset_relId' => $srd2Obj->srd2_group_relId ])->count() > 0){

                        }else{
                            $gas->gas_std_relId = $semreg->semreg_std_relId;
                            $gas->gas_gset_relId = $srd2Obj->srd2_group_relId;
                            $gas->gas_prevGroup_relId = $pgset;
                            if($gas->save()){
                                $transaction->commit();
                                return 1;
                            }else{
                                $transaction->rollBack();
                                return json_encode($gas->errors);
                            }
                        }
                    } else {
                        $transaction->rollBack();
                        return json_encode($srd2Obj->errors);
                    }
                } else {
                    $transaction->rollBack();
                    return json_encode($semreg->errors);
                }
            } else {
                return "Course id graded, grade Obtained : [{$srd2Obj->srd2GradeRel->gs_ltr_grade}]";
            }
        } else {
            return "Entry not found!";
        }
    }
    public function changeApproveStatus($option)
    {
        if (Yii::$app->user->can('DepartmentHead')) {
            $this->dept_approved = $option;
            $this->dept_approved_at = ($option == 1) ? time() : NULL;
            $this->dept_approved_by = ($option == 1) ? Yii::$app->user->identity->id : NULL;
        } else if (Yii::$app->user->can('Registrar')) {
            $result = ($option == 1) ? $this->updateSemreg() : 1;
            if ($result == 1) {
                $this->reg_approved = $option;
                $this->reg_approved_at = ($option == 1) ? time() : NULL;
                $this->reg_approved_by = ($option == 1) ? Yii::$app->user->identity->id : NULL;
            }else{
                return $result;
            }
        }

        $this->getData();
        if ($this->save()) {
            return 1;
        } else {
            return json_encode($this->errors);
        }
    }

    public function createRequest($srd2_id)
    {
        $srd2 = SemesterRegistration2Detail::findOne($srd2_id);
        $semreg = $srd2->srd2SemregRel;
        $this->prev_srd2 = serialize($srd2);
        $this->prev_semreg = serialize($semreg);
        $this->prev_gas_relId = $srd2->srd2_group_relId;
        $this->course_relId = $srd2->srd2_course_relId;
        // check if approval pending request exists:
        if ($this->pendingRequest()) {
            return "Request pending approval.";
        }
        $this->trail_count = $this->getTrailCount() + 1;
        if ($this->trail_count > 4) {
            return "Can NOT request for re-registration, trail more than 4.";
        }
        $this->created_at = time();
        $this->created_by = Yii::$app->user->identity->id;
        $this->getData();

        if ($this->save()) {
            Yii::$app->session->setFlash('success', 'Requested successfully');
            return $this->id;
        } else {
            return json_encode($this->errors);
        }
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'prev_srd2' => $this->prev_srd2,
            'prev_semreg' => $this->prev_semreg,
            'prev_gas_relId' => intval($this->prev_gas_relId),
            'course_relId' => intval($this->course_relId),
            'trail_count' => intval($this->trail_count),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    private function pendingRequest()
    {
        return self::find()
            ->where([
                'course_relId' => $this->course_relId,
                'std_relId' => $this->std_relId
            ])
            ->andWhere([
                'OR',
                ['dept_approved' => NULL],
                ['reg_approved' => NULL]
            ])
            ->count() > 0;
    }

    private function getTrailCount()
    {
        return self::find()
            ->where([
                'course_relId' => $this->course_relId,
                'std_relId' => $this->std_relId
            ])->count();
    }

    /**
     * Gets query for [[CourseRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'course_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[DeletedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeletedBy()
    {
        return $this->hasOne(User::class, ['id' => 'deleted_by']);
    }

    /**
     * Gets query for [[DeptApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'dept_approved_by']);
    }

    /**
     * Gets query for [[NewCoff2detRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewCoff2detRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'new_coff2det_relId']);
    }

    /**
     * Gets query for [[NewGasRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNewGasRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'new_gas_relId']);
    }

    /**
     * Gets query for [[PrevGasRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPrevGasRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'prev_gas_relId']);
    }

    /**
     * Gets query for [[RegApprovedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'reg_approved_by']);
    }

    /**
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
