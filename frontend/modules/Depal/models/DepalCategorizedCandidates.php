<?php

namespace frontend\modules\Depal\models;

use Yii;
use backend\models\User;
use frontend\modules\Depal\models\DepalCategory;

/**
 * This is the model class for table "depal_categorized_candidates".
 *
 * @property int $id
 * @property int $setting_relId
 * @property int $category_relId
 * @property string $criteria_name
 * @property string $candidates
 * @property int $finalized
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_by
 * @property int|null $updated_at
 * @property string $data
 *
 * @property DepalCategory $categoryRel
 * @property User $createdBy
 * @property DepalSetting $settingRel
 * @property User $updatedBy
 */
class DepalCategorizedCandidates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depal_categorized_candidates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['setting_relId', 'category_relId', 'criteria_name', 'candidates', 'created_by', 'created_at', 'data'], 'required'],
            [['setting_relId', 'category_relId', 'finalized', 'created_by', 'created_at', 'updated_by', 'updated_at'], 'integer'],
            [['candidates'], 'safe'],
            [['data'], 'string'],
            [['criteria_name'], 'string', 'max' => 50],
            [['category_relId'], 'exist', 'skipOnError' => true, 'targetClass' => DepalCategory::class, 'targetAttribute' => ['category_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['setting_relId'], 'exist', 'skipOnError' => true, 'targetClass' => DepalSetting::class, 'targetAttribute' => ['setting_relId' => 'id']],
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
            'setting_relId' => Yii::t('rms', 'Setting Entry'),
            'category_relId' => Yii::t('rms', 'Criteria category'),
            'criteria_name' => Yii::t('rms', 'Criteria Name'),
            'candidates' => Yii::t('rms', 'Candidates'),
            'finalized' => Yii::t('rms', 'Finalized'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getUnClosedDepalSchedule(){
        $std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->id]);
        if ($std) {
            $catgegorizeds = DepalCategorizedCandidates::find()
                ->innerJoin('depal_setting','depal_setting.id=depal_categorized_candidates.setting_relId')
                ->where(['depal_setting.status' => [1,0]])
                ->all();
            if ($catgegorizeds && count($catgegorizeds) > 0) {
                foreach ($catgegorizeds as $categroized) {
                    if (in_array($std->id, json_decode($categroized->candidates))) {
                        $st = $categroized->settingRel->getScheduleStatus(true);
                        $btn = '';
                        $choice = DepalChoice::find()->where(['std_relId' => $std->id,'setting_relId' => $categroized->setting_relId])->one();
                        if ($choice) {
                            $btn = "<span class='bg-warning pull-right'>Department choice registered.</span>";
                        }else{
                            if (in_array($st, ['Active','Up-Next'])) {
                                $btn = "<button class='btn btn-xs btn-primary pull-right' onclick='placeChoice({$categroized->id});return false;'>Place Choice</button>";
                            }
                        }
                        
                        $settingHeader = $categroized->settingRel->getSettingHeader();
                        return "{$settingHeader}: Schedule : <b>".$st."</b> >> <i style='background-color: #EC5;'>".$categroized->settingRel->getScheduleInterval()."</i>{$btn}";
                    }
                }
                return "<i class='pull-right bg-warning'><b>No Active/Upcomming department choice found for you.</b></i>";
            }else{
                return "<i class='pull-right bg-warning'>No schedule for department selection found.</i>";
            }
            // 
            var_dump($catgegorizeds);
            echo "<hr>";
            var_dump($std);
        }
    }

    public function checkCategoryMatches($student,$alloted,$settingModel){
        $allCategorizations = $settingModel->depalCategorizedCandidates;
        foreach ($allCategorizations as $category) {
            $candidates = json_decode(trim($category->candidates,'"'));
            // $dif = array_diff($candidates, [$student,$alloted]);
            if (in_array($student, $candidates) && in_array($alloted, $candidates)) {
                return [true,"<< In the same category-criteria: {$category->criteria_name}, with {$alloted} >> "];
            }
        }
        return [false,""];
    }

    public function getCategorizedUnder($student,$settingModel){
        $allCategorizations = $settingModel->depalCategorizedCandidates;
        $catag = [];
        foreach ($allCategorizations as $category) {
            $candidates = json_decode(trim($category->candidates,'"'));
            if (in_array($student->id, $candidates)) {
                $catag[] = substr($category->criteria_name, 0, 10).".";
            }
        }
        return implode('/', $catag);
    }

    public function finalizeSelection(){
                // schedule should be closed:
        if ($this->settingRel->status != $this->settingRel::STATUS_CLOSED) {
            return "Schedule is NOT closed";
        }
        // $unselected = DepalChoice::find()->where(['setting_relId' => $this->setting_relId,'dept_selected' => NULL])->all();
        // if ($unselected && count($unselected) > 0) {
        //     return "Department selection not completed, yet.";
        // }

        $this->finalized = 1;
        $this->updated_by = \Yii::$app->user->id;
        $this->updated_at = time();
        $this->getData();

        if ($this->update()) {
            foreach ($this->settingRel->depalChoices as $choice) {
                $choice->finalizad = 1;
                $choice->updated_at = time();
                $choice->updated_by = \Yii::$app->user->id;
                $choice->getData();
                $choice->update();
            }
            return 1;
        }else{
            return json_encode($this->errors());
        }
    }

    public function groupEligibleCandidaets($settingId){
        $setting = DepalSetting::findOne($settingId);
        $categories = json_decode(trim($setting->category_IDS,'"'));
        if ($categories && count($categories) > 0) {
            foreach ($categories as $categoryId) {
                $categoryModel = DepalCategory::findOne($categoryId);
                if ($categoryModel) {

                    $modelGrouped = new DepalCategorizedCandidates();
                    $modelGrouped->category_relId = $categoryModel->id;
                    $modelGrouped->criteria_name = $categoryModel->category_name;
                    $modelGrouped->setting_relId = $setting->id;

                    $candidates = [];

                    if ($categoryModel->comparison_type == 'POINT_INTERVAL') {
                        // POINT_INTERVAL categorization is only allowed for CGPA of students .
                        // so group students with CGPA value of semreg_CGPA attribute from semesterRegistration 
                        // make use of max and min of categoryModel in this case 
                        // semesterRegistration entry should be of teh students grouped in the selected section only.
                        $section = $setting->sectionRel;
                        $semregs = \backend\models\SemesterRegistration2::find()
                            ->where([
                                'semreg_acadYear_relId' => $section->academicYearRel->schoolYearRel->id,
                                'semreg_semester_relId' => $section->semester_relId,
                                'semreg_clslvl_relId' => $section->class_level_relId
                            ])
                            ->andWhere(" semreg_CGPA between {$categoryModel->lower_bound} and {$categoryModel->upper_bound} ")
                            ->orderBy(['semreg_CGPA' => SORT_DESC])
                            ->all();
                        if ($semregs && count($semregs) > 0) {
                            foreach ($semregs as $semreg) {
                                if ($categoryModel->lower_bound <= $semreg->semreg_CGPA && $semreg->semreg_CGPA <= $categoryModel->upper_bound) {
                                    $candidates[] = $semreg->semreg_std_relId;
                                }
                            }
                        }

                        $modelGrouped->candidates = json_encode($candidates);

                        if ($modelGrouped->alreadyExists()) {
                            $existig = DepalCategorizedCandidates::find()
                                ->where([
                                    'category_relId' => $modelGrouped->category_relId,
                                    'setting_relId' => $modelGrouped->setting_relId,
                                ])->one();
                            if ($existig->candidates != $modelGrouped->candidates) {
                                $existig->candidates = $modelGrouped->candidates;
                                $existig->updated_at = time();
                                $existig->updated_by = Yii::$app->user->id;
                                $existig->getData();
                                if ($existig->update()) {
                                    continue;
                                }else{
                                    var_dump($modelGrouped->getErrors());
                                    die();
                                }
                            }else{
                                continue;
                            }
                        }

                        $modelGrouped->created_at = time();
                        $modelGrouped->created_by = Yii::$app->user->id;
                        $modelGrouped->getData();

                        if($modelGrouped->save()){
                            continue;
                        }else{
                            var_dump($modelGrouped->getErrors());
                            die();
                        }

                    }else if($categoryModel->comparison_type == 'POINT_VALUE'){
                        // POINT_VALUE categorizatrion is applicable for grouping student as per their EthnicGroup (from address of birth inforamtion)
                        // so check this value with user.eth_group_relId attribute 
                        // read all available EthnicGroups in address model and group students under each.

                        $ethGroups = \backend\models\EthnicGroup::find()->all();

                        if ($ethGroups && count($ethGroups) > 0) {
                            foreach ($ethGroups as $ethgroup) {
                                $modelGrouped = new DepalCategorizedCandidates();
                                $modelGrouped->category_relId = $categoryModel->id;
                                $modelGrouped->criteria_name = $ethgroup->name;
                                $modelGrouped->setting_relId = $setting->id;

                                $candidates = [];

                                $students = \backend\models\Student::find()
                                    ->innerJoin('user','user.id=student.std_user_relId')
                                    ->innerJoin('w_group_assignment','w_group_assignment.gas_std_relId=student.id')
                                    ->innerJoin('w_group_setup','w_group_setup.id=w_group_assignment.gas_gset_relId')
                                    ->where([
                                        'user.eth_group_relId' => $ethgroup->id,
                                        'gs_active_prog_relId' => $setting->section_relId
                                    ])
                                    ->all();
                                if ($students && count($students) > 0) {
                                    foreach ($students as $student) {
                                        $candidates[] = $student->id;
                                    }

                                    $modelGrouped->candidates = json_encode($candidates);
                                    if ($modelGrouped->alreadyExists()) {
                                        $existig = DepalCategorizedCandidates::find()
                                            ->where([
                                                'category_relId' => $modelGrouped->category_relId,
                                                'setting_relId' => $modelGrouped->setting_relId,
                                                'criteria_name' => $modelGrouped->criteria_name,
                                            ])->one();
                                        if ($existig->candidates != $modelGrouped->candidates) {
                                            $existig->candidates = $modelGrouped->candidates;
                                            $existig->updated_at = time();
                                            $existig->updated_by = Yii::$app->user->id;
                                            $existig->getData();
                                            if ($existig->update()) {
                                                continue;
                                            }else{
                                                var_dump($modelGrouped->getErrors());
                                                die();
                                            }
                                        }else{
                                            continue;
                                        }
                                    }

                                    $modelGrouped->created_at = time();
                                    $modelGrouped->created_by = Yii::$app->user->id;
                                    $modelGrouped->getData();

                                    if($modelGrouped->save()){
                                        continue;
                                    }else{
                                        var_dump($modelGrouped->getErrors());
                                        die();
                                    }
                                }else{
                                    echo "No candidate available under this category {$categoryModel->category_name}-{$ethgroup->name}<br>";
                                }
                            }
                        }else{
                            echo "Criteria Category {$categoryModel->category_name} is in-applicable for selection control";
                            die();
                        }
                    }   
                }else{
                    continue;
                }
            }   
        }else{
            echo "Error with Evaluation category setup.";
            return;
        }
    }

    public function alreadyExists(){
        return self::find()
            ->where([
                'category_relId' => $this->category_relId,
                'setting_relId' => $this->setting_relId,
                'criteria_name' => $this->criteria_name,
            ])
            ->count() > 0;
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'criteria_name'=>$this->criteria_name,
            'category_relId'=>intval($this->category_relId),
            'setting_relId'=>intval($this->setting_relId),
            'finalized'=>intval($this->finalized),
            'candidates'=>$this->candidates,
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    /**
     * Gets query for [[CategoryRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryRel()
    {
        return $this->hasOne(DepalCategory::class, ['id' => 'category_relId']);
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
     * Gets query for [[SettingRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSettingRel()
    {
        return $this->hasOne(DepalSetting::class, ['id' => 'setting_relId']);
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
