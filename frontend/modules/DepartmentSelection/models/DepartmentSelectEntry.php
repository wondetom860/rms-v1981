<?php

namespace frontend\modules\DepartmentSelection\models;

use Yii;
use backend\models\User;
use backend\models\Department;
use backend\models\Student;
// use backend\models\Student;

/**
 * This is the model class for table "department_select_entry".
 *
 * @property int $id
 * @property int $student_relId
 * @property int $setting_relId
 * @property string $dept_choice
 * @property int|null $approved_dept_relId
 * @property string|null $approved_at
 * @property int|null $approved_by
 * @property string $created_at
 * @property int $created_by
 * @property string|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 * @property int $imported
 *
 * @property User $approvedBy
 * @property Department $approvedDeptRel
 * @property User $createdBy
 * @property SelectionSettingEntry $settingRel
 * @property Student $studentRel
 * @property TouchedGrades $updatedBy
 */
class DepartmentSelectEntry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department_select_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['student_relId', 'setting_relId', 'dept_choice', 'created_at', 'created_by', 'data'], 'required'],
            [['student_relId', 'setting_relId', 'approved_dept_relId', 'approved_by', 'created_by', 'updated_by','imported'], 'integer'],
            [['dept_choice', 'data', 'status'], 'string'],
            [['approved_at', 'created_at', 'updated_at'], 'safe'],
            [['approved_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['approved_by' => 'id']],
            [['approved_dept_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Department::class, 'targetAttribute' => ['approved_dept_relId' => 'id']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['created_by' => 'id']],
            [['setting_relId'], 'exist', 'skipOnError' => true, 'targetClass' => SelectionSettingEntry::class, 'targetAttribute' => ['setting_relId' => 'id']],
            [['student_relId'], 'exist', 'skipOnError' => true, 'targetClass' => Student::class, 'targetAttribute' => ['student_relId' => 'id']],
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
            'student_relId' => Yii::t('rms', 'Student Full Name'),
            'setting_relId' => Yii::t('rms', 'Setting Entry'),
            'dept_choice' => Yii::t('rms', 'Dept Choice'),
            'approved_dept_relId' => Yii::t('rms', 'Approved Dept.'),
            'approved_at' => Yii::t('rms', 'Approved At'),
            'approved_by' => Yii::t('rms', 'Approved By'),
            'created_at' => Yii::t('rms', 'Date'),
            'created_by' => Yii::t('rms', 'Created By'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'data' => Yii::t('rms', 'Data'),
            'status' => Yii::t('rms', 'Schedule'),
            'imported' => Yii::t('rms', 'Import Status'),
        ];
    }

    public function getNewSelections(){
        return $this->find()->where(['approved_dept_relId'=>NULL])->all();
    }

    public function generateRegistrarRequestAlert(){

        return $this->find()->where(['approved_dept_relId'=>NULL])->count();
    }

    public function getData(){
        $mainData = (object)[
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
            'student_relId'=>intval($this->student_relId),
            'setting_relId'=>intval($this->setting_relId),
            'approved_dept_relId'=>intval($this->approved_dept_relId),
            'dept_choice'=>$this->dept_choice,
            'created_at'=>$this->created_at,
            'updated_at'=>$this->updated_at
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered(){
        return $this->getData() == $this->data;
    }

    public function getTitle(){
        return $this->getStudentFullName().' [ '.$this->getSettingENtry(1).' ]';
    }

    public function getStudentFullName(){
        return $this->studentRel->getFullName();
    }

    public function getSettingENtry($filter = NULL){
        $setting = $this->settingRel;
        if ($filter) {
            return $setting->acadYearRel->sch_year_desc.'-'.$setting->semRel->semester_name.' ('.$setting->getClassLevel($filter).' )';
        }

        return $setting->acadYearRel->sch_year_desc.'-'.$setting->semRel->semester_name.' ('.$setting->getClassLevel($filter).' )';
        
    }

    public function getLastGpas(){
        $setting = $this->settingRel;

        $semregEntry = \backend\models\SemesterRegistration2::find()
            ->where([
                'semreg_acadYear_relId' => $setting->acadYear_relId,
                'semreg_semester_relId' => $setting->sem_relId,
                'semreg_std_relId' => $this->student_relId,
                'semreg_clslvl_relId' => $setting->clslvl_relId,
            ])
            ->andWhere(['semreg_gradeCompleted' => 1])
            ->one();
        if ($semregEntry) {
            return [$semregEntry->semreg_SGPA,$semregEntry->semreg_CGPA];
        }
        return NULL;
    }

    public function getDeptSelection(){
        $depts = json_decode($this->dept_choice);
        
        $selecteds = "";

        if ($depts) {
            $c = 0;
            foreach ($depts as $keyDeptId) {
                $deptEntrry = Department::findOne($keyDeptId);
                if ($deptEntrry) {
                    $selecteds .= ++$c.". {$deptEntrry->dept_code} - {$deptEntrry->dept_name}<br>";
                }
            }
        }

        return $selecteds;
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
    public function getApprovedDeptRel()
    {
        return $this->hasOne(Department::class, ['id' => 'approved_dept_relId']);
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
    public function getSettingRel()
    {
        return $this->hasOne(SelectionSettingEntry::class, ['id' => 'setting_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStudentRel()
    {
        return $this->hasOne(Student::class, ['id' => 'student_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUpdatedBy()
    {
        return $this->hasOne(User::class, ['id' => 'updated_by']);
    }
}
