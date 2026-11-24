<?php

namespace frontend\modules\ExitExam\models;

use Yii;

use backend\models\User;
use backend\models\SemesterRegistration2;
use backend\models\SemesterRegistration2Detail;
use backend\models\Student;

/**
 * This is the  = $this; class for table "et_elligiblity_test".
 *
 * @property int $id
 * @property int $semreg_relId
 * @property int $setting_relId
 * @property int|null $srd2_relId
 * @property int $reason
 * @property int|null $pass_status
 * @property int $trails
 * @property int $remaining_trails
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property SemesterRegistration2 $semregRel
 * @property EtSetting $settingRel
 * @property User $updatedBy
 * @property SemesterRegistration2Detail $srd2Rel
 * @property EtTestDetail[] $etTestDetails
 */
class EtElligiblityTest extends \yii\db\ActiveRecord
{

    public $regId, $full_name;
    public $_reasons = [
        1 => 'Exit Exam',
        2 => 'Clear Course Back Logs',
        3 => 'Other',
    ];
    public $_pass_status = [
        1 => 'Pass',
        0 => 'Fail',
    ];

    public function getReasons()
    {
        $rr = [];
        foreach ($this->_reasons as $key => $value) {
            $rr[] = ['id' => $key, 'name' => $value];
        }

        return $rr;
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'et_elligiblity_test';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['semreg_relId', 'reason', 'created_at', 'created_by', 'data'], 'required'],
            [['semreg_relId', 'srd2_relId', 'reason', 'pass_status', 'trails', 'remaining_trails', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['semreg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2::class, 'targetAttribute' => ['semreg_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['srd2_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SemesterRegistration2Detail::class, 'targetAttribute' => ['srd2_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'regId' => Yii::t('rms', 'Reg. ID'),
            'full_name' => Yii::t('rms', 'Full Name'),
            'srd2_relId' => Yii::t('rms', 'Course'),
            'semreg_relId' => Yii::t('rms', 'Semester Registration'),
            'reason' => Yii::t('rms', 'Reason'),
            'pass_status' => Yii::t('rms', 'Pass Status'),
            'trails' => Yii::t('rms', 'Trials'),
            'remaining_trails' => Yii::t('rms', 'Remaining Trials'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'semreg_relId' => $this->semreg_relId,
            'srd2_relId' => $this->srd2_relId,
            'reason' => $this->reason,
            'pass_status' => $this->pass_status,
            'trails' => $this->trails,
            'remaining_trails' => $this->remaining_trails,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function isEligibleForTest()
    {
        $eligible = false;
        $trailsCount = count($this->etTestDetails);
        if ($this->remaining_trails != (3 - $trailsCount)) {
            $this->remaining_trails = 3 - $trailsCount;
            $this->trails = $trailsCount;
            $this->save();
            $this->refresh();
        }
        if (Yii::$app->user->can('Student')) {
            if ($this->remaining_trails > 0 && ($this->pass_status == 0) && $this->semregRel->semregStdRel->std_user_relId == Yii::$app->user->id) {
                $eligible = true;
            }
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            // Registrar or Admin can check any student
            if ($this->remaining_trails > 0 && ($this->pass_status == 0)) {
                $eligible = true;
            }
        }
        return $eligible;
    }

    public static function getFailedEtRecord($stdId)
    {
        return self::find()
            ->joinWith('semregRel')
            ->where(['semreg_std_relId' => $stdId])
            ->andWhere(['pass_status' => 0, 'srd2_relId' => NULL])
            ->andWhere(['reason' => 1])
            ->one();
        // return $failedEt;
    }

    public static function getPassedEtRecord($stdId)
    {
        $passedEt = EtElligiblityTest::find()
            ->joinWith('semregRel')
            ->where(['semreg_std_relId' => $stdId])
            ->andWhere(['pass_status' => 1, 'srd2_relId' => NULL])
            ->andWhere(['reason' => 1])
            ->one();
        return $passedEt;
    }


    public function getReason()
    {
        return $this->_reasons[$this->reason];
    }

    public function getPassStatus()
    {
        $details = $this->getEtTestDetails()->andWhere(['pass_status' => [0, 1]])->one();
        // var_dump(!in_array(NULL,[1,2]));die();
        // $approved = false;
        $approved = $details ? ($details->grade_accepted == 1 ? true : false) : false;
        if ($details && $this->pass_status == NULL) {
            $this->pass_status = $details->pass_status;
            $this->trails = count($this->etTestDetails);
            $this->remaining_trails = 3 - $this->trails;
            $this->getData();
            $this->save();
            $this->_pass_status[$this->pass_status];
            $approved = $details->grade_accepted == 1 ? true : false;
        }
        $details = $this->getEtTestDetails()->andWhere(['pass_status' => NULL])->one();
        if ($details && $this->pass_status != NULL) {
            $this->pass_status = NULL;
            $this->trails = count($this->etTestDetails);
            $this->remaining_trails = 3 - $this->trails;
            $this->getData();
            $this->save();
            $approved = $details->grade_accepted == 1 ? true : false;
        }
        return is_null($this->pass_status) ? '-' : [$this->_pass_status[$this->pass_status], $approved];
    }

    // private function passedLastSem($lastSemreg = NULL)
    // {
    //     $lastSemreg =  $lastSemreg ? $lastSemreg :  $this->getLastSemester();
    //     return $lastSemreg->semesterLastSsnRel && ($lastSemreg->semesterLastSsnRel->sssn_pass_status == 1);
    // }

    private function getLastSemester($std)
    {
        // $std = Student::findOne(['std_user_relId' => Yii::$app->user->id]);
        $semregs = $std
            ->getSemesterRegistrations()
            ->orderBy([
                'semreg_acadYear_relId' => SORT_ASC,
                'semreg_semester_relId' => SORT_ASC
            ])
            ->all();
        return $semregs[count($semregs) - 1];
    }

    public function prcessExitExamRequest($std_id = null)
    {
        if (Yii::$app->user->can('Student')) {
            $std = Student::findOne(['std_user_relId' => Yii::$app->user->id]);
        } else if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
            $std = Student::findOne(['id' => $std_id]);
        } else {
            return [
                'status' => 0,
                'message' => 'Please select student (Graduating Class Student).',
            ];
        }

        $lastSemreg = $this->getLastSemester($std);
        // $passed =  $this->passedLastSem($lastSemreg); //semreg_last_ssn_relId
        if (($std->stdGroupReal->gsActiveProgRel->graduate_class == 1)) { //and lastSemreg->semregStatus == 1
            $model = $this;
            $model->semreg_relId = $lastSemreg->id;
            // check if ducplicate entry exists
            $pReq = $this->pendingrequestExists();
            if (!is_null($pReq)) {
                return $pReq;
            }

            $passedTest = $this->find()
                ->where(['semreg_relId' => $this->semreg_relId])
                ->andWhere(['srd2_relId' => $this->srd2_relId])
                ->andWhere(['reason' => $this->reason])
                ->andWhere(['pass_status' => 1])
                ->one();
            if ($passedTest) {
                return [
                    'status' => 0,
                    'message' => 'You have already passed the exit exam.',
                ];
            }

            $filedTest = $this->find()
                ->where(['semreg_relId' => $this->semreg_relId])
                ->andWhere(['srd2_relId' => $this->srd2_relId])
                ->andWhere(['reason' => $this->reason])
                ->andWhere(['pass_status' => 0])
                ->one();
            if ($filedTest) {
                if ($filedTest->remaining_trails > 0) {
                    $model = $filedTest;
                    $model->trails += 1;
                    $model->remaining_trails -= 1;
                    $model->updated_at = time();
                    $model->updated_by = Yii::$app->user->id;
                } else {
                    return [
                        'status' => 0,
                        'message' => 'You do NOT have trails, please contact Regsitrar office for more information',
                    ];
                }
            } else {
                $model->created_at = time();
                $model->created_by = Yii::$app->user->id;
            }
            $model->getData();
            if ($model->save()) {
                // create test details
                $testDetails = new EtTestDetail();
                $testDetails->test_relId = $model->id;
                $testDetails->acadYear_relId = $std->stdGroupReal->gsActiveProgRel->academic_year_relId;
                $testDetails->mark_obtained = NULL;
                $testDetails->created_at = time();
                $testDetails->created_by = Yii::$app->user->id;
                $testDetails->getData();
                if ($testDetails->save()) {
                    return [
                        'status' => 1,
                        'message' => 'Exit exam request has been submitted successfully.',
                        'model' => $model->id,
                    ];
                } else {
                    // $this->delete();
                    return [
                        'status' => 0,
                        'message' => 'Exit exam request has not been submitted successfully. Error: ' . json_encode($testDetails->getErrors()),
                    ];
                }
            } else {
                return [
                    'status' => 0,
                    'message' => 'Exit exam request has not been submitted successfully. Error: ' . json_encode($this->getErrors()),
                ];
            }
        } else {
            return [
                'status' => 0,
                'message' => 'Students except ( GC and Passed ) can Not request for Exit exam.',
            ];
        }
    }

    public function unapprovedRequests()
    {
        return EtTestDetail::find()
            ->where([
                'grade_accepted' => 0
            ])
            ->andWhere(['IS NOT','mark_obtained',NULL])
            ->all();
    }
    // 
    private function pendingrequestExists()
    {
        $duplicate = EtElligiblityTest::find()
            ->joinWith('etTestDetails')
            ->where(['semreg_relId' => $this->semreg_relId])
            ->andWhere(['srd2_relId' => $this->srd2_relId])
            ->andWhere(['reason' => $this->reason])
            ->andWhere(['mark_obtained' => NULL])
            ->one();
        if ($duplicate) {
            return [
                'status' => 1,
                'message' => 'Pending request exists.',
                'model' => $duplicate->id,
            ];
        }

        return NULL;
    }

    /**
     * Gets query for [[settingRel]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getSettingRel()
    {
        return $this->hasOne(EtSetting::class, ['id' => 'setting_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[SemregRel]].
     *
     * @return \yii\db\ActiveQuery|SemesterRegistration2Query
     */
    public function getSemregRel()
    {
        return $this->hasOne(SemesterRegistration2::class, ['id' => 'semreg_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[Srd2Rel]].
     *
     * @return \yii\db\ActiveQuery|SemesterRegistration2DetailQuery
     */
    public function getSrd2Rel()
    {
        return $this->hasOne(SemesterRegistration2Detail::class, ['id' => 'srd2_relId']);
    }

    /**
     * Gets query for [[EtTestDetails]].
     *
     * @return \yii\db\ActiveQuery|EtTestDetailQuery
     */
    public function getEtTestDetails()
    {
        return $this->hasMany(EtTestDetail::class, ['test_relId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return EtElligiblityTestQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EtElligiblityTestQuery(get_called_class());
    }
}
