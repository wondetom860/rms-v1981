<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "course".
 *
 * @property integer $id
 * @property string $course_code
 * @property string $course_title
 * @property integer $course_crdthr
 * @property integer $course_dept_relId
 * @property integer $course_lecture_hour
 * @property integer $course_lab_hour
 * @property integer $course_ETCS
 * @property integer $course_type
 * @property integer $course_entry_type
 * @property integer $course_tutorial_hour
 * @property integer $course_created_by
 * @property string $course_created_at
 * @property string $course_equivalet
 * @property integer $course_updated_by
 * @property string $course_updated_at
 *
 * @property User $courseCreatedBy
 * @property Department $courseDeptRel
 * @property User $courseUpdatedBy
 * @property CourseCatalog[] $courseCatalogs
 * @property CourseOffering[] $courseOfferings
 */
class Course extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    public function getCourseTypeOptios()
    {

        // course type by assessment mode
        return [
            1 => 'Graded',
            2 => 'P/F',
            3 => 'Thesis',
        ];
    }

    public function getCourseTypeName()
    {
        $types = $this->getCourseTypeOptios();
        return isset($types[$this->course_type]) ? $types[$this->course_type] : "<span class='label bg-danger'>Not Set</span>";
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['course_code', 'course_title', 'course_crdthr', 'course_dept_relId', 'course_lecture_hour', 'course_created_by', 'course_created_at', 'course_updated_by', 'course_updated_at'], 'required'],
            [['course_crdthr', 'course_ETCS', 'course_type', 'course_dept_relId', 'course_lecture_hour', 'course_lab_hour', 'course_tutorial_hour', 'course_created_by', 'course_updated_by', 'course_entry_type'], 'integer'],
            [['course_created_at', 'course_updated_at'], 'safe'],
            [['course_code'], 'string', 'max' => 20],
            [['course_equivalet'], 'string'],
            [['course_title'], 'string', 'max' => 90],
            [['course_code'], 'unique'],
            [['course_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['course_created_by' => 'id']],
            [['course_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['course_dept_relId' => 'id']],
            [['course_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['course_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'course_code' => Yii::t('rms', 'Course Code'),
            'course_title' => Yii::t('rms', 'Course Title'),
            'course_crdthr' => Yii::t('rms', 'Crdthr'),
            'course_type' => Yii::t('rms', 'Course Type'),
            'course_ETCS' => Yii::t('rms', 'ETCS'), //course_equivalet
            'course_equivalet' => Yii::t('rms', 'Equivalent Course(s)'),
            'course_entry_type' => Yii::t('rms', 'Entry Type'),
            'course_dept_relId' => Yii::t('rms', 'Department'),
            'course_lecture_hour' => Yii::t('rms', 'Lecture Hour'),
            'course_lab_hour' => Yii::t('rms', 'Lab Hour'),
            'course_tutorial_hour' => Yii::t('rms', 'Tutorial Hour'),
            'course_created_by' => Yii::t('rms', 'created By'),
            'course_created_at' => Yii::t('rms', 'created At'),
            'course_updated_by' => Yii::t('rms', 'Updated By'),
            'course_updated_at' => Yii::t('rms', 'Updated At'),
        ];
    }

    public function setCourseType($type)
    {
        $this->checkColumnName('course_type');
        $this->course_type = $type;
        $this->course_updated_by = Yii::$app->user->identity->id;
        $this->course_updated_at = date('Y-m-d g:i:s');
        if ($this->save()) {
            return 1;
        } else {
            return "Failed to change course type. Error: " . json_encode($this->errors);
        }
    }

    private function checkColumnName($col_name)
    {
        $sql = "SHOW COLUMNS FROM `course` LIKE 'course_type'";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        // var_dump(count($result) > 0);exit;
        if (count($result) > 0) {
            return;
        } else {
            $sql = "ALTER TABLE `course` ADD COLUMN `course_type` SMALLINT(1) default 1 AFTER `course_equivalet`";
            $result = Yii::$app->db->createCommand($sql)->execute();
            $this->refresh();
            // $this->refreshTableSchema('course_catalog');
        }
    }

    public function getCourseEntryType()
    {
        return $this->course_entry_type == 1 ? 'Regular' : 'Transfer';
    }

    function getCourseCredits($crdtHrsOnly = NULL)
    {
        // CT-2121(3-2-3/0)
        if ($crdtHrsOnly) {
            return "$this->course_crdthr-$this->course_lecture_hour-$this->course_lab_hour/$this->course_tutorial_hour";
        }
        return "$this->course_code($this->course_crdthr-$this->course_lecture_hour-$this->course_lab_hour/$this->course_tutorial_hour)|$this->course_ETCS";
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'course_created_by']);
    }

    public function getPreRequisiteForCourses()
    {
        // returns a comma separated string of course codes for which this course is a pre-requisit
        $preqForCourses = CoursePrerequisit::find()->where(['preq_preq_course_relId' => $this->id])->all();
        $preq_for = [];
        $pfs = [];
        if ($preqForCourses) {
            foreach ($preqForCourses as $pfc) {
                if (in_array($pfc->preq_main_course_relId, $pfs)) {
                    continue;
                } else {
                    array_push($pfs, $pfc->preq_main_course_relId);
                    $preq_for[] = "<span title='{$pfc->preqMainCourseRel->course_title}'>{$pfc->preqMainCourseRel->course_code}</span>";
                }
            }
            return implode(", ", $preq_for);
        }
        return NULL;
    }

    public function getEquivalentCourseIds()
    {
        // returns the equivalent course ids as an array
        // array_multisort(json_decode($this->course_equivalet, true), SORT_DESC); // sort in descending order
        // sort in descending order
        if ($this->course_equivalet) {
            $courseEquivalents = json_decode($this->course_equivalet, true);
            if (is_array($courseEquivalents)) {
                array_multisort($courseEquivalents, SORT_DESC);
                return $courseEquivalents;
            }
        }
        return NULL;

        // return $this->course_equivalet ? array_multisort(array_values(json_decode($this->course_equivalet, true)),SORT_DESC) : NULL;
    }

    public function cascadeEquivalence()
    {
        $equivalentCourses = $this->getEquivalentCourses()->all();

        foreach ($equivalentCourses as $eCourse) {
            $eCourse->course_equivalet = $eCourse->course_equivalet ? array_merge(json_decode($eCourse->course_equivalet, true), [$this->id]) : [$this->id];
            $eCourse->course_equivalet = json_encode($eCourse->course_equivalet);
            $eCourse->course_updated_by = Yii::$app->user->identity->id;
            $eCourse->course_updated_at = date('Y-m-d g:i:s');
            $eCourse->save();
        }
    }

    public function removeEqivalentCourse($courseId)
    {
        // removes $courseId from $this courses equivalent_courses_list
        $eq_course_ids = $this->course_equivalet ? json_decode($this->course_equivalet, true) : NULL;

        if ($eq_course_ids) {
            if (($key = array_search($courseId, $eq_course_ids)) !== false) {
                unset($eq_course_ids[$key]);
            }
            $this->course_equivalet = json_encode($eq_course_ids);
            $this->course_updated_by = Yii::$app->user->identity->id;
            $this->course_updated_at = date('Y-m-d g:i:s');

            $this->save();
        }
    }

    public function getEquivalentCourses()
    {
        return $this->course_equivalet === null ? [] : Course::find()->where(['id' => array_values(json_decode($this->course_equivalet, true))]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'course_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'course_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCatalogs()
    {
        return $this->hasMany(CourseCatalog::class, ['cocat_course_relId' => 'id']);
    }

    public function getCoursePrerequisits()
    { //gets all course pre-requisits as per the configuration
        return $this->hasMany(CoursePrerequisit::class, ['preq_main_course_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering2Detail::class, ['coff2det_course_relId' => 'id']);
    }
}
