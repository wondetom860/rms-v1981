<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\Department;
use backend\models\User;

/**
 * This is the model class for table "ase_department_evaluation_schedule".
 *
 * @property int $id
 * @property int $header_relId
 * @property int $dept_relId
 * @property int $criteria_bundle_relId
 * @property int $start_date
 * @property int $end_date
 * @property int $finalized
 * @property int|null $status
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property AseCriteriaBundle $criteriaBundleRel
 * @property Department $deptRel
 * @property User $createdBy
 * @property User $updatedBy
 * @property AseSemesterEvaluation $headerRel
 * @property AseEvaluationControlSetting[] $aseEvaluationControlSettings
 * @property AseEvaluationResult[] $aseEvaluationResults
 */
class AseDepartmentEvaluationSchedule extends \yii\db\ActiveRecord
{

    public $startdate,$enddate;
    
    public const STATUS_ACTIVE = 1;
    public const STATUS_CLOSED = 0;
    public const STATUS_UPNEXT = -1;

    public $statuses = [0 => 'Closed',1 => 'Active',-1 => 'Up Next'];
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_department_evaluation_schedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['header_relId', 'dept_relId', 'criteria_bundle_relId', 'start_date', 'end_date', 'created_by', 'created_at', 'data'], 'required'],
            [['header_relId', 'dept_relId', 'criteria_bundle_relId', 'start_date', 'end_date', 'finalized', 'status', 'created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['data','startdate','enddate'], 'string'],
            [['criteria_bundle_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseCriteriaBundle::class, 'targetAttribute' => ['criteria_bundle_relId' => 'id']],
            [['dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['dept_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['updated_by' => 'id']],
            [['header_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseSemesterEvaluation::class, 'targetAttribute' => ['header_relId' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'header_relId' => Yii::t('rms', 'Evaluation Header'),
            'dept_relId' => Yii::t('rms', 'Department'),
            'criteria_bundle_relId' => Yii::t('rms', 'Evaluation Bundle'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'finalized' => Yii::t('rms', 'Finalized'),
            'status' => Yii::t('rms', 'Schedule Status'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function extendSchedule(){
        $this->start_date = $this->headerRel->start_date;
        $this->end_date = $this->headerRel->end_date;
        if (!$this->validDateInterval()) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Please check schedule dates'.json_encode($this));
            return false;
        }
        $this->getData();
        if ($this->save()) {
            (new AseEvaluationControlSetting())->generateControlEntries($this->id,$this);
            Yii::$app->session->setFlash('type','success');
            Yii::$app->session->setFlash('message','Schedule extended successfully');
            return true;
        }
        // 
        Yii::$app->session->setFlash('type','error');
        Yii::$app->session->setFlash('message','Schedule extension failed, ERROR: '.json_encode($this->getErrors()));
        return false;
    }

    public function validDateInterval(){
        // with regard to header confgiuration
        // $time = time();
        $header = $this->headerRel;
        return ($this->start_date >= $header->start_date && $this->end_date <= $header->end_date) && ($this->start_date < $this->end_date);
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'start_date'=>$this->start_date,
            'end_date'=>$this->end_date,
            'finalized'=>$this->finalized,
            'header_relId'=>intval($this->header_relId),
            'dept_relId'=>intval($this->dept_relId),
            'criteria_bundle_relId'=>intval($this->criteria_bundle_relId),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function getStatus(){
        $st = "";
        $s = 1;

        $acadYearStatus = $this->headerRel->getStatus(1);

        if (strtoupper($acadYearStatus) != 'ACTIVE') {
            //if academic year is not active, the program scdule must also be not active.
            $s = array_keys($this->statuses,$acadYearStatus)[0];
            $st = $acadYearStatus;
        }else{
            if(time() > $this->end_date){
                $st = "Closed";
                $s = 0;
            }else if(time() < $this->start_date){
                $st = "Up Next";
                $s = -1;
            }else{
                $s = 1;
                $st = "Active";
            }
        }

        if ($this->status != $s) {
            $this->status = $s;
            $this->update(false); 
        }

        // return $st;
        return $this->status == 1 ? "<span class='bg-success'>{$st}</span>" : "<span class='bg-warning'>{$st}</span>";
    }

    public function getScheduleInterval(){
        return date("d/m/Y",$this->start_date)."-".date("d/m/Y",$this->end_date);
    }

    public function getEvaluationheader(){
        return $this->headerRel->academicYearRel->getAcHeader();
    }

    public function getHoD(){
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
        $this->dept_relId = $inst->inst_dep_relId;
        return;
    }

    public function checkIfExists($id = null){
        if ($id) {
            return self::find()->where([
                'header_relId' => $this->header_relId,
                'dept_relId' => $this->dept_relId,
                'criteria_bundle_relId' => $this->criteria_bundle_relId,
            ])->andWhere(" id <> $id ")->one();
        }
        return self::findOne([
            'header_relId' => $this->header_relId,
            'dept_relId' => $this->dept_relId,
            'criteria_bundle_relId' => $this->criteria_bundle_relId,
        ]);
    }

    /**
     * Gets query for [[CriteriaBundleRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCriteriaBundleRel()
    {
        return $this->hasOne(AseCriteriaBundle::class, ['id' => 'criteria_bundle_relId']);
    }

    /**
     * Gets query for [[DeptRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'dept_relId']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'created_by']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }

    /**
     * Gets query for [[HeaderRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHeaderRel()
    {
        return $this->hasOne(AseSemesterEvaluation::class, ['id' => 'header_relId']);
    }

    /**
     * Gets query for [[AseEvaluationControlSettings]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationControlSettings()
    {
        return $this->hasMany(AseEvaluationControlSetting::class, ['dept_plan_relId' => 'id']);
    }

    /**
     * Gets query for [[AseEvaluationResults]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseEvaluationResults()
    {
        return $this->hasMany(AseEvaluationResult::class, ['dept_plan_relId' => 'id']);
    }
}
