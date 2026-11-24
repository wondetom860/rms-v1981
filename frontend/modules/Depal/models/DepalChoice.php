<?php

namespace frontend\modules\Depal\models;

use Yii;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntry as oldChoice;
use backend\models\User;
use backend\models\Student;
use backend\models\Department;

/**
 * This is the model class for table "depal_choice".
 *
 * @property int $id
 * @property int $std_relId
 * @property int $setting_relId
 * @property string $dept_selected
 * @property int|null $secured_dept_relId
 * @property int $finalizad
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdBy
 * @property Department $securedDeptRel
 * @property DepalSetting $settingRel
 * @property Student $stdRel
 * @property User $updatedBy
 */
class DepalChoice extends \yii\db\ActiveRecord
{
    public $categorized_under,$std_regId;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'depal_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['std_relId', 'setting_relId', 'dept_selected', 'created_at', 'created_by', 'data'], 'required'],
            [['std_relId', 'setting_relId', 'secured_dept_relId', 'finalizad', 'created_at', 'created_by', 'updated_at', 'updated_by'], 'integer'],
            [['dept_selected','categorized_under','std_regId'], 'safe'],
            [['data'], 'string'],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['secured_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['secured_dept_relId' => 'id']],
            [['setting_relId'], 'exist', 'skipOnError' => true, 'targetClass' => DepalSetting::class, 'targetAttribute' => ['setting_relId' => 'id']],
            [['std_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['std_relId' => 'id']],
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
            'std_relId' => Yii::t('rms', 'Student Name'),
            'setting_relId' => Yii::t('rms', 'Setting Entry'),
            'dept_selected' => Yii::t('rms', 'Dept Chose'),
            'secured_dept_relId' => Yii::t('rms', 'Alloted Dept'),
            'finalizad' => Yii::t('rms', 'Finalizad'),
            'created_at' => Yii::t('rms', 'Created At'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'std_regId' => Yii::t('rms', 'Reg. ID'),
            'categorized_under' => Yii::t('rms', 'Categorized Under'),
        ];
    }

    public function getCategorizedUnder($student,$settingModel){
        $allCategorizations = $settingModel->depalCategorizedCandidates;
        $catag = [];
        foreach ($allCategorizations as $category) {
            $candidates = json_decode($category->candidates);
            if (in_array($student->id, $candidates)) {
                $catag[] = substr($category->criteria_name, 0,8).".";
            }
        }
        return implode('/', $catag);
    }

    public function getCGPA($section,$student){
        $semreg = \backend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId' => $section->academicYearRel->schoolYearRel->id,
                'semreg_semester_relId' => $section->semester_relId,
                'semreg_clslvl_relId' => $section->class_level_relId,
                'semreg_std_relId' => $student->id,
            ])
            ->all();
        if ($semreg && count($semreg) > 0) {
            return sprintf("%0.3f",$semreg[0]->semreg_CGPA);
        }else{
            return NULL;
        }
    }

    public function stringifyDepartments(){
        if (is_null($this->dept_selected)) {
            return "-";
        }
        $str = [];
        $modelDept = new Department();
        foreach(json_decode(trim($this->dept_selected,'"')) as $deptId){
            $str[] = $modelDept->findOne($deptId)->dept_code;
        }
        return implode(',', $str);
    }

