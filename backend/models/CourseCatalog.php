<?php

namespace backend\models;

use Yii;
use yii\caching\CacheInterface;

/**
 * This is the model class for table "course_catalog".
 *
 * @property integer $id
 * @property integer $cocat_admission_relId
 * @property integer $cocat_program_relId
 * @property integer $cocat_dept_relId//
 * @property integer $cocat_entry_type
 * @property integer $cocat_course_relId
 * @property integer $min_pass_grade_relId
 * @property integer $course_category_relId
 * @property integer $cocat_clslvl_relId
 * @property integer $cocat_spec_relId
 * @property integer $cocat_sem_relId 
 * @property integer $cocat_status
 * @property integer $cocat_created_by
 * @property string $cocat_created_at
 * @property integer $cocat_updated_by
 * @property string $cocat_updated_at
 *
 * @property CocatDetail[] $cocatDetails
 * @property Admission $cocatAdmissionRel
 * @property ClassLevel $cocatClslvlRel
 * @property User $cocatCreatedBy
 * @property Department $cocatDeptRel
 * @property Program $cocatProgramRel
 * @property Semester $cocatSemRel
 * @property Specialization $cocatSpecRel
 * @property User $cocatUpdatedBy
 * @property CourseOffering[] $courseOfferings
 * @property SemesterRegistration[] $semesterRegistrations
 */
