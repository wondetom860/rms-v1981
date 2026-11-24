<?php

namespace frontend\modules\ExamSchedule\models;

use Yii;
use common\models\User;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;

/**
 * This is the model class for table "ex_exam_schedule_entry".
 *
 * @property int $id
 * @property int $acad_year_relId
 * @property int $start_date
 * @property int $end_date
 * @property string $header_text
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property ExDateTimeSchedule[] $exDateTimeSchedules
 * @property AcAcademicYear $acadYearRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class ExExamScheduleEntry extends \yii\db\ActiveRecord
{

    public $start_date_str;
    public $end_date_str;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_exam_schedule_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['acad_year_relId', 'start_date', 'end_date', 'header_text', 'created_at', 'created_by', 'data'], 'required'],
            [['acad_year_relId', 'start_date', 'end_date', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['header_text', 'data', 'start_date_str', 'end_date_str'], 'string'],
            [['acad_year_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['acad_year_relId' => 'id']],
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
            'acad_year_relId' => Yii::t('rms', 'Academic Year'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'header_text' => Yii::t('rms', 'Header Text'),
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
            'acad_year_relId' => $this->acad_year_relId,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'header_text' => $this->header_text,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function checkIfExists($id = null)
    {
        if ($id) {
            return self::find()
                ->where([
                    'acad_year_relId' => $this->acad_year_relId,
                ])
                ->andWhere(" id <> $id ")
                ->one();
        }
        return self::findOne([
            'acad_year_relId' => $this->acad_year_relId,
        ]);
    }

    public function getScehduleEntryHeader()
    {
        return $this->acadYearRel->getAcHeader() . ' [ ' . date('dMY', $this->start_date) . ' to ' . date('dMY', $this->end_date) . ']';
    }
    public function checkValidDate($forUpdate = false)
    {
        $error_message  = null;
        if ($this->start_date > $this->end_date) {
            $error_message = 'Start Date cannot be greater than End Date.';
        }

        if (!$forUpdate && $this->start_date < time() - (86400 + 1)) {
            $error_message = Yii::t('rms', 'Start Date cannot be less than current date.');
        }

        if ($this->end_date < time()) {
            $error_message = Yii::t('rms', 'End Date cannot be less than current date.');
        }

        return $error_message;
    }

    public function getStatustext()
    {
        $status = '';
        if ($this->start_date > time()) {
            $status = '<span class="badge bg-info badge-info">Upcoming</span>';
        } elseif ($this->start_date < time() && $this->end_date > time()) {
            $status = '<span class="badge bg-warning badge-warning">Ongoing</span>';
        } elseif ($this->end_date < time()) {
            $status = '<span class="badge bg-success badge-success">Completed</span>';
        }
        return $status;
    }


    /**
     * Gets query for [[AcadYearRel]].
     *
     * @return \yii\db\ActiveQuery|AcAcademicYearQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'acad_year_relId']);
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
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery|UserQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[ExDateTimeSchedules]].
     *
     * @return \yii\db\ActiveQuery|ExDateTimeScheduleQuery
     */
    public function getExDateTimeSchedules()
    {
        return $this->hasMany(ExDateTimeSchedule::class, ['exam_schedule_entry_relId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ExExamScheduleEntryQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExExamScheduleEntryQuery(get_called_class());
    }
}