    private  function sortStudentsByCGPA($setting,$candidates,$categoryModel){
        $section = $setting->sectionRel;
        $semregs = \backend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId' => $section->academicYearRel->schoolYearRel->id,
                'semreg_semester_relId' => $section->semester_relId,
                'semreg_clslvl_relId' => $section->class_level_relId
            ])
            ->andWhere(['semreg_std_relId' => $candidates])
            ->orderBy(['semreg_CGPA' => SORT_DESC])
            ->all();
        $candidates2 = [];
        if ($semregs && count($semregs) > 0) {
            foreach ($semregs as $semreg) {
                if ($categoryModel->lower_bound <= $semreg->semreg_CGPA && $semreg->semreg_CGPA <= $categoryModel->upper_bound) {
                    $candidates2[] = $semreg->semreg_std_relId;
                }
            }
        }

        return $candidates2;
    }

    private function checkIfConflicting($choice,$sites,$alloted,$criteriaGroup,$student){
        $message = "";
        if (!$alloted[$choice] || count($alloted[$choice]) < 1) {
            $message .= " << None allocated case {$choice} >> <br>";
            return [false,$choice,$message];
        }
        $tolerate = intval(count(json_decode(trim($criteriaGroup->candidates,'"'))) / count($sites));
        // all quota might be occupied by first_comers : HIGH_CGPAs.
        $cAlloted = count($alloted[$choice]);
        if ($cAlloted >= $tolerate) {
            // check qouta:
            $site_quota = $criteriaGroup->settingRel->getDepalDeptQoutas()->andFilterWhere(['dept_relId' => $choice])->all();
            if ($site_quota && count($site_quota) > 0) {
                $site_quota_perc = $site_quota[0]->q_percentage;
                // find total dept_selected_student_count:
                $dept_selected_student_count = $criteriaGroup->settingRel->getDepalChoices()->count();

                // allocated_student_count to the site/department = $cAlloted
                $alloted_percent = intval(($cAlloted+1)/$dept_selected_student_count * 100);
                if ($alloted_percent <= $site_quota_perc) {
                    $message .= " << department quota has vacant allotmment, case.>> ";
                    return [false,$choice,$message];
                }
            }
            $message .= " << All ocupied, look for next Choice:>>, Criteria: {$criteriaGroup->criteria_name} tolerate: {$tolerate}, allotedCount: {$cAlloted} <br>";
            return [true,null,$message];
        }

        // if allotting tolerates to have more candicates with the same category in the same site:
        if ($cAlloted < $tolerate) {
            $message .= " << Alloted but tolerable:>>, Criteria: {$criteriaGroup->criteria_name} tolerate: {$tolerate}, allotedCount: {$cAlloted} ";
            return [false,$choice,$message];
        }
        // $hisCategorizeGroup = $criteriaGroup->getCategorizedUnder($studentModel,$criteriaGroup->settingRel);
        $c = 0;
        foreach ($alloted[$choice] as $alloted) {
            // check if they are from the same categorizedGroup: CGPA/EthnicGroup
            // if any match is found, look for the next choice:
            $atLeastOneMatches = $criteriaGroup->checkCategoryMatches($student,$alloted,$criteriaGroup->settingRel);
            if ($atLeastOneMatches[0]) {
                $message .= " << {$atLeastOneMatches[1]} >> Already occupied, take next choice. :>> <br> ";
                return [true,null,$message];

                // if ($c < $tolerate) {
                //     echo " <<Take next site :>> ";
                //     $c++;
                //     continue;
                // }else{
                //     echo " <<Non conflicting found from inside :>> ";
                //     return [true,null];
                // }
                
            }else{
                echo " << No competent found case :>> <br>";
                return [false,$choice,$message];
            }
        }
        return [false,$choice,$message];

    }

    private function getChoice($settingModel,$studentId){
        return DepalChoice::find()
            ->where([
                'setting_relId' => $settingModel->id,
                'std_relId' => $studentId
            ])
            ->one();
    }

    private function initializeSites($sites){
        $__allotedInitial = [] ;
        foreach ($sites as $site) {
            $__allotedInitial[$site] = [];
        }

        return $__allotedInitial;
    }

    private function getSafeDept($settingModel,$studentModel,$choices,$sites,$alloted){
        // if student is CIVIL: persist his/her first choice
        // this should be done at the last, otherwise it will close the choise of high scoring and first choice deserving candidates.
        if (in_array(strtoupper($studentModel->std_type),["CIVIL","FOREIGN"])) {
            return [0,$choices[0]];
        }

        $diff = array_diff($sites, $choices);
        
        // this shall iterate over out_of_choice_depts till it gets non-conflicting one:
        // $model->checkIfConflicting($choices[$choiceCount],$sites,$alloted,$criteriaGroup,$student);

        return [1,$diff[array_rand($diff)]];
    }

    public function runnAllign($settingModel){
        $sites = json_decode(trim($settingModel->elligible_depts,'"'));
        $choices = DepalChoice::find()
            ->where(['setting_relId' => $settingModel->id])
            ->andWhere(' dept_selected IS NOT NULL ')
            ->all();
        $model = new DepalChoice();
        $allotedStudents = [];
        $report = "";
        $citeriaGroups = $settingModel->getDepalCategorizedCandidates()
            ->innerjoin('depal_category','depal_category.id=depal_categorized_candidates.category_relId')
            ->orderBy(['lower_bound' => SORT_DESC])
            ->all();
        $alloted = $model->initializeSites($sites); //this array will hold all alloted students: structure => ['stite']['candidates'] form.
        if ($choices && count($choices) > 0) {
            $laterDistib = [];
            foreach ($citeriaGroups as $criteriaGroup) {
                $candidates = json_decode(trim($criteriaGroup->candidates,'"'));
                $candidates = $model->sortStudentsByCGPA($settingModel,$candidates,$criteriaGroup->categoryRel);
                $tStuds = count($candidates);
                $report .= "<br>Allotting by criteria: {$criteriaGroup->criteria_name}, Total Students: {$tStuds}<br>";
                $site = null;
                $cc1 = 0;
                foreach ($candidates as $student) {
                    $choice = $model->getChoice($settingModel,$student);
                    if (in_array($student, $allotedStudents)) {
                        continue;
                    }else{
                        $allotedStudents[] = $student;
                    }
                    $conflicting = false;
                    $choiceCount = 0;
                    $studentModel = Student::findOne($student);
                    if (!($choice)) {
                        $report .= " <br><hr>Student {$studentModel->std_regId} did not chose department yet. ";
                        continue;
                    }
                    if (in_array($choice->id, $laterDistib)) {
                        continue;
                    }
                    // civil student and foreigners are skiped for later allotting:
                    if (in_array(strtoupper($studentModel->std_type),['CIVIL','FOREIGN'])) {
                        $laterDistib[] = $choice->id;
                        $report .= " <br><hr>Student {$studentModel->std_regId} listed in laterDistib. Type: {$studentModel->std_type}";
                        continue;
                    }
                    $choices = json_decode(trim($choice->dept_selected,'"'));
                    $chh = implode(',',$choices);
                    $report .= "<br><hr>Student : {$studentModel->std_regId}: Choice: {$chh} <br>";
                    while ($choiceCount < $settingModel->depts_count && isset($choices[$choiceCount])) {
                        // this will itrate till it finds approprite site for an employee, within his choice and tolerate interval
                        $report .= "<br>&nbsp;&nbsp; Choice: {$choiceCount} =>({$choices[$choiceCount]}) >> ";
                        $site = $model->checkIfConflicting($choices[$choiceCount],$sites,$alloted,$criteriaGroup,$student);
                        $conflicting = $site[0];
                        $report .= $site[2];
                        $site = $site[1];
                        $report .= " << After checkIfConflicting >> Confilicting: {$conflicting} >> ";
                        if ($conflicting) {
                            $choiceCount++;
                            continue;
                        }else{
                            // $site = $site[1];
                            $conflicting = false;
                            break;
                        }
                    }
                    if ($conflicting) {
                        // $site = $model->getSafeDept($settingModel,$student);
                        $report .= " <br>&nbsp;&nbsp; << All choices are conflicting, putting for later random distribution: <hr>";
                        $laterDistib[] = $choice->id;
                        $conflicting = true;
                        continue;
                    }
                    $alloted[$site][] = $student;
                    // update choice db_table
                    if (($choice->secured_dept_relId != $site) && $choice->finalizad) {
                        $choice->secured_dept_relId = $site;
                        $choice->updated_by = Yii::$app->user->id;
                        $choice->updated_at = time();
                        $choice->getData();
                        if ($choice->update()) {
                            $report .= " <br>&nbsp;&nbsp; Updated choice of {$studentModel->std_regId} to {$site} <hr>";
                            continue;
                        }else{
                            var_dump($choice->getErrors());
                            die();
                        }   
                    }else{
                        $report .= $choice->finalizad ? "<br>Already Alloted, {$studentModel->std_regId}.({$site})" : "Selection event not finalizad." ;
                    }
                    $report .= " <br>&nbsp;&nbsp;&nbsp;&nbsp; Found Dept {$site} for {$studentModel->std_regId}. ";
                    $report .= "<hr>";
                }
            }
            // in this stage, all candidates on the category should get their approprite dept.
            if (count($laterDistib) > 0) {
                $unallocatedChoices = DepalChoice::find()->where(['id' => $laterDistib])->all();
                if ($unallocatedChoices && count($unallocatedChoices) > 0) {
                    foreach($unallocatedChoices as $choice){
                        $site = $model->getSafeDept($choice->settingRel,$choice->stdRel,json_decode(trim($choice->dept_selected,'"')),$sites,$alloted);
                        $alloted[$site[1]][] = $choice->std_relId;
                        if (!is_null($choice->secured_dept_relId)) {
                            $report .= "<br>Student {$choice->stdRel->std_regId} already alloted in ({$choice->secured_dept_relId})";
                            continue;
                        }
                        // $choice->update();
                        if (($choice->secured_dept_relId != $site[1] ) && $choice->finalizad) {
                            $choice->secured_dept_relId = $site[1];
                            $choice->updated_by = Yii::$app->user->id;
                            $choice->updated_at = time();
                            $choice->getData();

                            if ($choice->update()) {
                                $report .= "<br>Student {$choice->stdRel->std_regId} alloted in {$site[1]}";
                            }else{
                                var_dump($choice->getErrors());
                                die();
                            }
                        }else{
                            $report .= $choice->finalizad ? "<br>Already Alloted, {$studentModel->std_regId}.({$site})" : "Selection event not finalizad." ;
                        }                
                    }
                }
            }
        }else{
            echo "Department choice not made by any, yet";
        }
        return $report;
    }
    public function getData(){
        $this->data = md5(serialize((object)[
            'dept_selected'=>$this->dept_selected,
            'std_relId'=>intval($this->std_relId),
            'setting_relId'=>intval($this->setting_relId),
            'secured_dept_relId'=>intval($this->secured_dept_relId),
            'finalizad'=>intval($this->finalizad),
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public static function importDepartmentChoice(){
        // look for any un-imported department choice entry.
        $unimported = oldChoice::find()->where(' imported = 0 ')->all();
        if ($unimported && count($unimported) > 0) {
            foreach ($unimported as $deptChoice) {
                $newSelection = new DepalChoice();

                $newSelection->std_relId = $deptChoice->student_relId;
                $newSelection->dept_selected = $deptChoice->dept_choice;
                $newSelection->secured_dept_relId = null;
                $newSelection->setting_relId = self::getFromOldSetting($deptChoice->settingRel);
                $newSelection->created_at = time();
                $newSelection->created_by = Yii::$app->user->id;
                $newSelection->getData();

                if ($newSelection->save()) {
                    $deptChoice->imported = 1;
                    $deptChoice->update(false);
                }else{
                    echo "Import failed!,";
                    var_dump($newSelection->getErrors());
                }
            }
        }
    }

    private static function getFromOldSetting($oldSettingRel){
        // $oldSettingRel
        $newEquivalent = DepalSetting::find()
            ->where(['old_setting_id' => $oldSettingRel->id])
            ->all();
        if ($newEquivalent && count($newEquivalent) > 0) {
            return $newEquivalent[0]->id;
        }else{
            echo " No equivalent setting entry found!";
        }
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
     * Gets query for [[SecuredDeptRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSecuredDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'secured_dept_relId']);
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
     * Gets query for [[StdRel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStdRel()
    {
        return $this->hasOne(Student::class, ['id' => 'std_relId']);
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
