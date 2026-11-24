<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "attendance".
 *
 * @property integer $id
 * @property integer $a_instructor_relId
 * @property integer $a_course_relId
 * @property integer $a_group_relId
 * @property integer $a_coff_relId
 * @property string $a_createdAt
 * @property string $a_updatedAt
 *
 * @property Course $aCourseRel
 * @property Instructor $aInstructorRel
 * @property WGroupSetup $aGroupRel
 * @property CourseOffering2Detail $aCoffRel
 * @property Attendancedetail[] $attendancedetails
 */
class Attendance extends \yii\db\ActiveRecord
{

    public $course;

    public $acadYear;
    public $semester;
    public $presents;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'attendance';
    }
 
    /**
     * @inheritdoc
     */
    public function rules()
    {
        // $this->course == is_null($this->course) ? (isset($this->id) ? $this->aCoffRel->coff2CourseDetail->course_title : NULL) : $this->course;
        return [
            [['a_instructor_relId', 'a_course_relId', 'a_hour', 'a_group_relId', 'a_coff_relId'], 'integer'],
            [['a_group_relId', 'a_coff_relId'], 'required'],
            [['a_hour'],'integer', 'min' => 1, 'max' => 3],
            [['a_createdAt', 'a_updatedAt','a_presentStudentsList','course'], 'safe'],
            [['a_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['a_course_relId' => 'id']],
            [['a_instructor_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Instructor::class, 'targetAttribute' => ['a_instructor_relId' => 'id']],
            [['a_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['a_group_relId' => 'id']],
            [['a_coff_relId'], 'exist', 'skipOnError' => true, 'targetClass' => CourseOffering2Detail::class, 'targetAttribute' => ['a_coff_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'course' => Yii::t('rms', 'Course'),
            'semester' => Yii::t('rms', 'Semester'),
            'acadYear' => Yii::t('rms', 'Academic Year'),
            'a_instructor_relId' => Yii::t('rms', 'Instructor'),
            'a_course_relId' => Yii::t('rms', 'Course'),
            'a_group_relId' => Yii::t('rms', 'Group'),
            'a_hour' => Yii::t('rms', 'Hour'),
            'a_coff_relId' => Yii::t('rms', 'Course Offering entry'),
            'a_createdAt' => Yii::t('rms', 'Submitted At'),
            'a_updatedAt' => Yii::t('rms', 'Updated At'),
            // 'a_created_by' => Yii::t('rms', 'Collected By'),
        ];
    }

    public function getAttendanceReport2($std,$courseId,$gsetId){
        // get student attendance report per course:
        $student = $std;
        $attendanceData = Attendance::find()
            ->where(['attendance.a_course_relId' => $courseId])
            ->andWhere(['attendance.a_group_relId' => $gsetId]);
        $numberOfClasses = $attendanceData->count();

        if ($numberOfClasses < 1) {
            return [' ',0];
        }
        $attendanceData = $attendanceData->all();
        $total = 0;
        $p = 0;
        foreach ($attendanceData as $aData) {
            $ahour = $aData->a_hour;
            if (!is_null($aData->a_presentStudentsList) && in_array($student->id, json_decode($aData->a_presentStudentsList,true))) {
                $status = 'P';
                // $hr = $ahour == 0 ? 1:$ahour;
                $p +=  $ahour;
            }
            $total +=  $ahour == 0 ? 1:$ahour;
          }
        $g = round($p / $total *100,2);
        return [100-$g,$total];//100-40=60, 100-60=40;
    }

    public function getAttendanceReport($semreg,$courseId,$gsetId){
        // get student attendance report per course:
        $student = $semreg->semregStdRel;
        $attendanceData = Attendance::find()
            ->where(['attendance.a_course_relId' => $courseId])
            ->andWhere(['attendance.a_group_relId' => $gsetId]);
        $numberOfClasses = $attendanceData->count();

        if ($numberOfClasses < 1) {
            return null;
        }
        $attendanceData = $attendanceData->all();
        $total = 0;
        $p = 0;
        foreach ($attendanceData as $aData) {
            $ahour = $aData->a_hour;
            if (!is_null($aData->a_presentStudentsList) && in_array($student->id, json_decode($aData->a_presentStudentsList,true))) {
                $status = 'P';
                // $hr = $ahour == 0 ? 1:$ahour;
                $p +=  $ahour;
            }
            $total +=  $ahour == 0 ? 1:$ahour;
          }
        $g = round($p / $total *100,1);
        return 100-$g;
    }

    public function getAbsents(){
        $enrolledStudents = SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id='.SemesterRegistration2Detail::tableName().'.srd2_semreg_relId')
            ->innerJoin('student','student.id=semester_registration_2.semreg_std_relId')
            ->where([
                'srd2_group_relId' => $this->a_group_relId,
                'srd2_course_relId' => $this->a_course_relId
            ])
            ->select('student.id')
            ->all();
        $stdIds = [];
        if ($enrolledStudents && count($enrolledStudents) > 0) {
            foreach ($enrolledStudents as $std) {
                $stdIds[] = $std->id;
            }
        }

        $aList = (array)json_decode($this->a_presentStudentsList,true);
        return Student::find()
            ->innerJoin('user','user.id=student.std_user_relId')
            ->where(['NOT IN','student.id',$aList])
            ->andWhere(['student.id' => $stdIds])
            ->orderBy('first_name')
            ->all();
    }


    public function getPresentStudent(){
        $aList = json_decode($this->a_presentStudentsList,true);
        // var_dump($aList);die();
        if (is_array($aList) && count($aList) > 0) {
            return Student::find()
                ->innerJoin('user','user.id=student.std_user_relId')
                ->where(['student.id' => $aList])
                ->orderBy('first_name')
                ->all();
        }

        // var_dump(array_values($aList));die();
        
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getACourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'a_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAInstructorRel()
    {
        return $this->hasOne(Instructor::class, ['id' => 'a_instructor_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAGroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'a_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getACoffRel()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['id' => 'a_coff_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttendancedetails()
    {
        return $this->hasMany(Attendancedetail::class, ['ad_attendance_relId' => 'id']);
    }
}
