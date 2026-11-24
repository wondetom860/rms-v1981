<?php

namespace frontend\modules\DepartmentSelection\models;

use Yii;
use backend\models\User;
// use backend\models\Department;
use backend\models\Student;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\ClassLevel;
use common\Components\CommonControllerFunctions as CCF;

/**
 * This is the model class for table "selection_setting_entry".
 *
 * @property int $id
 * @property int $acadYear_relId
 * @property int $sem_relId
 * @property int $clslvl_relId
 * @property int $max_count_dept
 * @property int|null $status
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $created_at
 * @property int $created_by
 * @property string $data
 * @property string $depts_to_choose_from
 * @property int $linked
 *
 * @property DepartmentSelectEntry[] $departmentSelectEntries
 * @property SchoolYear $acadYearRel
 * @property User $approvedBy
 * @property ClassLevel $clslvlRel
 * @property User $createdBy
 * @property Semester $semRel
 * @property User $updatedBy
 */
class SelectionSettingEntry extends \yii\db\ActiveRecord
{

    const STATUS_APPROVED = 1;
    const STATUS_NOT_APPROVED = NULL;
    const STATUS_REJECTED = 0;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'selection_setting_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['acadYear_relId', 'sem_relId', 'clslvl_relId', 'max_count_dept', 'created_at', 'created_by', 'data'], 'required'],
            [['acadYear_relId', 'sem_relId', 'clslvl_relId', 'max_count_dept', 'status', 'approved_by', 'updated_by', 'created_by', 'linked'], 'integer'],
            [['approved_at', 'updated_at', 'created_at','date_from','date_to','depts_to_choose_from'], 'safe'],
            [['data'], 'string'],
            [['acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SchoolYear::class, 'targetAttribute' => ['acadYear_relId' => 'id']],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approved_by' => 'id']],
            [['clslvl_relId'], 'exist', 'skipOnError' => true, 'targetClass' => ClassLevel::class, 'targetAttribute' => ['clslvl_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Semester::class, 'targetAttribute' => ['sem_relId' => 'id']],
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
            'acadYear_relId' => Yii::t('rms', 'Academic Year'),
            'sem_relId' => Yii::t('rms', 'Semester'),
            'clslvl_relId' => Yii::t('rms', 'Class Year'),
            'max_count_dept' => Yii::t('rms', 'Dept. Choice Count'),
            'status' => Yii::t('rms', 'Status'),
            'depts_to_choose_from' => Yii::t('rms', 'Departments To Choose from'),
            'approved_at' => Yii::t('rms', 'Approved At'),
            'date_to' => Yii::t('rms', 'End Date'),
            'date_from' => Yii::t('rms', 'Start Date'),
            'approved_by' => Yii::t('rms', 'Approved By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'data' => Yii::t('rms', 'Data'),
            'linked' => Yii::t('rms', 'Linked'),
        ];
    }



    public function generateRegistrarRequestAlert(){

        return $this->find()->where(['status'=>$this::STATUS_NOT_APPROVED])->count();
    }

    public function getData(){
        $mainData = (object)[
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
            'acadYear_relId'=>intval($this->acadYear_relId),
            'sem_relId'=>intval($this->sem_relId),
            'clslvl_relId'=>intval($this->clslvl_relId),
            'max_count_dept'=>intval($this->max_count_dept),
            'status'=>$this->status,
            'depts_to_choose_from'=>$this->depts_to_choose_from,
            'date_to'=>$this->date_to,
            'date_from'=>$this->date_from,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function checkIfRequested(){
        $selects = $this->departmentSelectEntries;
        if ($selects && count($selects) > 0 && \Yii::$app->user->can('Student')) {
            $student = \backend\models\Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id]);
            foreach ($selects as $keySelection) {
                if ($keySelection->student_relId == $student->id) {
                    return $keySelection;
                }
            }
        }
    }

    public function isScheduleActive(){
        // check if the schedule is active for the selection of the department.

        $time_now = time();

        $start_date = strtotime($this->date_from);
        $end_date = strtotime($this->date_to);

        return $start_date <= $time_now && $end_date >= $time_now;

    }

    public function getActiveSettingEntry(){
        if (\Yii::$app->user->can('Student')) {
            $student = \backend\models\Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id]);

            if ($student) {
                $gset = $student->stdGroupReal;

                if ($gset) {
                    // check for similar active setting...
                    // return "Filtering Setting...";
                    return $this->find()
                        ->where([
                            'acadYear_relId' => $gset->gs_acadYear_relId,
                            'sem_relId' => $gset->gs_sem_relId,
                            'clslvl_relId' => $gset->gs_clslvl_relId,
                        ])
                        ->andWhere(['status' => $this::STATUS_APPROVED])
                        ->all();
                }else{
                    return "Group/Section Not Assigned";
                }
            }else{
                return "Student record entry missing";
            }
        }
        return "This task is ONLY allowed for Students!";
    }

    public function getSelectionDate(){
        $date_from = $this->date_from;
        $date_to = $this->date_to;

        if (!is_null($date_from) && !is_null($date_to)) {

            $date_from = date_format(date_create($this->date_from),"d/m/Y");
            $date_to = date_format(date_create($this->date_to),"d/m/Y");
            return $date_from.' - '.$date_to;
        }

        return "NOT SETTED";
    }

    public function getStatus(){
        $status = $this->status;

        return  $status == 1?"APPROVED" :
                ($status == 2?"REJECTED" :
                    "NOT APPROVED YET"
                );
    }

    public function getClassLevel($filter = NULL){
        $clslvl = $this->clslvlRel->clslvl_num;

        if ($filter) {
            return "Year - ".$clslvl;
        }
        return  $clslvl == 1?"1<sup>st</sup> Yr." :
                ($clslvl == 2?"2<sup>nd</sup> Yr." :
                    ($clslvl == 3?"3<sup>rd</sup> Yr." :
                        $clslvl."<sup>th</sup> Yr."
                    )
                );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDepartmentSelectEntries()
    {
        return $this->hasMany(DepartmentSelectEntry::class, ['setting_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAcadYearRel()
    {
        return $this->hasOne(SchoolYear::class, ['id' => 'acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getApprovedBy()
    {
        return $this->hasOne(User::class, ['id' => 'approved_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getClslvlRel()
    {
        return $this->hasOne(ClassLevel::class, ['id' => 'clslvl_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSemRel()
    {
        return $this->hasOne(Semester::class, ['id' => 'sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