class CourseCatalog extends \yii\db\ActiveRecord
{
    public $filterDept;
    public $maxCredit;
    public $collectedCredit;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_catalog';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        // 'cocat_clslvl_relId', 'cocat_sem_relId', 
        return [
            [['cocat_admission_relId', 'cocat_program_relId', 'cocat_created_by', 'cocat_created_at', 'cocat_updated_by', 'cocat_course_relId', 'cocat_updated_at', 'cocat_courseMajor', 'cocat_courseProject', 'cocat_courseelectives'], 'required'],
            [['cocat_admission_relId', 'cocat_entry_type', 'cocat_program_relId', 'cocat_dept_relId', 'cocat_clslvl_relId', 'cocat_spec_relId', 'cocat_sem_relId', 'cocat_created_by', 'cocat_updated_by', 'maxCredit', 'collectedCredit', 'cocat_courseMajor', 'cocat_courseProject', 'cocat_status', 'cocat_courseelectives', 'min_pass_grade_relId', 'course_category_relId'], 'integer'],
            [['cocat_created_at', 'cocat_updated_at', 'preq_course_ids'], 'safe'],
            [['cocat_admission_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['cocat_admission_relId' => 'id']],
            [['cocat_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['cocat_clslvl_relId' => 'id']],
            [['cocat_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cocat_created_by' => 'id']],
            [['cocat_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['cocat_dept_relId' => 'id']],
            [['cocat_program_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['cocat_program_relId' => 'id']],
            [['cocat_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['cocat_sem_relId' => 'id']],
            [['cocat_spec_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Specialization::class, 'targetAttribute' => ['cocat_spec_relId' => 'id']],
            [['cocat_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::class, 'targetAttribute' => ['cocat_course_relId' => 'id']],
            [['cocat_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cocat_updated_by' => 'id']],
            [['cocat_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['cocat_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'cocat_admission_relId' => Yii::t('rms', 'Admission'),
            'cocatAdmissionRel.admission_name' => Yii::t('rms', 'Admission'),
            'course_category_relId' => Yii::t('rms', 'Course Category'),
            'min_pass_grade_relId' => Yii::t('rms', 'Pass Grade (Min)'),
            'cocat_program_relId' => Yii::t('rms', 'Program'),
            'cocatProgramRel.program_name' => Yii::t('rms', 'Program'),
            'cocat_dept_relId' => Yii::t('rms', 'Department'),
            'cocatDeptRel.dept_name' => Yii::t('rms', 'Department'),
            'cocat_clslvl_relId' => Yii::t('rms', 'Class Level'),
            'cocatClslvlRel.clslvl_name' => Yii::t('rms', 'Class Level'),
            'cocatClslvlRel.clslvl_num' => Yii::t('rms', 'Class Level'),
            'cocat_spec_relId' => Yii::t('rms', 'Specialization'),
            'cocatSpecRel.spec_name' => Yii::t('rms', 'Specialization'),
            'cocat_sem_relId' => Yii::t('rms', 'Semester'),
            'preq_course_ids' => Yii::t('rms', 'Pre-Requisites'),
            'cocatSemRel.semester_name' => Yii::t('rms', 'Semester'),
            'cocat_course_relId' => Yii::t('rms', 'Course'),
            'cocatCourseRel.course_title' => Yii::t('rms', 'Course'),
            'cocat_created_by' => Yii::t('rms', 'Created By'),
            'cocat_courseMajor' => Yii::t('rms', 'Major?'),
            'cocat_courseProject' => Yii::t('rms', 'Project?'),
            'cocat_courseelectives' => Yii::t('rms', 'Elective?'),
            'cocat_created_at' => Yii::t('rms', 'Created At'),
            'cocat_updated_by' => Yii::t('rms', 'Updated By'),
            'cocat_updated_at' => Yii::t('rms', 'Updated At'),
            'cocat_status' => Yii::t('rms', 'Status'),
        ];
    }

    private function getMajorCategoryId()
    {
        $major_name = CourseCategory::find()->where(['catgeory_name' => 'MAJOR'])->one();
        return $major_name ? $major_name->id : null;
    }

    public function determineCategory()
    {
        if ($this->cocat_courseMajor && is_null($this->course_category_relId)) {
            $majoryCategoryId = $this->getMajorCategoryId();

            if ($majoryCategoryId) {
                $this->course_category_relId = $majoryCategoryId;
                // $this->min_pass_grade_relId = $major_name->pass_grade_relId;//grade varies with program but courseCategory model doe not consider this.
                $this->save();
                return;
            }
        }
    }

    public function savePreqBatch($majorCourses)
    {
        $majorCourses = explode(',', $majorCourses);
        $addedd = $this->preq_course_ids ? explode(',', $this->preq_course_ids) : [];
        if ($majorCourses) {
            foreach (array_unique($majorCourses)  as $mCourse) {
                if ($this->preq_course_ids && in_array($mCourse, $addedd)) {
                    continue;
                } else {
                    $pmodel = new CoursePrerequisit();
                    $pmodel->preq_adm_relId = $this->cocat_admission_relId;
                    $pmodel->preq_prog_relId = $this->cocat_program_relId;
                    $pmodel->preq_main_course_relId = $this->cocat_course_relId;
                    $pmodel->preq_preq_course_relId = $mCourse;
                    $pmodel->preq_dept_relId = $this->cocat_dept_relId;
                    $pmodel->preq_spec_relId = $this->cocat_spec_relId;
                    $pmodel->preq_created_at = $pmodel->preq_updated_at = date('Y-m-d');
                    $pmodel->preq_updated_by = $pmodel->preq_created_by = Yii::$app->user->identity->id;
                    if ($pmodel->save()) {
                        array_push($addedd, $mCourse);
                    }
                }
            }
        }
        $this->preq_course_ids = join(',', array_unique($addedd));
        $this->update(false);
        return 1;
    }

    public function getAllProgramMajorCourses()
    {
        $major_category_id = $this->getMajorCategoryId();
        $program_major_courses = self::find()
            ->where([
                'cocat_admission_relId' => $this->cocat_admission_relId,
                'cocat_program_relId' => $this->cocat_program_relId,
                'course_category_relId' => $major_category_id,
                'cocat_dept_relId' => $this->cocat_dept_relId
            ])
            ->andWhere([
                'OR',
                ['cocat_spec_relId' => $this->cocat_spec_relId],
                ['cocat_spec_relId' => NULL]
            ])
            ->andWhere(' cocat_course_relId <> ' . $this->cocat_course_relId)
            ->orderBy([
                'cocat_clslvl_relId' => SORT_ASC,
                'cocat_sem_relId' => SORT_ASC,
            ])
            ->groupBy('cocat_course_relId')
            ->all();
        return $program_major_courses;
    }

    private function checkColumnName($col_name)
    {
        $sql = "SHOW COLUMNS FROM `course_catalog` LIKE 'preq_course_ids'";
        $result = Yii::$app->db->createCommand($sql)->queryAll();
        // var_dump(count($result) > 0);exit;
        if (count($result) > 0) {
            return;
        } else {
            $sql = "ALTER TABLE `course_catalog` ADD COLUMN `preq_course_ids` TINYTEXT NULL AFTER `course_category_relId`";
            $result = Yii::$app->db->createCommand($sql)->execute();
            $this->refresh();
            // $this->refreshTableSchema('course_catalog');
        }
    }

    public function refreshTableSchema($name)
    {
        $rawName = $this->getRawTableName($name);
        unset($this->_tableMetadata[$rawName]);
        $this->_tableNames = [];
        /* @var $cache CacheInterface */
        $cache = is_string($this->db->schemaCache) ? Yii::$app->get($this->db->schemaCache, false) : $this->db->schemaCache;
        if ($this->db->enableSchemaCache && $cache instanceof CacheInterface) {
            $cache->delete($this->getCacheKey($rawName));
        }
    }

    public function setPreqsToCatalog($preqCourses)
    {
        // check if column name exists, and create one if NOT
        $this->checkColumnName('preq_course_ids');
        // if(!$preqCourses){
        //     $preqCourses = $this->getPreqsByCocat();
        // }
        $courseIds = array_column($preqCourses, 'preq_preq_course_relId');
        $this->preq_course_ids = join(',', array_unique($courseIds));
        $this->update(false);
    }

    public function getPreqsByCocat()
    {
        $preqCourses = CoursePrerequisit::find()
            ->where([
                'preq_adm_relId' => $this->cocat_admission_relId,
                'preq_prog_relId' => $this->cocat_program_relId,
                'preq_main_course_relId' => $this->cocat_course_relId,
            ])
            ->andWhere([
                'OR',
                ['preq_dept_relId' => $this->cocat_dept_relId],
                ['preq_dept_relId' => NULL]
            ])
            ->andWhere([
                'OR',
                ['preq_spec_relId' => $this->cocat_spec_relId],
                ['preq_spec_relId' => NULL]
            ])
            ->groupBy('preq_preq_course_relId')
            ->all();

        $this->setPreqsToCatalog($preqCourses);

        return $preqCourses;
    }

    public function getCatalogSet()
    {
        // returns ClassLevel-Semestet of academic Year.
        $clslvl = $this->cocat_clslvl_relId ? $this->cocatClslvlRel->clslvl_num : '-';
        $sem = $this->cocat_sem_relId ? $this->cocatSemRel->semester_name : '-';

        return $clslvl . '/' . $sem;
    }



    // public function getCatalogSetup()
    // {
    //     // returns ClassLevel-Semestet of academic Year.
    //     $clslvl = $this->cocat_clslvl_relId ? $this->cocatClslvlRel->clslvl_name : '-';
    //     $sem = $this->cocat_sem_relId ? $this->cocatSemRel->semester_name : '-';

    //     return $clslvl . '/' . $sem;
    // }

    public function getCatalogEntryType()
    {
        if ($this->cocat_entry_type != $this->cocatCourseRel->course_entry_type) {
            $this->cocat_entry_type = $this->cocatCourseRel->course_entry_type;
            $this->save();
        }

        return $this->cocatCourseRel->getCourseEntryType();
    }

    public function getCatalogCourse3($gset, $student = null)
    {
        // get all gsets in which student was assigned so far, 
        $deptIds = [];
        if (!is_null($student)) {
            $gasss = WGroupAssignment::find()
                ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
                ->where(['gas_std_relId' => $student->id])
                ->groupBy('gs_dept_relId')
                ->all();
            if ($gasss && count($gasss) > 0) {
                foreach ($gasss as $keyGas) {
                    $deptIds[] = $keyGas->gasGsetRel->gs_dept_relId;
                }
            }
        } else {
            $deptIds = $gset->gs_dept_relId;
        }
        // var_dump($deptIds);die();

        $catCourses = $this->find()
            ->filterWhere([
                'cocat_admission_relId' => $gset->gs_adm_relId,
                'cocat_program_relId' => $gset->gs_prog_relId,
                'cocat_dept_relId' => $deptIds,
            ]);
        // if (strtoupper($gset->gsProgRel->program_name) != 'MASTERS') {
        //     $catCourses = $catCourses->indexBy('cocat_courseMajor');
        // }
        if (!is_null($gset->gs_spec_relId)) {
            $catCourses = $catCourses->andFilterWhere([
                'OR',
                ['cocat_spec_relId' => $gset->gs_spec_relId],
                ['cocat_spec_relId' => NULL]
            ]);
        }
        // ->groupBy('cocat_course_relId')
        $catCourses = $catCourses->all();

        return $catCourses;
    }

    /**
     * get Catalog entries for prerquisite course
     * @param mixed $preqCourse
     * @return void|array
     */
    public function getCatalogEntry($preqCourse)
    {
        return self::find()
            ->where([
                'cocat_admission_relId' => $this->cocat_admission_relId,
                'cocat_program_relId' => $this->cocat_program_relId,
                'cocat_dept_relId' => $this->cocat_dept_relId,
                'cocat_course_relId' => $preqCourse->preq_preq_course_relId,
            ])
            ->orderBy(['cocat_clslvl_relId' => SORT_ASC, 'cocat_sem_relId' => SORT_ASC])
            ->all();
    }

    public function gteCourseECTs()
    {
        // https://www.mi.fu-berlin.de/en/data-science/_inhaltselemente-rd/downloads/Conversion-of-credit-points.pdf
        // https://www.edunation.co/wp-content/uploads/2023/10/LUT-UNIVERSITY_-ECTS-Conversion.pdf

        // Conversion factor = 180/total_program_minimum_credit_hrs
        // european standard total_credit_hours for UG program=180 and for PG(2nd) Degree=60(1Yr) & 120(2Yr).
        $total_credit_hrs_program = $this->cocatProgramRel->total_crdt_hrs ? $this->cocatProgramRel->total_crdt_hrs : $this->cocatProgramRel->program_duration * 60;
        $conversion_factor = round(180 / $total_credit_hrs_program, 3, PHP_ROUND_HALF_UP); //this way we can get conversion factor

        return $conversion_factor * $this->cocatCourseRel->course_crdthr;
    }

    public function gteCourseECTs2()
    {
        // https://www.mi.fu-berlin.de/en/data-science/_inhaltselemente-rd/downloads/Conversion-of-credit-points.pdf
        // https://www.edunation.co/wp-content/uploads/2023/10/LUT-UNIVERSITY_-ECTS-Conversion.pdf

        // Conversion factor = 180/total_program_minimum_credit_hrs
        // european standard total_credit_hours for UG program=180 and for PG(2nd) Degree=60(1Yr) & 120(2Yr).
        $course = $this->cocatCourseRel;
        $total_hrs_per_week = $course->course_lecture_hour + $course->course_lab_hour + $course->course_tutorial_hour;
        $total_weeks_for_the_subject = $course->course_total_weeks ?? 18;
        return round(($total_hrs_per_week * $total_weeks_for_the_subject) / 30, 3, PHP_ROUND_HALF_UP); //this way we can get conversion factor
    }

    public function getCatalogCourse4($gset)
    {
        $catCourses = $this->find()
            ->where([
                'cocat_admission_relId' => $gset->gs_adm_relId,
                'cocat_program_relId' => $gset->gs_prog_relId,
                'cocat_dept_relId' => $gset->gs_dept_relId,
                'cocat_clslvl_relId' => $gset->gs_clslvl_relId,
                'cocat_sem_relId' => $gset->gs_sem_relId,
            ]);
        if (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
            $catCourses = $catCourses->andWhere(['<>', 'cocat_courseelectives', 1]);
        }
        if (!is_null($gset->gs_spec_relId)) {
            $catCourses = $catCourses->andWhere(['OR', ['cocat_spec_relId' => $gset->gs_spec_relId], ['cocat_spec_relId' => NULL]]);
        }
        $catCourses = $catCourses->all(); //->groupBy('cocat_course_relId')

        return $catCourses;
    }

    public function getCatalogCourse2($gset)
    {
        $catCourses = $this->find()
            ->where([
                'cocat_admission_relId' => $gset->gs_adm_relId,
                'cocat_program_relId' => $gset->gs_prog_relId,
                'cocat_dept_relId' => $gset->gs_dept_relId,
            ]);
        if (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
            $catCourses = $catCourses->andWhere(['<>', 'cocat_courseelectives', 1]);
        }
        if (!is_null($gset->gs_spec_relId)) {
            $catCourses = $catCourses->andWhere(['OR', ['cocat_spec_relId' => $gset->gs_spec_relId], ['cocat_spec_relId' => NULL]]);
        }
        $catCourses = $catCourses->all(); //->groupBy('cocat_course_relId')

        return $catCourses;
    }

    public function CatalogProjectCourses($gsetKey)
    {
        // 
        return $this->find()
            ->where([
                'cocat_admission_relId' => $gsetKey->gs_adm_relId,
                'cocat_program_relId' => $gsetKey->gs_prog_relId,
                'cocat_dept_relId' => $gsetKey->gs_dept_relId,
            ])
            ->andWhere(['cocat_courseProject' => 1])
            ->andWhere(['OR', ['cocat_clslvl_relId' => $gsetKey->gs_clslvl_relId], ['cocat_clslvl_relId' => NULL]])
            ->all();
    }

    public function checkIfOffered()
    {
        return CourseOffering2Detail::find()->where(['coff2det_cocat_relId' => $this->id])->count() > 0;
    }

    public function isProjectCourses($gsetObject, $courseId)
    {
        // 
        $cocat = $this->find()
            ->where([
                'cocat_admission_relId' => $gsetObject->gs_adm_relId,
                'cocat_program_relId' => $gsetObject->gs_prog_relId,
                'cocat_dept_relId' => $gsetObject->gs_dept_relId,
            ])
            ->andWhere(['cocat_courseProject' => 1])
            ->andWhere(['cocat_course_relId' => $courseId])
            ->andWhere(['OR', ['cocat_clslvl_relId' => $gsetObject->gs_clslvl_relId], ['cocat_clslvl_relId' => NULL]]);
        if (isset($gsetObject->gs_spec_relId) && !empty($gsetObject->gs_spec_relId)) {
            $cocat = $cocat->andWhere(['OR', ['cocat_spec_relId' => $gsetObject->gs_spec_relId], ['cocat_spec_relId' => NULL]]);
        }
        return $cocat->count() > 0;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDetails()
    {
        return $this->hasMany(CocatDetail::class, ['cocatDet_cocat_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatAdmissionRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'cocat_admission_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'cocat_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cocat_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMinPassGradeRel()
    {
        return $this->hasOne(GradeScale::class, ['id' => 'min_pass_grade_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseCategoryRel()
    {
        return $this->hasOne(CourseCategory::class, ['id' => 'course_category_relId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'cocat_dept_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatCourseRel()
    {
        return $this->hasOne(Course::class, ['id' => 'cocat_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatProgramRel()
    {
        return $this->hasOne(Program::class, ['id' => 'cocat_program_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'cocat_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatSpecRel()
    {
        return $this->hasOne(Specialization::class, ['id' => 'cocat_spec_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCocatUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'cocat_updated_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferingDetails()
    {
        return $this->hasMany(CourseOffering2Detail::class, ['coff2det_cocat_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOfferings()
    {
        return $this->hasMany(CourseOffering::class, ['coff_cocat_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistrations()
    {
        return SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2', 'semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('course_offering2', 'course_offering2.id=semester_registration_2_detail.srd2_coff_relId')
            ->innerJoin('course_offering2_detail', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
            ->filterWhere([
                'coff2det_cocat_relId' => $this->id,
                'coff2det_course_relId' => $this->cocat_course_relId,
            ]);
        // return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_cocat_relId' => 'id']);
    }
}
