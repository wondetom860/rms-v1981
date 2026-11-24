<?php

namespace frontend\modules\ExamSchedule\models;

use backend\models\CourseOffering2;
use backend\models\CourseOffering2Detail;
use common\models\User;
use frontend\modules\ClassSchedule\models\CsAvailableClassRoom;
use Yii;

// CsAvailableClassRoom

/**
 * This is the model class for table "ex_exam_room_slot".
 *
 * @property int $id
 * @property int $date_time_slot_relId
 * @property string|null $offering_detail_IDs
 * @property int $class_room_relId
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string|null $data
 *
 * @property User $createdBy
 * @property User $updatedBy
 * @property ExDateTimeSchedule $dateTimeSlotRel
 * @property CsAvailableClassRoom $classRoomRel
 * @property ExExaminees[] $exExaminees
 * @property ExInvAttendanceForm[] $exInvAttendanceForms
 * @property ExInvigilators[] $exInvigilators
 */
class ExExamRoomSlot extends \yii\db\ActiveRecord
{
    public $offeringIds;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ex_exam_room_slot';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date_time_slot_relId', 'class_room_relId', 'created_at', 'created_by'], 'required'],
            [['date_time_slot_relId', 'class_room_relId', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['offering_detail_IDs'], 'safe'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['date_time_slot_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ExDateTimeSchedule::class, 'targetAttribute' => ['date_time_slot_relId' => 'id']],
            [['class_room_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CsAvailableClassRoom::class, 'targetAttribute' => ['class_room_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'date_time_slot_relId' => Yii::t('rms', 'Date Time Slot'),
            'offering_detail_IDs' => Yii::t('rms', 'Course Detail'),
            'class_room_relId' => Yii::t('rms', 'Class Room'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getScheduledCourseInstructors()
    {
        $OFf = $this->offering_detail_IDs ? json_decode($this->offering_detail_IDs, true) : [];
        $cc = [];
        if ($OFf) {
            foreach ($OFf as $index => $offering) {
                $offeringDetail = CourseOffering2Detail::findOne(['id' => $offering]);
                if ($offeringDetail->coff2det_inst_relId) {
                    $cc[] = $offeringDetail->coff2detInstRel->inst_user_relId;
                }
            }
        }
        return array_unique($cc);
    }

    public function getCourseCode()
    {
        $OFf = $this->offering_detail_IDs ? json_decode($this->offering_detail_IDs, true) : [];
        $cc = [];
        if ($OFf) {
            foreach ($OFf as $index => $offering) {
                $offeringDetail = CourseOffering2Detail::findOne(['id' => $offering]);
                $course = $offeringDetail->coff2detCourseRel;
                $cc[] = $course->getCourseCredits();
            }
        }

        return join('<br>', $cc);
    }

    /**
     * Summary of checkForStudentCourseCollision
     * @param mixed $offeringDetailId
     * @return bool
     * check for student course regsitration entries if any clash is there
     */
    public function checkForStudentCourseCollision($offeringDetailId)
    {
        // $examRoomSlots =
        $coff2det = CourseOffering2Detail::findOne(['id' => $offeringDetailId]);
        $enrolls = $coff2det->enrolls;  // gets all students enrolled course in offeringDetailObject adn returns single srd2Object
        if (count($enrolls) > 0) {
            foreach ($enrolls as $srd2) {  // fore
                $semregs = $srd2
                    ->srd2SemregRel
                    ->getSemesterRegistration2Details()
                    ->andWhere(" srd2_course_relId <> $coff2det->coff2det_course_relId")
                    ->all();
                if ($semregs) {
                    foreach ($semregs as $srd2) {
                        $course = $srd2->srd2CourseRel;
                        $offeringDetailIds = $this->getCourseOfferings($course);
                        if ($coff2det->coff2det_course_relId != $srd2->srd2_course_relId && $this->checkIntersection($offeringDetailIds)) {
                            return true;
                        }
                    }
                }
            }
            return false;
        } else {
            return false;
        }
        // 1.Get semester registrations per student => array_of_coff2details
        // 2.foreach semregDetail, look for the examRoomSlot which contains at least one course from the above list in the same dateTimeSlot
        // 3.if any such entry is found, studentCourseCollision is true.
        // 4.array_intersect() => if there is any common entry for both semregCoff2dets and roomSlotCoff2details, then it is point of collision
    }

    protected function checkIntersection($offeringDetailIds)
    {
        $roomSlots = $this->dateTimeSlotRel->exExamRoomSlots;
        if ($roomSlots) {
            foreach ($roomSlots as $roomSlot) {
                $roomSlotCoff2detailIds = $roomSlot->offering_detail_IDs ? json_decode($roomSlot->offering_detail_IDs, true) : [];
                if (!empty($roomSlotCoff2detailIds)) {
                    $intersection = array_intersect($roomSlotCoff2detailIds, $offeringDetailIds);
                    if (!empty($intersection)) {
                        return true;
                    }
                } else {
                    continue;
                }
            }
        }

        return false;
    }

    protected function getCourseOfferings($course)
    {
        return $course
            ->getCourseOfferings()
            ->innerJoinWith('coff2detCoff2Rel')
            ->andWhere([
                'coff2_acadYear_relId' => $this->dateTimeSlotRel->examScheduleEntryRel->acadYearRel->schoolYearRel->id,
                'coff2_sem_relId' => $this->dateTimeSlotRel->examScheduleEntryRel->acadYearRel->semesterRel->id,
            ])
            ->select('course_offering2_detail.id')
            ->column();
    }

    /**
     * Summary of checkForSectionCourseCollision
     * @param mixed $offeringDetailId
     * @return bool
     * checks for section course collisions
     */
    public function checkForSectionCourseCollision($offeringDetailId)
    {
        $offeringDetail = CourseOffering2Detail::findOne(['id' => $offeringDetailId]);
        $sectionOfferings = $offeringDetail
            ->coff2detCoff2Rel
            ->getCourseOffering2Details()
            ->andWhere(" id <> {$offeringDetailId} ")
            ->all();

        if ($sectionOfferings) {
            foreach ($sectionOfferings as $sectionOffering) {
                // check if offering exists in this dateTimeSchedule
                if ($this->courseScheduledInTheSameDateTimeSchedule($sectionOffering->id)) {
                    return true;
                }
            }
        }
        return false;
    }

    public function courseScheduledInTheSameDateTimeSchedule($offeringDetailId)
    {
        // it should be checked course wise, not offeringdetail wise.
        $offeringDetal1 = CourseOffering2Detail::findOne($offeringDetailId);
        $course = $offeringDetal1->coff2detCourseRel;
        $courseOfferingEntries = $this->getCourseOfferings($course);
        if ($courseOfferingEntries) {
            $roomSlots = $this->dateTimeSlotRel->exExamRoomSlots;
            if ($roomSlots) {
                foreach ($courseOfferingEntries as $courseOfferingEntry) {
                    foreach ($roomSlots as $roomSlot) {
                        if ($roomSlot->offering_detail_IDs && in_array($courseOfferingEntry, json_decode($roomSlot->offering_detail_IDs, true))) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    public function courseAvailableInAnotherSchedule($offeringdetailId)
    {
        return self::find()
            ->innerJoinWith('dateTimeSlotRel')
            ->where(" $offeringdetailId IN offering_detail_IDs ")
            ->andWhere(" id <> {$this->id} ")
            ->andWhere(['exam_schedule_entry_relId' => $this->dateTimeSlotRel->exam_schedule_entry_relId])
            ->count() > 0;
    }

    public function checkForCourseBlockedByAnotherCourse($offeringdetailId)
    {
        // checks if the course is blocked by anither course which is both enrolled by the section and scheduled in the same dateTimeSlot.
        $offeringDetal1 = CourseOffering2Detail::findOne($offeringdetailId);
        $course = $offeringDetal1->coff2detCourseRel;
        $sections = CourseOffering2::find()
            ->innerJoinWith('courseOffering2Details')
            ->where([
                'coff2det_course_relId' => $course->id,
                'coff2_acadYear_relId' => $offeringDetal1->coff2detCoff2Rel->coff2_acadYear_relId,
                'coff2_sem_relId' => $offeringDetal1->coff2detCoff2Rel->coff2_sem_relId,
            ])
            ->all();
        // $sections = $offeringDetal1
        //     ->coff2detCoff2Rel
        //     ->getCourseOffering2Details()
        //     ->andWhere([
        //         'coff2det_course_relId' => $course->id,
        //         'coff2_acadYear_relId' => $offeringDetal1->coff2detCoff2Rel->coff2_acadYear_relId,
        //         'coff2_sem_relId' => $offeringDetal1->coff2detCoff2Rel->coff2_sem_relId,
        //     ])
        //     ->all();
        if ($sections) {
            foreach ($sections as $section) {
                // it should not be blocked by the same course:
                $coff2dets = $section->getCourseOffering2Details()
                    ->select('id')
                    ->andWhere(" id = $offeringdetailId ")
                    ->column();
                if ($this->checkIntersection($coff2dets)) {
                    return true;
                }
            }
        }
        // $courseOfferingEntries = $this->getCourseOfferings($course);
        // 1.get All sections for which this course offered.
        // 2.for each sections, get courses offered =>array_of_coff2Details
        // 3.get all examRoomSlots inthis dateTimeSlot,
        // 4.id there is any intersection between array_of_coff2Details and examRoomSlot->coff2detail_IDs,
        // then it is point of conflict.
    }

    public function courseAvailableInAnotherSchedule2($offeringdetailId)
    {
        // check through dateTimeSlot
        $offeringDetal1 = CourseOffering2Detail::findOne($offeringdetailId);
        $course = $offeringDetal1->coff2detCourseRel;
        $courseOfferingEntries = $this->getCourseOfferings($course);
        $dateTimeScehdules = $this
            ->dateTimeSlotRel
            ->examScheduleEntryRel
            ->getExDateTimeSchedules()
            ->andWhere(['exam_schedule_entry_relId' => $this->dateTimeSlotRel->exam_schedule_entry_relId])
            ->all();
        if ($dateTimeScehdules) {
            foreach ($dateTimeScehdules as $dateTimeScehdule) {
                if ($dateTimeScehdule->id == $this->date_time_slot_relId) {
                    continue;
                }
                foreach ($dateTimeScehdule->exExamRoomSlots as $exexamRoomSlot) {
                    if ($exexamRoomSlot->id == $this->id) {
                        continue;
                    }
                    $offeringDetal2s = $exexamRoomSlot->offering_detail_IDs ? json_decode($exexamRoomSlot->offering_detail_IDs, true) : [];
                    foreach ($courseOfferingEntries as $offering1Id) {
                        if (in_array($offering1Id, $offeringDetal2s)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }

    // checks and removes
    public function sanitizeOfferings()
    {
        $offerings = $this->offering_detail_IDs ? json_decode($this->offering_detail_IDs, true) : [];
        if (!empty($offerings)) {
            foreach ($offerings as $index => $offeringdetailId) {
                if ($this->courseAvailableInAnotherSchedule2($offeringdetailId)) {
                    unset($offerings[$index]);
                }
            }
        }
        return $offerings;
    }

    public function removeMultipleSchedules($offeringIds)
    {
        $trees = [];
        if (!empty($offeringIds)) {
            foreach ($offeringIds as $id) {
                if ($this->courseAvailableInAnotherSchedule2($id)) {
                    // unset($offeringIds[$id]);
                } else {
                    $trees[] = $id;
                }
            }
        }
        return $trees;
    }

    public function removeOffering($offeringDetailid)
    {
        // remove offeringdetail ID
        // remove examinees if allocated any
        $examinees = $this->getExExaminees()->andFilterWhere([
            'offering_detail_relId' => $offeringDetailid,
            'exam_slot_relId' => $this->id
        ])->all();
        if ($examinees) {
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($examinees as $examine) {
                if ($examine->delete()) {
                    continue;
                } else {
                    $transaction->rollBack();
                    return 'Error while removing Examinee: ' . json_encode($examine->getErrors());
                }
            }
            $transaction->commit();
        }
        $this->offering_detail_IDs = json_encode(array_diff(json_decode($this->offering_detail_IDs, true), [$offeringDetailid]));
        return;
    }

    public function appendOfferings()
    {
        // $this->offeringIds = json_decode();
        if (is_null($this->offering_detail_IDs) || !json_decode($this->offering_detail_IDs, true)) {
            $this->offering_detail_IDs = $this->offeringIds;
        } else {
            // $this->offering_detail_IDs =  ;
            $offeringIds = json_decode($this->offeringIds, true);
            $offeringIds = array_merge(json_decode($this->offering_detail_IDs, true), $offeringIds);
            $offeringIds = array_unique($offeringIds);
            $offeringIds = $this->removeMultipleSchedules($offeringIds);
            $this->offering_detail_IDs = json_encode($offeringIds);
        }
        return;
    }

    public function getData()
    {
        $this->data = md5(serialize((object) [
            'date_time_slot_relId' => $this->date_time_slot_relId,
            'offering_detail_IDs' => $this->offering_detail_IDs,
            'class_room_relId' => $this->class_room_relId,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function getRoomHeader()
    {
        return $this->classRoomRel->room;
    }

    public function checkIfExists($id = null)
    {
        if ($id) {
            return self::find()
                ->where([
                    'date_time_slot_relId' => $this->date_time_slot_relId,
                    'class_room_relId' => $this->class_room_relId,
                ])
                ->andWhere(" id <> $id ")
                ->one();
        }
        return self::findOne([
            'date_time_slot_relId' => $this->date_time_slot_relId,
            'class_room_relId' => $this->class_room_relId,
        ]);
    }

    public function getSections()
    {
        $offeringdetails = $this->offering_detail_IDs ? json_decode($this->offering_detail_IDs, true) : [];
        if (empty($offeringdetails)) {
            return 0;
        }
        $sections = [];
        foreach ($offeringdetails as $index => $offeringdetail) {
            $coff2Detail = CourseOffering2Detail::findOne($offeringdetail);
            $sections[] = $coff2Detail->coff2detCoff2Rel->coff2GroupRel->getGsDisp();
        }

        return implode('<br> ', $sections);
    }

    public function getHeaderText()
    {
        return $this->dateTimeSlotRel->getHeaderText() . ' - [' . $this->classRoomRel->room . ']';
    }

    public function getRoomList()
    {
        $alreadyBookedRooms = ExExamRoomSlot::find()
            ->select('class_room_relId')
            ->where(['date_time_slot_relId' => $this->date_time_slot_relId])
            ->asArray();
        if (empty($alreadyBookedRooms)) {
            return CsAvailableClassRoom::find()
                ->select(['room', 'id'])
                ->where(['status' => 1])
                ->indexBy('id')
                ->column();
        }
        return CsAvailableClassRoom::find()
            ->select(['room', 'id'])
            ->where(['status' => 1])
            ->andWhere([' NOT IN ', 'id', $alreadyBookedRooms])
            ->indexBy('id')
            ->column();
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
     * Gets query for [[DateTimeSlotRel]].
     *
     * @return \yii\db\ActiveQuery|ExDateTimeScheduleQuery
     */
    public function getDateTimeSlotRel()
    {
        return $this->hasOne(ExDateTimeSchedule::class, ['id' => 'date_time_slot_relId']);
    }

    /**
     * Gets query for [[ClassRoomRel]].
     *
     * @return \yii\db\ActiveQuery|CsAvailableClassRoomQuery
     */
    public function getClassRoomRel()
    {
        return $this->hasOne(CsAvailableClassRoom::class, ['id' => 'class_room_relId']);
    }

    public function getAllocatedStudentByOfferingDetailId($offeringDetailId)
    {
        return $this
            ->getExExaminees()
            ->andWhere(['offering_detail_relId' => $offeringDetailId])
            ->all();
    }

    /**
     * Gets query for [[ExExaminees]].
     *
     * @return \yii\db\ActiveQuery|ExExamineesQuery
     */
    public function getExExaminees()
    {
        return $this->hasMany(ExExaminees::class, ['exam_slot_relId' => 'id']);
    }

    /**
     * Gets query for [[ExInvAttendanceForms]].
     *
     * @return \yii\db\ActiveQuery|ExInvAttendanceFormQuery
     */
    public function getExInvAttendanceForms()
    {
        return $this->hasMany(ExInvAttendanceForm::class, ['exam_slot_relId' => 'id']);
    }

    public function getExExamOfferingDetails()
    {
        return CourseOffering2Detail::find()
            ->where([
                'id' => $this->offering_detail_IDs ? json_decode($this->offering_detail_IDs, true) : null,
            ]);
        // return $this->hasMany(CourseOffering2Detail::class, ['id' => $offeringIds]);
    }

    /**
     * Gets query for [[ExInvigilators]].
     *
     * @return \yii\db\ActiveQuery|ExInvigilatorQuery
     */
    public function getExInvigilators()
    {
        return $this->hasMany(ExInvigilators::class, ['exam_room_slot_relId' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return ExExamRoomSlotQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ExExamRoomSlotQuery(get_called_class());
    }
}
