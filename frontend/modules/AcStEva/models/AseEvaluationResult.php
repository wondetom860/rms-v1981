<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\User;

/**
 * This is the model class for table "ase_evaluation_result".
 *
 * @property int $id
 * @property int $control_setting_relId
 * @property int $criteria_bundle_relId
 * @property string $completed_staffs
 * @property int $dept_plan_relId
 * @property float $average_total
 * @property float $average_percentage
 * @property int $status
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 * @property string $point_value_entry
 * @property string $eval_point_rate 
 *
 * @property AseEvaluationControlSetting $controlSettingRel
 * @property AseCriteriaBundle $criteriaBundleRel
 * @property AseDepartmentEvaluationSchedule $deptPlanRel
 * @property User $createdBy
 * @property User $updatedBy
 */
class AseEvaluationResult extends \yii\db\ActiveRecord
{
    // public $eval_point_rate;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_evaluation_result';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['control_setting_relId', 'criteria_bundle_relId', 'completed_staffs', 'dept_plan_relId', 'average_total', 'average_percentage', 'created_at', 'created_by', 'data'], 'required'],
            [['control_setting_relId', 'criteria_bundle_relId', 'dept_plan_relId', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['completed_staffs'], 'safe'],
            [['average_total', 'average_percentage'], 'number'],
            [['data', 'eval_point_rate'], 'string'],
            [['control_setting_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseEvaluationControlSetting::class, 'targetAttribute' => ['control_setting_relId' => 'id']],
            [['criteria_bundle_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseCriteriaBundle::class, 'targetAttribute' => ['criteria_bundle_relId' => 'id']],
            [['dept_plan_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AseDepartmentEvaluationSchedule::class, 'targetAttribute' => ['dept_plan_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
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
            'control_setting_relId' => Yii::t('rms', 'Evaluation Entry'),
            'criteria_bundle_relId' => Yii::t('rms', 'Criteria Bundle'),
            'completed_staffs' => Yii::t('rms', 'Completed Staffs'),
            'dept_plan_relId' => Yii::t('rms', 'Schedule'),
            'average_total' => Yii::t('rms', 'Average Total'),
            'average_percentage' => Yii::t('rms', 'Average Percentage'),
            'status' => Yii::t('rms', 'Status'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function checkIfExists()
    {
        return self::find()
            ->where([
                'control_setting_relId' => $this->control_setting_relId,
                'criteria_bundle_relId' => $this->criteria_bundle_relId,
                'dept_plan_relId' => $this->dept_plan_relId,
            ])
            ->one();
    }

    public function loadPrevResult(){
        $all = self::find()->all();
        foreach ($all as $evalresult) {
            if(str_starts_with($evalresult->eval_point_rate,'{') && str_ends_with($evalresult->eval_point_rate,'}')){
                if($evalresult->result_distributed != 1){
                    $ratings = json_decode($evalresult->eval_point_rate, true);
                    if($ratings){
                        $evalresult->placeRatings($ratings);
                    }
                    $evalresult->result_distributed = 1;
                    $evalresult->save(false);
                }
            }
        }
    }

    public function getData()
    {
        $this->data = md5(serialize((object)[
            'control_setting_relId' => $this->control_setting_relId,
            'criteria_bundle_relId' => $this->criteria_bundle_relId,
            'completed_staffs' => $this->completed_staffs,
            'dept_plan_relId' => $this->dept_plan_relId,
            'average_total' => $this->average_total,
            'average_percentage' => $this->average_percentage,
            'created_by' => intval($this->created_by),
            'updated_by' => intval($this->updated_by),
        ]));
    }

    public function getApplicableBundlesCount()
    {
        $bundle = $this->criteriaBundleRel;
        $evalCriteriaIds = json_decode($bundle->criteria_IDS);
        $inst = \backend\models\Instructor::findOne(['inst_user_relId' => $this->controlSettingRel->candidate_staff_relId]);
        if ($inst == null) {
            return 0;
        }
        $criteria = \frontend\modules\AcStEva\models\AseCriteriaDescription::find()
            ->where(condition: [
                'id' => $evalCriteriaIds
            ])
            ->indexBy('category_relId')
            ->all();
        if ($criteria) {
            $count = 0;
            foreach ($criteria as $categoryid => $criteriaDescription) {
                $category = \frontend\modules\AcStEva\models\AseCriteriaCategory::findOne($categoryid);
                if (in_array($inst->instUserRel->user_type, json_decode($category->profess_list))) {
                    foreach ($category->aseCriteriaDescriptions as $point) {
                        if (!in_array($point->id, $evalCriteriaIds)) {
                            continue;
                        }
                        $count++;
                    }
                }
            }
            return $count;
        }
    }

    public function getEvalPointRate()
    {
        return json_decode($this->eval_point_rate);
    }

    public function placeratings($eval_point_rate){
        // ratings = [criteriaId, rating_out_of_5]
        foreach ($eval_point_rate as $criteria => $rating) {
            if($rating == 0){
                continue; // skip zero ratings
            }
            $exists = AseEvaluationResultDetail::find()
            ->where([
                'evaluation_result_relId' => $this->id,
                'criteria_relId' => $criteria,
                'evaluee_relId' => $this->controlSettingRel->candidate_staff_relId,
            ])
            ->one();
            if($exists){
                $_ratings = json_decode($exists->ratings, true);
                $_ratings = $_ratings ? array_merge($_ratings, [$rating]) : [$rating];
                $exists->ratings = json_encode($_ratings);

                $exists->average = array_sum($_ratings) / count($_ratings);
                $exists->updated_by = Yii::$app->user->id;
                $exists->updated_at = time();
                $exists->getdata();
                if(!$exists->save()){
                    Yii::$app->session->setFlash('type', 'error');
                    Yii::$app->session->setFlash('message', 'Error while saving ratings for criteria: '.$criteria);
                    // var_dump($exists->getErrors());
                    // exit;
                }
            }else{
                $model = new AseEvaluationResultDetail();
                $model->evaluation_result_relId = $this->id;
                $model->evaluee_relId = $this->controlSettingRel->candidate_staff_relId;
                $model->criteria_relId = $criteria;
                $model->ratings = json_encode([$rating]);
                $model->average = $rating;
                $model->created_by = Yii::$app->user->id;
                $model->created_at = time();
                $model->getdata();
                if(!$model->save()){
                    Yii::$app->session->setFlash('type', 'error');
                    Yii::$app->session->setFlash('message', 'Error while saving ratings for criteria: '.$criteria);
                    // var_dump($model->getErrors());
                    // exit;
                }
            }   
        }
    }

    public function computeAverage()
    {
        $total = 0;
        $na_evals = 0;
        $evals = json_decode($this->eval_point_rate,true);
        if (!$evals || count($evals) == 0) {
            // $this->average_total = 0;
            return;
        }
        foreach ($evals as $key => $value) {
            if($value == 0){ $na_evals++; continue; }
            $total += $value;
        }
        $average_total = $total == 0 ? $total : ($total / (count($evals) - $na_evals));
        $this->average_total += $average_total;
        // $this->average_total /= count(json_decode($this->completed_staffs,true));

        //  
        // $this->average_percentage = $this->average_total/count(json_decode($this->completed_staffs));
    }

    public function computeAverageLast()
    {
        $total = 0;
        $na_evals = 0;
        // $criteria_bundles_count = count(json_decode($this->criteriaBundleRel->criteria_IDS));
        if($this->eval_point_rate == 0){
            return;
        }
        $evals = json_decode($this->eval_point_rate,true);
        if (!$evals) {//|| count($evals) == 0
            $evals = explode(',',$this->eval_point_rate);
            if(!$evals || count($evals) == 0){
                // $this->average_total = 0;
                return;
            }
        }
        // $evaluees_count = count(explode(',', $this->completed_staffs));
        // var_dump(count($evals),$evaluees_count);exit;
        foreach ($evals as $key => $value) {
            $value = floatval($value);
            if($value == 0){ $na_evals++; continue; }
            $total += $value;
        }
        $average_total = $total == 0 ? $total : ($total / (count($evals) - $na_evals));
        // $average_total = ($total / count($evals));
        $this->average_total = ($average_total);///$evaluees_count
    }

    public function computePercentage()
    {
        if($this->average_total == 0 && $this->eval_point_rate){
            $this->computeAverage();
            // $this->refresh();
        }
        $this->average_percentage = $this->average_total * ($this->criteriaBundleRel->average_percentage / 100);
    }

    public function markEvaluated($status=1){
        if($this->controlSettingRel->course_relId){
            $coff2det = $this->controlSettingRel->facultyCourseAssignmentRel->coff2detRel;
            $coff2 = $coff2det->coff2detCoff2Rel;
            $srd2s = $coff2->getSemesterRegistration2Details($this->controlSettingRel->course_relId)->all();
            if($srd2s){
                $srd2 = array_filter($srd2s, function($srd2) {
                    return $srd2->srd2SemregRel->semregStdRel->std_user_relId == Yii::$app->user->identity->id;
                });
                // var_dump($srd2);exit;
                $srd2 = $srd2[0] ?? null;
                $both_evaluated = $this->evaluatedBothInstAndTa($coff2det,$srd2);
                if($srd2 && ($both_evaluated == true)){
                    // check if both instructor and TA are evaluated by student,
                    $srd2->srd2_evaluationCompleted = $status;
                    $srd2->save(false);
                }
            }
        }
    }

    private function evaluatedBothInstAndTa($coff2det,$srd2){
        if($coff2det && $srd2){
            $instEvaluated = $coff2det->coff2detInstRel ? $this->checkIfEvaluated(NULL,$coff2det->coff2det_course_relId,$coff2det->coff2detInstRel->inst_user_relId,$srd2->srd2SemregRel->semregStdRel->std_user_relId) : TRUE;
            $tAEvaluated = $coff2det->coff2detTARel ? $this->checkIfEvaluated(NULL,$coff2det->coff2det_course_relId,$coff2det->coff2detTARel->inst_user_relId,$srd2->srd2SemregRel->semregStdRel->std_user_relId) : TRUE;
            return $instEvaluated && $tAEvaluated;
        }

        return false;
    }

    public function checkIfEvaluated($gset,$courseId, $evalueeId, $evaluatorId){
        $controlEntries = AseEvaluationControlSetting::find()
            ->where([
                'candidate_staff_relId' => $evalueeId,
                'course_relId' => $courseId,
            ])
            ->all();
        if($controlEntries){
            foreach ($controlEntries as $controlEntry) {
                $evaluationResults = $controlEntry->aseEvaluationResults;
                $result = array_filter($evaluationResults, function($result) use ($gset, $evaluatorId) {
                    return in_array($evaluatorId, json_decode($result->completed_staffs, true));
                });
                if ($result) {
                    return true;
                }
            }
            return false;        
        }
        return true;
    }

    /**
     * Gets query for [[ControlSettingRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getControlSettingRel()
    {
        return $this->hasOne(AseEvaluationControlSetting::class, ['id' => 'control_setting_relId']);
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
     * Gets query for [[DeptPlanRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDeptPlanRel()
    {
        return $this->hasOne(AseDepartmentEvaluationSchedule::class, ['id' => 'dept_plan_relId']);
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
}