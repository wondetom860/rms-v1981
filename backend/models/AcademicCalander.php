<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "academic_calander".
 *
 * @property integer $id
 * @property integer $acad_action_relId
 * @property integer $acad_acadYear_relId
 * @property integer $acad_adm_relId
 * @property integer $acad_prog_relId
 * @property integer $acad_clslvl_relId
 * @property integer $acad_sem_relId
 * @property string $acad_starttime
 * @property string $acad_finishtime
 * @property integer $acad_status
 * @property integer $acad_created_by
 * @property string $acad_created_at
 * @property string $acad_updated_at
 * @property integer $acad_updated_by
 *
 * @property SchoolYear $acadAcadYearRel
 * @property AcademicCalanderAction $acadActionRel
 * @property Admission $acadAdmRel
 * @property ClassLevel $acadClslvlRel
 * @property User $acadCreatedBy
 * @property Program $acadProgRel
 * @property Semester $acadSemRel
 * @property User $acadUpdatedBy
 */
class AcademicCalander extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'academic_calander';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acad_action_relId', 'acad_acadYear_relId','acad_starttime', 'acad_finishtime', 'acad_created_by', 'acad_created_at', 'acad_updated_at', 'acad_updated_by'], 'required'],
            [['acad_action_relId', 'acad_acadYear_relId', 'acad_adm_relId', 'acad_prog_relId', 'acad_clslvl_relId', 'acad_sem_relId', 'acad_status', 'acad_created_by', 'acad_updated_by'], 'integer'],
            [['acad_starttime', 'acad_finishtime', 'acad_created_at', 'acad_updated_at'], 'safe'],
            // [['acad_starttime'], 'date', 'min' => date("d-m-Y", strtotime('0 days'))],
            [['acad_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['acad_acadYear_relId' => 'id']],
            [['acad_action_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcademicCalanderAction::class, 'targetAttribute' => ['acad_action_relId' => 'id']],
            [['acad_adm_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Admission::class, 'targetAttribute' => ['acad_adm_relId' => 'id']],
            [['acad_clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['acad_clslvl_relId' => 'id']],
            [['acad_created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['acad_created_by' => 'id']],
            [['acad_prog_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Program::class, 'targetAttribute' => ['acad_prog_relId' => 'id']],
            [['acad_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['acad_sem_relId' => 'id']],
            [['acad_updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['acad_updated_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'acad_action_relId' => Yii::t('rms', 'Action / Activity'),
            'acad_acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'acad_adm_relId' => Yii::t('rms', 'Admission'),
            'acad_prog_relId' => Yii::t('rms', 'Program'),
            'acad_clslvl_relId' => Yii::t('rms', 'Class Year / Batch'),
            'acad_sem_relId' => Yii::t('rms', 'Semester'),
            'acad_starttime' => Yii::t('rms', 'Starttime'),
            'acad_finishtime' => Yii::t('rms', 'Finishtime'),
            'acad_status' => Yii::t('rms', 'Status'),
            'acad_created_by' => Yii::t('rms', 'Created By'),
            'acad_created_at' => Yii::t('rms', 'Created At'),
            'acad_updated_at' => Yii::t('rms', 'Updated At'),
            'acad_updated_by' => Yii::t('rms', 'Updated By'),
        ];
    }

    public function updateSemester(){
        $model = $this;
        if (!in_array($model->acadActionRel->aca_name, ['Semester I','Semester II','Summer'])) {
            $schedule = self::find()
                ->innerJoin('academic_calander_action','academic_calander_action.id=academic_calander.acad_action_relId')
                ->andWhere([
                    'aca_name'=>['Semester I','Semester II','Summer'],
                    'acad_sem_relId'=>$model->acad_sem_relId,
                    'acad_acadYear_relId'=>$model->acad_acadYear_relId,
                ])
                ->orderBy(['acad_finishtime' => SORT_DESC])
                ->one();
            if ($schedule  && count($schedule ) > 0) {
                if (strtotime($schedule->acad_finishtime) < strtotime($model->acad_finishtime)) {
                    $schedule->acad_finishtime = $model->acad_finishtime;

                    $model->acad_finishtime = strtotime($model->acad_finishtime) + (24*60*60)-1;
                    $model->acad_finishtime = date('Y-m-d h:i:s',$model->acad_finishtime);

                    // var_dump($model->acad_finishtime);die();

                    $schedule->update();
                }
            }
        }
    }

    public function getStartDate(){
        return date_format(date_create($this->acad_starttime),"d/m/Y @ h:i A");
    }

    public function getEndDate(){
        return date_format(date_create($this->acad_finishtime),"d/m/Y  @ h:i A");
    }

    public function getProgram(){
        $adm = isset($this->acad_adm_relId) ? $this->acadAdmRel->admission_name : 'All Admissions';
        $pgm = isset($this->acad_prog_relId) ? $this->acadProgRel->program_name : 'All Programs';
        return $adm.' - '.$pgm;
    }

    public function getClassLevl(){
        return $this->acad_clslvl_relId ? $this->acadClslvlRel->getClslvlDisp() : 'All Class Levels';
    }

    public function getStatus($statusOnly = null){
        // [
        //     -1=>CLOSED,
        //     1=>ACTIVE,
        //     2=>FUTURE,
        // ]
        $startDate = strtotime($this->acad_starttime);
        $endDate = strtotime($this->acad_finishtime);

        if (time() - $endDate > 0) {
            if ($this->acad_status != -1) {
                $this->acad_status = -1;
                $this->update();
            }
            return $statusOnly ? 'Closed' : ['warning','Closed'];
        }

        if (time() - $endDate < 0 && time() - $startDate > 0) {
            if ($this->acad_status != 1) {
                $this->acad_status = 1;
                $this->update();
            }
            return $statusOnly ? 'Active' :  ['success','Active'];
        }

        if (time() - $startDate < 0 ) {
            if ($this->acad_status != 2) {
                $this->acad_status = 2;
                $this->update();
            }
            return $statusOnly ? 'Not Started Yet' :  ['danger',"Up Next"];
        }
    }

    public function getHeaderInfo($programObj = NULL){//title for displaying on registration entry 
        $sem = $this->acadSemRel ? "-".$this->acadSemRel->semester_name : NULL;
        $prog = $this->acadProgRel ? $this->acadProgRel->program_name : ($programObj ? $programObj->program_name : NULL);
        $adm = $this->acadAdmRel ? " for ".$this->acadAdmRel->admission_name : NULL;
        return $this->acadActionRel->aca_name .'_'.$this->acadAcadYearRel->sch_year_desc.$sem.$adm.' '.$prog.' Students.';
    }

    public static function getSchedule($startDate,$endDate){
        return self::find()
            ->where(['>=', 'acad_starttime', $startDate])
            ->orWhere(['<=', 'acad_finishtime', $endDate])
            ->orderBy([
                'acad_status' => SORT_DESC,
                'acad_finishtime' => SORT_DESC
            ])
            ->limit(15)
            ->all();
    }



    public function checkIfOpen2($actionName,$gset){
        // check if schedule is open for the action name
        if (is_null($gset)) {
            return NULL;
        }
        $modelAps = new \frontend\modules\AcademicCalendar\models\AcProgramSchedule();
        // $modelAps->checkIfScheduleActive($actionId,$programId);
        return $this->find()
            ->innerJoin('academic_calander_action','academic_calander_action.id = academic_calander.acad_action_relId')
            ->where([
                'academic_calander_action.aca_name'=>$actionName,
            ])
            ->andWhere(['<=', 'acad_starttime', date('Y-m-d h:i:s')])
            ->andWhere(['>=', 'acad_finishtime', date('Y-m-d h:i:s')])
            ->all();
    }


    public function checkIfOpen($actionName,$academicYearId = NULL){
        // check if schedule is open for the action name

        return $this->find()
            ->innerJoin('academic_calander_action','academic_calander_action.id = academic_calander.acad_action_relId')
            ->where([
                'academic_calander_action.aca_name'=>$actionName,
                'acad_status' => 1
            ])
            // ->andWhere(['>=', 'acad_starttime', date('Y-m-d')])
            // ->andWhere(['<=', 'acad_finishtime', date('Y-m-d')])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'acad_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadActionRel()
    {
        return $this->hasOne(AcademicCalanderAction::class, ['id' => 'acad_action_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadAdmRel()
    {
        return $this->hasOne(Admission::class, ['id' => 'acad_adm_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'acad_clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'acad_created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadProgRel()
    {
        return $this->hasOne(Program::class, ['id' => 'acad_prog_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'acad_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'acad_updated_by']);
    }
}
