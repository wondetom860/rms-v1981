<?php

namespace frontend\modules\AcStEva\models;

use backend\models\Department;
use backend\models\DepartmentHead;
use Yii;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use backend\models\User;
use yii\helpers\Url;

/**
 * This is the model class for table "ase_semester_evaluation".
 *
 * @property int $id
 * @property int $academic_year_relId
 * @property string $evaluation_header
 * @property int $start_date
 * @property int $end_date
 * @property int $status
 * @property int $force_above_half_evaluation //force above half evaluation for report generating (>=51%)
 * @property string $criteria_bundle_IDS
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AseDepartmentEvaluationSchedule[] $aseDepartmentEvaluationSchedules
 * @property AcAcademicYear $academicYearRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class AseSemesterEvaluation extends \yii\db\ActiveRecord
{

    public $startdate;
    public $enddate;
    public $statuses = [0 => 'Closed', 1 => 'Active', -1 => 'Up Next'];

    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 0;
    public const STATUS_UPNEXT = -1;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_semester_evaluation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['academic_year_relId', 'evaluation_header', 'start_date', 'end_date', 'criteria_bundle_IDS', 'created_at', 'created_by', 'data'], 'required'],
            [['academic_year_relId', 'start_date', 'end_date', 'force_above_half_evaluation', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['criteria_bundle_IDS', 'startdate', 'enddate'], 'safe'],
            [['academic_year_relId'], 'unique'],
            [['data'], 'string'],
            [['evaluation_header'], 'string', 'max' => 55],
            [['academic_year_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['academic_year_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'academic_year_relId' => Yii::t('rms', 'Academic Year'),
            'evaluation_header' => Yii::t('rms', 'Evaluation Header'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'startdate' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'enddate' => Yii::t('rms', 'End Date'),
            'status' => Yii::t('rms', 'Status'),
            'force_above_half_evaluation' => Yii::t('rms', 'Above half evaluation'),
            'criteria_bundle_IDS' => Yii::t('rms', 'Evaluation Criteria'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getStatus($sts = null)
    {
        $st = "";
        $s = 1;

        $acadYearStatus = $this->academicYearRel->getStatus();

        if (strtoupper($acadYearStatus) != 'ACTIVE') {
            //if academic year is not active, the program scdule must also be not active.
            $s = array_keys($this->statuses, $acadYearStatus)[0];
            $st = $acadYearStatus;
        } else {
            if (time() > $this->end_date) {
                $st = "Closed";
                $s = 0;
            } else if (time() < $this->start_date) {
                $st = "Up Next";
                $s = -1;
            } else {
                $s = 1;
                $st = "Active";
            }
        }

        if ($this->status != $s) {
            $this->status = $s;
            $this->update(false);
        }

        if ($sts) {
            return $st;
        }

        // return $st;
        return $this->status == 1 ? "<span class='bg-success'>{$st}</span>" : "<span class='bg-warning'>{$st}</span>";
    }

    public function sendNotificationMail()
    {
        $hodEmails = [];
        $depts = Department::find()->where(['status' => 1])->all();
        foreach($depts as $dept){
            $hod = $dept->hod;
            if($hod){
                $inst = $hod->dhInstRel;
                $email = $inst->instUserRel->email;
                if($email){
                    $hodEmails[] = $email;
                }
            }
        }

        // $hodEmails = ["wondetom@gmail.com"];

        if (!empty($hodEmails)) {
            // send Email to HoD Emails
            $acy = $this->academicYearRel->getAcHeader();
            $urll = Yii::$app->urlManager->createAbsoluteUrl(['/AcStEva/ase-semester-evaluation/view', 'id' => $this->id]);
            $email_body = "Dear all, <br>This is to inform you that <strong>{$this->evaluation_header}</strong> for academic year of <strong>{$acy}</strong> 
                is scheduled to <strong>{$this->getScheduleInterval()}</strong>. Please setup your department schedule  and cordinate your 
                department staff to complete this task before the schedule goes off. Click {$urll} 
                for department schedule.";
            $message = Yii::$app->mailer->compose('@common/mail/AcStEva_notification', ['content' => $email_body])
                ->setFrom([Yii::$app->params['adminEmail'] => 'ETDU - Registrar Office'])
                ->setTo($hodEmails)
                ->setSubject("{$this->evaluation_header}");

            $message->send();

            // $message->attach('reports/mailed_evaluation_results' . '/' . $filename);
        } else {
            return false;
        }
    }

    public function getCriteriaBundleModels()
    {
        if (!$this->criteria_bundle_IDS) {
            return NULL;
        }

        return AseCriteriaBundle::find()->where(['id' => json_decode($this->criteria_bundle_IDS)])->all();
    }

    public function getScheduleInterval()
    {
        return date("d/m/Y", $this->start_date) . "-" . date("d/m/Y", $this->end_date);
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'criteria_bundle_IDS' => $this->criteria_bundle_IDS,
            'evaluation_header' => $this->evaluation_header,
            'status' => $this->status,
            'academic_year_relId' => intval($this->academic_year_relId),
            'start_date' => intval($this->start_date),
            'end_date' => intval($this->end_date),
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function checkIfExists($id = null)
    {
        if ($id) {
            return self::find()->where(['academic_year_relId' => $this->academic_year_relId])->andWhere(" id <> $id ")->one();
        }
        return self::findOne(['academic_year_relId' => $this->academic_year_relId]);
    }


    /**
     * Gets query for [[AseDepartmentEvaluationSchedules]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseDepartmentEvaluationSchedules()
    {
        return $this->hasMany(AseDepartmentEvaluationSchedule::class, ['header_relId' => 'id']);
    }

    /**
     * Gets query for [[AcademicYearRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAcademicYearRel()
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'academic_year_relId']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
