<?php

namespace backend\models;

use \common\components\CommonControllerFunctions as CCF;
use yii\base\ErrorException;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;

use Yii;

/**
 * This is the model class for table "course_offering2".
 *
 * @property integer $id
 * @property integer $coff2_acadYear_relId
 * @property integer $coff2_sem_relId
 * @property integer $coff2_group_relId
 * @property integer $coff2_acProg_relId
 * @property integer $coff2_lock_status
 *
 * @property SchoolYear $coff2AcadYearRel
 * @property WGroupSetup $coff2GroupRel
 * @property Semester $coff2SemRel
 * @property CourseOffering2Detail[] $courseOffering2Details
 */
class CourseOffering2 extends \yii\db\ActiveRecord
{

    public $admission = NULL;
    public $program = NULL;
    public $department = NULL;
    public $clslvl = NULL;
    public $spec = NULL;
    public $extra_course = NULL;

    public $HoDDept;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_offering2';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['coff2_group_relId', 'admission', 'department', 'program', 'clslvl', 'extra_course'], 'required'],
            [['coff2_acadYear_relId', 'coff2_sem_relId', 'coff2_group_relId', 'coff2_acProg_relId', 'coff2_lock_status', 'coff2_created_at', 'coff2_created_by', 'coff2_updated_at', 'coff2_updated_by'], 'integer'],
            [['coff2_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['coff2_acadYear_relId' => 'id']],
            [['coff2_acProg_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::class, 'targetAttribute' => ['coff2_acProg_relId' => 'id']],
            [['coff2_group_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::class, 'targetAttribute' => ['coff2_group_relId' => 'id']],
            [['coff2_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['coff2_sem_relId' => 'id']],
            [['coff2_cocatIds', 'data'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'coff2_lock_status' => Yii::t('rms', 'Offering lock status'),
            'coff2_acadYear_relId' => Yii::t('rms', 'Acad. Yr.'),
            'coff2_sem_relId' => Yii::t('rms', 'Sem.'),
            'coff2_group_relId' => Yii::t('rms', 'Group'),
            'clslvl' => Yii::t('rms', 'Class Lvl'),
            'coff2_cocatIds' => Yii::t('rms', 'Offered Courses'),
        ];
    }

    public function getCourseOfferingHeader()
    {
        return $this->getOfferingAcadeYearInfo() . ' - ' . $this->coff2GroupRel->getGsDisp(null);
    }

    public function getOfferingAcadeYearInfo()
    {
        return $this->coff2AcadYearRel->sch_year_desc . '-' . $this->coff2SemRel->semester_name;
    }

    public function copyCocatIdsFromTemp($acp)
    { //acp = active academi program
        $modelTemp  = new CourseOfferingTemp();
        $cocats = $modelTemp->getOfferedCourses($acp->academicYearRel->schoolYearRel->id, $acp->academicYearRel->semesterRel->id, $acp->admission_relId, $acp->program_relId, $acp->dept_relId, $acp->class_level_relId, $acp->spec_relId);
        $keyCooffTemp = $modelTemp->findOne($cocats[1]);

        if ($keyCooffTemp && !is_null($keyCooffTemp->cot_cocat_IDS)) {
            $this->coff2_cocatIds = $keyCooffTemp->cot_cocat_IDS;
            $this->coff2_lock_status = $keyCooffTemp->cot_locked;
            $this->update(false);
        }
    }

    // update coffTemp entry from coff2.

    public function updateCoofTempFromCoof2(){
        $acProg = $this->coff2AcProgRel;
        $coffTem = $acProg->coffTemp;
    }

    //update coff2Entry from coff2detail.
    private function checkOfferingDetail()
    {
        $cocatIds = json_decode($this->coff2_cocatIds, true);
        foreach ($this->courseOffering2Details as $detail) {
            if (!in_array($detail->coff2det_cocat_relId, $cocatIds)) {
                $cocatIds[] = $detail->coff2det_cocat_relId;
            }
        }

        $this->coff2_cocatIds = json_encode($cocatIds);
        $this->getData();
        $this->save(false);
        return;
    }

    public function distributeCoff2()
    {
        $cocatIds = json_decode($this->coff2_cocatIds, true);
        // check is offering detail info is deffrent from json_data
        $this->checkOfferingDetail();
        if ($cocatIds && count($cocatIds) > 0) {
            foreach ($cocatIds as $cocatId) {
                // check if that offering detail already contains cocatId
                $coff2det = CourseOffering2Detail::findOne(['coff2det_coff2_relId' => $this->id, 'coff2det_cocat_relId' => $cocatId]);
                if ($coff2det && count([$coff2det]) > 0) { //if entry already exists
                    if ($coff2det->coff2det_registrarApproved != $this->coff2_lock_status) {
                        $coff2det->coff2det_registrarApproved = $this->coff2_lock_status;
                        $coff2det->coff2det_registrarApproved_at = date('Y-m-d');
                        $coff2det->coff2det_registrarApproved_by = $this->coff2_updated_by;
                        if ($coff2det->update(false)) {
                            continue;
                        } else {
                            return json_encode($coff2det->getErrors());
                        }
                    }
                    continue;
                } else { //if course is offered after entry has been created
                    // die("Creating {$cocatId} from {$this->id}");
                    $cocat = CourseCatalog::findOne($cocatId);
                    $modelDet = new CourseOffering2Detail();

                    $modelDet->coff2det_registrarApproved = $this->coff2_lock_status;
                    $modelDet->coff2det_acProg_relId = $this->coff2_acProg_relId;
                    $modelDet->coff2det_coff2_relId = $this->id;
                    $modelDet->coff2det_cocat_relId = $cocatId;
                    $modelDet->coff2det_course_relId = $cocat->cocat_course_relId;
                    $modelDet->coff2det_created_by = $modelDet->coff2det_updated_by = Yii::$app->user->identity->id;
                    $modelDet->coff2det_created_at = $modelDet->coff2det_updated_at = date('Y-m-d h:i:s');

                    if ($modelDet->save()) {
                        continue;
                    } else {
                        throw new ErrorException("Error Processing Request _ WONDE_TOM", 1);
                    }
                }
            }
        }
    }

    public function distributeCoff($gsetObject)
    {
        // get matching coffTemp
        if (!is_null($gsetObject) && is_object($gsetObject)) {

            $modelTemp  = new CourseOfferingTemp();
            if ($modelTemp->checkIfOffered($gsetObject->gs_acadYear_relId, $gsetObject->gs_sem_relId, $gsetObject->gs_adm_relId, $gsetObject->gs_prog_relId, $gsetObject->gs_dept_relId, $gsetObject->gs_clslvl_relId, $gsetObject->gs_spec_relId)) {

                $cocats = $modelTemp->getOfferedCourses($gsetObject->gs_acadYear_relId, $gsetObject->gs_sem_relId, $gsetObject->gs_adm_relId, $gsetObject->gs_prog_relId, $gsetObject->gs_dept_relId, $gsetObject->gs_clslvl_relId, $gsetObject->gs_spec_relId);

                if ($cocats[2] == 0) {
                    Yii::$app->db->createCommand()->update('course_offering_temp', ['cot_locked' => 1], ['id' => $cocats[1]])
                        ->execute();
                }

                $keyCooffTemp = $modelTemp->findOne($cocats[1]);
                if ($keyCooffTemp->cot_acProg_relId != $gsetObject->gs_active_prog_relId) {
                    $keyCooffTemp->cot_acProg_relId = $gsetObject->gs_active_prog_relId;
                    $keyCooffTemp->update();
                }
                // check if course is offered for the group
                $exists = $this->findOne(['coff2_group_relId' => $gsetObject->id]);
                if ($exists && count([$exists]) > 0) {
                    if ($gsetObject->gs_active_prog_relId != $exists->coff2_acProg_relId) {
                        $exists->coff2_acProg_relId == $gsetObject->gs_active_prog_relId;
                        $exists->coff2_cocatIds = $exists->coff2_cocatIds ? $exists->coff2_cocatIds : $keyCooffTemp->cot_cocat_IDS;
                        $exists->coff2_updated_by = \Yii::$app->user->id;
                        $exists->coff2_updated_at = time();
                        $exists->coff2_lock_status = $keyCooffTemp->cot_registrarApproved;

                        $exists->getdata();
                        $exists->update(false);
                    }
                    $this->distributeCoffDetail($cocats[0], $exists, $cocats, $modelTemp);
                } else {
                    // create Entry.
                    $model = new CourseOffering2();
                    $model->coff2_acadYear_relId = $gsetObject->gs_acadYear_relId;
                    $model->coff2_sem_relId = $gsetObject->gs_sem_relId;
                    $model->coff2_group_relId = $gsetObject->id;

                    $model->coff2_acProg_relId = $keyCooffTemp->cot_acProg_relId;
                    $model->coff2_cocatIds = $keyCooffTemp->cot_cocat_IDS;
                    $model->coff2_lock_status = $keyCooffTemp->cot_registrarApproved;

                    $model->coff2_created_by = \Yii::$app->user->id;
                    $model->coff2_created_at = time();

                    $model->getData();

                    // attributes NOT related with database.
                    $model->admission = 0;
                    $model->program = 0;
                    $model->department = 0;
                    $model->clslvl = 0;
                    $model->extra_course = 0;

                    if ($model->save()) {
                        $this->distributeCoffDetail($cocats[0], $model, $cocats, $modelTemp);
                    } else {
                        die("Entry Creation Error. Please retry!");
                    }
                }
            } else {
                die("Course NOT offered yet.");
            }
        } else {
            die("Invalid Group Setup");
        }
    }

    private function distributeCoffDetail($cocatIDS, $modelCoff, $cocats, $modelTemp)
    {

        foreach ($cocatIDS as $key) {
            $cocat = CourseCatalog::findOne($key);
            if (!($cocat && count([$cocat]) > 0)) {
                continue;
                // catalog Error, catalog missing for {$coutse->course_title}
                // throw new ErrorException("Catalog missing for {CATALOG_ENTRY_ID : {$key}}, Any change made on catalog? Please check it and offer the courses again", 1);
            }
            $course = $cocat->cocatCourseRel;
            $coff2det = CourseOffering2Detail::findOne(['coff2det_coff2_relId' => $modelCoff->id, 'coff2det_cocat_relId' => $key]);
            if ($coff2det && count([$coff2det]) > 0) {
                continue;
            } else {
                $keyCooffTemp = $modelTemp->findOne($cocats[1]);
                $modelDet = new CourseOffering2Detail();
                $modelDet->coff2det_registrarApproved = $keyCooffTemp->cot_registrarApproved;
                $modelDet->coff2det_acProg_relId = $keyCooffTemp->cot_acProg_relId;
                $modelDet->coff2det_coff2_relId = $modelCoff->id;
                $modelDet->coff2det_cocat_relId = $key;
                $modelDet->coff2det_course_relId = $course->id;
                $modelDet->coff2det_created_by = $modelDet->coff2det_updated_by = Yii::$app->user->identity->id;
                $modelDet->coff2det_created_at = $modelDet->coff2det_updated_at = date('Y-m-d h:i:s');

                if ($modelDet->save()) {
                    $modelDet->updateFaclutyAssignmentData();
                    continue;
                } else {
                    throw new ErrorException("Error Processing Request _ WONDE_TOM", 1);
                }
            }
        }
    }

    public static function updateAcEntry()
    {
        $unUpdated = self::find()->where(['coff2_acProg_relId' => NULL])->all();

        if ($unUpdated && count($unUpdated) > 0) {
            foreach ($unUpdated as $coff2) {
                if ($coff2->coff2_group_relId && !is_null($coff2->coff2GroupRel->gs_active_prog_relId)) {
                    $coff2->coff2_acProg_relId = $coff2->coff2GroupRel->gs_active_prog_relId;
                    // $coff2->coff2_cocatIds = CourseOfferingTemp::findOne(['cot_gset_relId' => $coff2->coff2_group_relId])->cot_cocat_IDS;
                    if ($coff2->update(false)) {
                        \Yii::$app->db->createCommand()
                            ->update(
                                'course_offering2_detail',
                                ['coff2det_acProg_relId' => $coff2->coff2_acProg_relId],
                                ['coff2det_coff2_relId' => $coff2->id]
                            )
                            ->execute();
                        // load temporary course offering temp files: 
                        $coff2->copyCocatIdsFromTemp($coff2->coff2detAcProgRel);
                    } else {
                        var_dump("Error: ", $coff2->getErrors());
                        die();
                    }
                }
            }
        }
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'coff2_lock_status' => $this->coff2_lock_status,
            'coff2_acadYear_relId' => intval($this->coff2_acadYear_relId),
            'coff2_sem_relId' => intval($this->coff2_sem_relId),
            'coff2_cocatIds' => intval($this->coff2_cocatIds),
            'coff2_created_by' => intval($this->coff2_created_by),
            'coff2_updated_by' => intval($this->coff2_updated_by),
        ]));
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2detAcProgRel()
    {
        return $this->hasOne(AcActivePrograms::class, ['id' => 'coff2_acProg_relId']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2AcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'coff2_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2GroupRel()
    {
        return $this->hasOne(WGroupSetup::class, ['id' => 'coff2_group_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2SemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'coff2_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCoff2CoffDet()
    {
        return $this->hasOne(CourseOffering2Detail::class, ['coff2det_coff2_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCourseOffering2Details()
    {
        return $this->hasMany(CourseOffering2Detail::class, ['coff2det_coff2_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMaxGrade($courseId)
    {
        return SemesterRegistration2Detail::find()
            ->innerJoin('course_offering2', 'semester_registration_2_detail.srd2_coff_relId=course_offering2.id')
            ->innerJoin('grade_scale', 'grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
            ->where([
                'srd2_coff_relId' => $this->id,
                'srd2_course_relId' => $courseId,
            ])
            ->orderBy([
                'gs_gradePoint' => SORT_DESC
            ])
            ->all();
        // if (!is_null($courseId)) {
        //     return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_coff_relId' => 'id'])
        //         ->andFilterWhere(['srd2_course_relId' => $courseId]);
        // }
        // return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_coff_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemesterRegistration2Details($courseId = null)
    {
        if (!is_null($courseId)) {
            return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_coff_relId' => 'id'])
                ->andFilterWhere(['srd2_course_relId' => $courseId]);
        }
        return $this->hasMany(SemesterRegistration2Detail::class, ['srd2_coff_relId' => 'id']);
    }
}
