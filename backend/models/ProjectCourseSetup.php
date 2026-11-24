<?php

namespace backend\models;
use \common\components\CommonControllerFunctions as CCF;

use Yii;

/**
 * This is the model class for table "project_course_setup".
 *
 * @property integer $id
 * @property integer $pcs_acadYear_relId
 * @property integer $pcs_sem_relId
 * @property integer $pcs_gset_relId
 * @property integer $pcs_course_relId
 * @property integer $pcs_pgName_relId
 * @property integer $pcs_advisor
 * @property integer $pcs_group_leader
 * @property string $pcs_title
 * @property integer $pcs_approved
 * @property string $pcs_approved_at
 * @property integer $pcs_approved_by
 * @property string $pcs_created_at
 * @property integer $pcs_created_by
 * @property string $pcs_updated_at
 * @property integer $pcs_updated_by
 *
 * @property SchoolYear $pcsAcadYearRel
 * @property Semester $pcsSemRel
 * @property WGroupSetup $pcsGsetRel
 * @property Course $pcsCourseRel
 * @property ProjectGroupName $pcsPgNameRel
 * @property Student $pcsGroupLeader
 * @property User $pcsApprovedBy
 * @property User $pcsCreatedBy
 * @property User $pcsUpdatedBy
 */
class ProjectCourseSetup extends \yii\db\ActiveRecord
{
    public $instDept = NULL;

    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'project_course_setup';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pcs_gset_relId', 'pcs_course_relId', 'pcs_pgName_relId', 'pcs_created_at', 'pcs_created_by', 'pcs_updated_at', 'pcs_updated_by'], 'required'],
            [[ 'pcs_gset_relId', 'pcs_course_relId', 'pcs_pgName_relId', 'pcs_group_leader', 'pcs_approved', 'pcs_approved_by', 'pcs_created_by', 'pcs_updated_by'], 'integer'],
            [['pcs_title','pcs_advisor'], 'string'],
            [['pcs_approved_at', 'pcs_created_at', 'pcs_updated_at'], 'safe'],
            [['pcs_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::className(), 'targetAttribute' => ['pcs_acadYear_relId' => 'id']],
            [['pcs_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::className(), 'targetAttribute' => ['pcs_sem_relId' => 'id']],
            [['pcs_gset_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WGroupSetup::className(), 'targetAttribute' => ['pcs_gset_relId' => 'id']],
            [['pcs_course_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Course::className(), 'targetAttribute' => ['pcs_course_relId' => 'id']],
            [['pcs_pgName_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectGroupName::className(), 'targetAttribute' => ['pcs_pgName_relId' => 'id']],
            [['pcs_group_leader'], 'exist', 'skipOnError' => true, 'targetClass' => Student::className(), 'targetAttribute' => ['pcs_group_leader' => 'id']],
            [['pcs_approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pcs_approved_by' => 'id']],
            [['pcs_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pcs_created_by' => 'id']],
            [['pcs_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['pcs_updated_by' => 'id']],
        ];
    }

    public function getDeptProjectCourse(){
        if (is_null($this->instDept)) {
            if (Yii::$app->user->can('DepartmentHead')) {
                $this->instDept = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;

            }else{
                return NULL;
            }
        }

        $proCourse = CourseCatalog::find()
            ->where(['cocat_dept_relId'=>$this->instDept,'cocat_courseProject'=>1])
            ->all();
            $option = [];
        if ($proCourse && count($proCourse) > 0) {
           foreach ($proCourse as $key) {
            $course = Course::findOne(['id'=>$key->cocat_course_relId]);
               $option[] = ['id'=>$course->id,'course_title'=>$course->course_title];
           }
        }

        return $option;
    }


    public function getfreeProjectGroups($gset){
        if (is_null($gset)) {
            return NULL;
        }
        $assignedGroups = ProjectCourseSetup::find()->where(['pcs_gset_relId'=>intval($gset)])
        ->all();
        
        $assignedGroupsArray = [];
        if ($assignedGroups && count($assignedGroups) > 0) {
            foreach ($assignedGroups as $key) {
                $assignedGroupsArray[] = $key->pcs_pgName_relId;
            }
        }

        if (!empty($assignedGroupsArray)) {
            $freeGroups = ProjectGroupName::find()
                ->andWhere(['NOT IN','id',$assignedGroupsArray])
                ->all();
        }else{
            $freeGroups = ProjectGroupName::find()
                ->all();
        }
        

        $freeGroupsArrayv= "<option value=''> - </option>";
        if ($freeGroups && count($freeGroups) > 0) {
            foreach ($freeGroups as $key) {
                $freeGroupsArrayv .= "<option value='{$key->id}'>{$key->pg_name}</option>";
            }
        }
        return $freeGroupsArrayv;
    }


    // return list of group setup made for HoDs dept.
    public function getGsetArray($i){
        // Get current HoDs dept.
        if (is_null($this->instDept)) {
            if (Yii::$app->user->can('DepartmentHead')) {
                $this->instDept = Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
            }else{
                return [];
            }
        }
        // Then groups Assigned to his dept.

        $this->__constructor();
        $gset = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId'=>$this->acadYearId,
                'gs_sem_relId'=>$this->semId,
                'gs_dept_relId'=>$this->instDept])
            ->all();
            $gsetArray  = [];
        if ($gset && count($gset) > 0) {
            if ($i==0) {
                foreach ($gset as $key ) {
                    $gsetArray[] = $key->id;
                }
                return $gsetArray;
            }else if ($i == 1) {
                $option = [];
                // $option = "<option value=''> - </option>";
                foreach ($gset as $key ) {
                   $gname = \backend\models\WGroup::findOne(['id'=>$key->gs_group_relId])->group_name;
                   $adm = \backend\models\Admission::findOne(['id'=>$key->gs_adm_relId])->admission_name;
                   $prog = \backend\models\Program::findOne(['id'=>$key->gs_prog_relId])->program_name;

                   $clslvl = \backend\models\ClassLevel::findOne(['id'=>$key->gs_clslvl_relId])->clslvl_num;
                   $spec = isset($key->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$key->gs_spec_relId])->spec_name:'-';

                   $dept = \backend\models\Department::findOne(['id'=>$key->gs_dept_relId])->dept_name;

                   $title = $adm . '_'.$prog.'_'.$dept.'_'.$clslvl.'_'.$spec;

                   $option[] = ['id'=>$key->id,'gname'=>$gname.'_'.$title];
                   // $option .= "<option value='{$key->id}' title='{$title}'>{$gname}</option>";
                }
                // var_dump($option);die();
                return $option;
            }
        }else{
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'pcs_acadYear_relId' => Yii::t('rms', 'Acad. Year'),
            'pcs_sem_relId' => Yii::t('rms', 'Semester'),
            'pcs_gset_relId' => Yii::t('rms', 'Group'),
            'pcs_course_relId' => Yii::t('rms', 'Course'),
            'pcs_pgName_relId' => Yii::t('rms', 'Proj. group Name'),
            'pcs_advisor' => Yii::t('rms', 'Advisor'),
            'pcs_group_leader' => Yii::t('rms', 'Group Leader'),
            'pcs_title' => Yii::t('rms', 'Project Title'),
            'pcs_approved' => Yii::t('rms', 'Approved'),
            'pcs_approved_at' => Yii::t('rms', 'Approved At'),
            'pcs_approved_by' => Yii::t('rms', 'Approved By'),
            'pcs_created_at' => Yii::t('rms', 'Created At'),
            'pcs_created_by' => Yii::t('rms', 'Created By'),
            'pcs_updated_at' => Yii::t('rms', 'Updated At'),
            'pcs_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsAcadYearRel()
    {
        return $this->hasOne(SchoolYear::className(), ['id' => 'pcs_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsSemRel()
    {
        return $this->hasOne(Semester::className(), ['id' => 'pcs_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsGsetRel()
    {
        return $this->hasOne(WGroupSetup::className(), ['id' => 'pcs_gset_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsCourseRel()
    {
        return $this->hasOne(Course::className(), ['id' => 'pcs_course_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsPgNameRel()
    {
        return $this->hasOne(ProjectGroupName::className(), ['id' => 'pcs_pgName_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsGroupLeader()
    {
        return $this->hasOne(Student::className(), ['id' => 'pcs_group_leader']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsApprovedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pcs_approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pcs_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcsUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'pcs_updated_by']);
    }
}
