<?php

namespace frontend\modules\Depal\models;

use Yii;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry as oldSetting;
use \backend\models\User;
use \backend\models\Department;

/**
 * This is the model class for table "depal_setting".
 *
 * @property int $id
 * @property int $section_relId
 * @property string $category_IDS
 * @property int $depts_count
 * @property string $elligible_depts
 * @property int $start_date
 * @property int $end_date
 * @property int $status
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string $data
 *
 * @property DepalCategorizedCandidates[] $depalCategorizedCandidates
 * @property DepalChoice[] $depalChoices
 * @property User $createdBy
 * @property AcActivePrograms $sectionRel
 * @property User $updatedBy
 */
class DepalSetting extends \yii\db\ActiveRecord
{

    public const STATUS_ACTIVE = 0;
    public const STATUS_UPNEXT = 1;
    public const STATUS_CLOSED = -1;

    public $startdate,$enddate;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depal_setting';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['section_relId', 'category_IDS', 'depts_count', 'elligible_depts', 'start_date', 'end_date', 'created_by', 'created_at', 'data'], 'required'],
            [['section_relId', 'depts_count', 'start_date', 'end_date', 'status', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['category_IDS', 'elligible_depts','startdate','enddate'], 'safe'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['section_relId'], 'exist', 'skipOnError' => true, 'targetClass' => AcActivePrograms::className(), 'targetAttribute' => ['section_relId' => 'id']],
            [['updated_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updated_by' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'section_relId' => Yii::t('rms', 'Academic Section'),
            'category_IDS' => Yii::t('rms', 'Criteria Categories'),
            'depts_count' => Yii::t('rms', 'Choice Limit'),
            'elligible_depts' => Yii::t('rms', 'Elligible Departments'),
            'start_date' => Yii::t('rms', 'Start Date'),
            'startdate' => Yii::t('rms', 'Start Date'),
            'end_date' => Yii::t('rms', 'End Date'),
            'enddate' => Yii::t('rms', 'End Date'),
            'status' => Yii::t('rms', 'Status'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getDeptChoiceAlloted($student){
        // choice model
        $choice = ['choice' => null,'secured' => null];
        $choiceObject = DepalChoice::find()
            ->where([
                'setting_relId' => $this->id,
                'std_relId' => $student->id
            ])
            ->all();
        if ($choiceObject && count($choiceObject) > 0) {
            $choiceObject = $choiceObject[0];
            $choice['choice'] = $this->stringifyChoice($choiceObject->dept_selected);
            $choice['secured'] = $choiceObject->securedDeptRel ? $choiceObject->securedDeptRel->dept_code : '-';
        }

        return $choice;
    }

    private function stringifyChoice($dept_selected){
        if (!is_null($dept_selected)) {
            $deptsChose = [];
            foreach (json_decode(trim($dept_selected,'"')) as $dept) {
                $department = \backend\models\Department::findOne($dept);
                if ($department && count([$department]) > 0) {
                    $deptsChose[] = $department->dept_code;
                }
            }
            return implode(',', $deptsChose);
        }else{
            return "-";
        }
    }

    public function getSettingHeader(){
        return "Department selection control setting >>".$this->sectionRel->getAcadYearHeader()."-".$this->sectionRel->getClassLevelHeader();
    }

    public function stringifyDepartments(){
        if (is_null($this->elligible_depts)) {
            return "[]";
        }
        $str = [];
        // $email = trim($string,'"');
        $depts = json_decode(trim($this->elligible_depts,'"'));
        $modelDept = new Department();
        foreach($depts as $deptId){
            $model = $modelDept->findOne($deptId);
            if ($model) {
                $str[] = $model->dept_code;
            }
            
        }
        return implode(',', $str);
    }

    public function stringifyCategories(){
        $str = [];
        $modelCategory = new DepalCategory();
        $catids = json_decode(trim($this->category_IDS,'"'));
        foreach($catids as $categoryId){
            $model = $modelCategory->findOne($categoryId);
            if($model){
                $str[] = $model->category_name;
            }
        }
        return implode(',', $str);
    }

    public function getScheduleStatus($update = true){
        $time = time();
        $status = '';
        if ($time <= $this->end_date && $time >= $this->start_date) {
            $thisStatus = self::STATUS_ACTIVE;
            $status = 'Active';
        }else if($time < $this->start_date){
            $thisStatus = self::STATUS_UPNEXT;
            $status = 'Up-Next';
        }else if($time > $this->end_date){
            $thisStatus = self::STATUS_CLOSED;
            $status = 'Closed';
        }
        // update database
        if($this->status != $thisStatus && $update){
            $this->status = $thisStatus;
            $this->getData();
            $this->update();
        }

        $this->status = $thisStatus;
        return $status;
    }

    public function getScheduleInterval(){
        return date("d/m/Y",$this->start_date)."-".date("d/m/Y",$this->end_date);
    }

    public function getActiveAcademicSections(){
        // activePrograms
        $activePrograms = [];
        $activePrograms = \frontend\modules\AcademicCalendar\models\AcActivePrograms::find()
            ->where(' status = 1 ')->all();
        if ($activePrograms && count($activePrograms) > 0) {
            foreach ($activePrograms as $acp) {
                $acp->getStatus();
                if ($acp->status = $acp::STATUS_ACTIVE) {
                    $activePrograms[] = ['id' => $acp->id,'clslvl' => $acp->getClassLevel()." ({$acp->deptRel->dept_code}/{$acp->programRel->getProgramHeader()})"];
                }
            }
        }

        return $activePrograms;
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'section_relId'=>$this->section_relId,
            'category_IDS'=>$this->category_IDS,
            'elligible_depts'=>$this->elligible_depts,
            'depts_count'=>intval($this->depts_count),
            'start_date'=>intval($this->start_date),
            'end_date'=>intval($this->end_date),
            'status'=>intval($this->status),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function alreadyExists(){
        return self::find()
            ->where([
                'section_relId' => $this->section_relId
            ])
            ->count() > 0;
    }

    public function checkForListChange(){
        $elligible_depts = json_decode(trim($this->elligible_depts,'"'));
        $quotas = $this->depalDeptQoutas;

        if (count($elligible_depts) == count($quotas)) {
            $this->updateQuota();
            return;
        }else{
            foreach ($quotas as $quota) {
                if (!in_array($quota->dept_relId, $elligible_depts)) {//dept has been removed from elligible list
                    $quota->delete();
                }
            }
            foreach ($elligible_depts as $dept) {
                // check if the allocation quota percentage entry exists:
                $deptQuota = $this->getDepalDeptQoutas()->andFilterWhere(['dept_relId' => $dept])->all();
                if ($deptQuota && count($deptQuota) > 0) {
                    // check if duplicate or not:
                    $k = count($deptQuota);
                    if ( $k > 1) {
                        $save = $deptQuota[0];
                        DepalDeptQuota::deleteAll("id <> ".$save->id." AND setting_relId=".$this->id);
                        // while ($k > 1) {
                        //     $deptQuota[$k-1]->delete();
                        //     $k = count($deptQuota);
                        // }
                    }
                    continue;
                }else{
                    // new dept is just added after quota distribution is made:
                    // so create new entry:
                    $model = new DepalDeptQuota();
                    $model->setting_relId = $this->id;
                    $model->dept_relId = $dept;
                    $model->q_percentage = 100 - $this->getDepalDeptQoutas()->sum('q_percentage');
                    $model->created_at = time();
                    $model->created_by = \Yii::$app->user->identity->id;

                    $model->getData();

                    if ($model->save()) {
                        continue;
                    }else{
                        // var_dump($model->getErrors);
                    }
                }
            }
            // if there is no any user managed entry in the quota list, update auto_allocated_quota of each site
            $this->updateQuota();
        }
    }

    private function updateQuota(){
        $userManagedQuota = $this->getDepalDeptQoutas()->andFilterWhere([' <> ','auto_allocated' ,1])->count();
        if ($userManagedQuota > 0) {
            // var_dump($userManagedQuota);
            // managing weight will be the job of admin/any person in charge of doing that.
        }else{
            $quotas = $this->depalDeptQoutas;
            $count = count($quotas);
            $sum = 0;
            $index = 0;
            foreach ($quotas as $quota) {
                $percentage = 0;
                if ($index == $count - 1) {
                    $percentage = sprintf("%0.2f",100-$sum);
                }else{
                    $percentage = sprintf("%0.2f",100/$count);
                    $sum += $percentage;
                }
                if ($quota->q_percentage != $percentage) {
                    $quota->q_percentage = $percentage;
                    $quota->getData();
                    if($quota->update()){

                    }else{
                        // var_dump($quota->getErrors());
                    }   
                }
                $index++;
            }
        }
    }
    public function distributeDepalQuotas(){
        $quotas = $this->depalDeptQoutas;
        if ($quotas && count($quotas) > 0) {
            // check if there is any added/removed dept in the list.
            $this->checkForListChange();
            return $this->depalDeptQoutas;
        }else{
            $eligibleDepts = json_decode(trim($this->elligible_depts,'"'));
            $siteCount = count($eligibleDepts);
            if (count($eligibleDepts) > 0) {
                // $eligibleDepts[count($eligibleDepts) - 1] = 100-$sum;
                $sum = 0;
                foreach($eligibleDepts as $index => $value){
                    $deptQuota = $this->getDepalDeptQoutas()->andFilterWhere(['dept_relId' => $value])->all();
                    if ($deptQuota && count($deptQuota) > 0) {
                        continue;
                    }
                    $percentage = 0;
                    if ($index == count($eligibleDepts) - 1) {
                        $percentage = 100-$sum;
                    }else{
                        $percentage = sprintf("%0.2f",100/$siteCount);
                        $sum += $percentage;
                    }
                    // create entry in dept_qouta table:
                    $model = new DepalDeptQuota();
                    $model->setting_relId = $this->id;
                    $model->dept_relId = $value;
                    $model->q_percentage = $percentage;
                    $model->created_at = time();
                    $model->created_by = \Yii::$app->user->id;

                    $model->getData();

                    if ($model->save()) {
                        continue;
                    }else{
                        // var_dump($model->getErrors);
                    }
                }

                return $this->depalDeptQoutas;
            }else{
                return NULL;
            }
        }
    }

    public static function importSetting(){
        // ->where(' depts_to_choose_from IS NOT NULL ')
        $unlinked = oldSetting::find()->where(['linked' => 0])->all();
        if ($unlinked && count($unlinked) > 0) {
            foreach ($unlinked as $setting) {
                $academicSections = AcActivePrograms::find()
                    ->innerJoin('ac_academic_year','ac_academic_year.id=ac_active_programs.academic_year_relId')
                    ->where([
                        'semester_relId' => $setting->sem_relId,
                        'class_level_relId' => $setting->clslvl_relId,
                    ])
                    ->andWhere(['school_year' => $setting->acadYearRel->sch_year_desc])
                    ->all();
                if ($academicSections && count($academicSections) > 0) {
                    foreach ($academicSections as $section) {
                        // sections found, create setting for each selection criteria categories.
                        $criteriaCategories = DepalCategory::find()->all();
                        $criCat = [];
                        foreach ($criteriaCategories as $category) {
                            $criCat[] = $category->id;
                        }
                        // in case of dept_to_select_from attrivute of old setting is empty, JSON of all dept ids would be used.
                        $newSetting = new DepalSetting();
                        if (is_null($setting->depts_to_choose_from)) {
                            $depts = [];
                            $departments = \backend\models\Department::find()->where(' status = 1 ')->all();

                            if ($departments && count($departments) > 0) {
                                foreach ($departments as $dept) {
                                    $depts[] = $dept->id;
                                }
                                $newSetting->elligible_depts = json_encode($depts);
                            }else{
                                continue;
                            }
                        }else{
                            $newSetting->elligible_depts = $setting->depts_to_choose_from;
                        }
                        $newSetting->section_relId = $section->id;
                        $newSetting->category_IDS = json_encode($criCat);
                        $newSetting->depts_count = $setting->max_count_dept;
                        $newSetting->start_date = strtotime($setting->date_from);
                        $newSetting->end_date = strtotime($setting->date_to);
                        $newSetting->created_by = \Yii::$app->user->id;
                        $newSetting->created_at = time();
                        $newSetting->old_setting_id = $setting->id;
                        $newSetting->getData();

                        if ($newSetting->save()) {
                            $setting->linked = 1;
                            $setting->update(false);
                        }else{
                            echo " importing failed!";
                            // var_dump($newSetting);
                            // var_dump($setting);
                            var_dump($newSetting->getErrors());
                            die();
                        }
                    }
                }else{
                   continue;
                }
            }
        }
    }

    /**
     * Gets query for [[DepalCategorizedCandidates]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepalCategorizedCandidates()
    {
        return $this->hasMany(DepalCategorizedCandidates::className(), ['setting_relId' => 'id']);
    }

    /**
     * Gets query for [[DepalChoices]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepalChoices()
    {
        return $this->hasMany(DepalChoice::className(), ['setting_relId' => 'id']);
    }

    /**
     * Gets query for [[DepalDeptQoutas]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepalDeptQoutas()//depalDeptQoutas
    {
        return $this->hasMany(DepalDeptQuota::className(), ['setting_relId' => 'id']);
    }

    /**
     * Gets query for [[CreatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    /**
     * Gets query for [[SectionRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSectionRel()
    {
        return $this->hasOne(AcActivePrograms::className(), ['id' => 'section_relId']);
    }

    /**
     * Gets query for [[UpdatedBy]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'updated_by']);
    }
}
