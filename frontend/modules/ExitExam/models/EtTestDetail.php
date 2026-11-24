<?php

namespace frontend\modules\ExitExam\models;

use \backend\models\User;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use Yii;

/**
 * This is the model class for table "et_test_detail".
 *
 * @property int $id
 * @property int $test_relId
 * @property int $acadYear_relId
 * @property float $mark_obtained
 * @property int $pass_status
 * @property int $date_submitted
 * @property int $grade_accepted
 * @property int|null $grade_accepted_at
 * @property int|null $grade_accepted_by
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AcAcademicYear $acadYearRel
 * @property User $createdBy
 * @property EtElligiblityTest $testRel
 * @property User $updatedBy
 */
class EtTestDetail extends \yii\db\ActiveRecord
{
    public $_pass_status = [
        1 => 'Pass',
        0 => 'Fail',
    ];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'et_test_detail';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['test_relId', 'acadYear_relId', 'created_at', 'created_by', 'data'], 'required'],
            [['test_relId', 'acadYear_relId', 'pass_status', 'date_submitted', 'grade_accepted', 'grade_accepted_at', 'grade_accepted_by', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['mark_obtained'], 'number'],
            [['data'], 'string'],
            [['acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcAcademicYear::class, 'targetAttribute' => ['acadYear_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['test_relId'], 'exist', 'skipOnError' => true, 'targetClass' => EtElligiblityTest::class, 'targetAttribute' => ['test_relId' => 'id']],
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
            'test_relId' => Yii::t('rms', 'Trial Entry'),
            'acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'mark_obtained' => Yii::t('rms', 'Mark Obtained'),
            'pass_status' => Yii::t('rms', 'Pass Status'),
            'date_submitted' => Yii::t('rms', 'Date Submitted'),
            'grade_accepted' => Yii::t('rms', 'Grade Accepted'),
            'grade_accepted_at' => Yii::t('rms', 'Grade Accepted At'),
            'grade_accepted_by' => Yii::t('rms', 'Grade Accepted By'),
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
            'test_relId' => $this->test_relId,
            'acadYear_relId' => $this->acadYear_relId,
            'mark_obtained' => $this->mark_obtained,
            'pass_status' => $this->pass_status,
            'date_submitted' => $this->date_submitted,
            'grade_accepted' => $this->grade_accepted,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function determinePassStatus(){
        $setting = EtSetting::findOne(['active_status' => 1]);

        if($this->mark_obtained !== NULL){
            if ($this->mark_obtained >= $setting->pass_mark) {
                $this->pass_status = 1; // Pass
            } else {
                $this->pass_status = 0; // Fail
            }

            // $this->save(false);
            // $this->refresh();

            if($this->testRel->pass_status != $this->pass_status){
                $test = $this->testRel;
                $test->pass_status = $this->pass_status;
                $test->updated_at = time();
                $test->updated_by = Yii::$app->user->id;
                $test->getData();
                $test->save(false);
                // $test->refresh();
            }
        }else{
            $this->pass_status = NULL; // Not yet determined
        }

        return $this->pass_status;
    }

    public function getPassMark(){
        $setting = EtSetting::findOne(['active_status' => 1]);
        return $setting->pass_mark;
    }

    public function getPassStatus()
    {
        if (is_null($this->pass_status)) {
            return '-';
        }
        return $this->_pass_status[$this->pass_status];
    }

    /**
     * Gets query for [[AcadYearRel]].
     *
     * @return \yii\db\ActiveQuery|AcAcademicYearQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(AcAcademicYear::class, ['id' => 'acadYear_relId']);
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
     * Gets query for [[TestRel]].
     *
     * @return \yii\db\ActiveQuery|EtElligiblityTestQuery
     */
    public function getTestRel()
    {
        return $this->hasOne(EtElligiblityTest::class, ['id' => 'test_relId']);
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
     * {@inheritdoc}
     * @return EtTestDetailQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EtTestDetailQuery(get_called_class());
    }
}
