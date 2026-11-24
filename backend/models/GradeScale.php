<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "grade_scale".
 *
 * @property integer $id 
 * @property double $gs_min
 * @property double $gs_max
 * @property string $gs_ltr_grade
 * @property double $gs_gradePoint
 * @property string $gs_remark
 * @property integer $gs_created_by
 * @property string $gs_created_at
 * @property integer $gs_updated_by
 * @property string $gs_updated_at
 */
class GradeScale extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'grade_scale';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['gs_min', 'gs_clearable', 'gs_max', 'gs_forProgram', 'gs_ltr_grade', 'gs_gradePoint', 'gs_created_by', 'gs_created_at', 'gs_updated_by', 'gs_updated_at'], 'required'],
            [['gs_min', 'gs_max', 'gs_gradePoint', 'gs_pass_status'], 'number'],
            [['gs_remark', 'data'], 'string'],
            [['gs_created_by', 'gs_updated_by', 'gs_forProgram', 'gs_scale_status'], 'integer'],
            [['gs_created_at', 'gs_updated_at'], 'safe'],
            [['gs_ltr_grade'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('rms', 'ID'),
            'gs_min' => Yii::t('rms', 'Lower Bound'),
            'gs_max' => Yii::t('rms', 'Upper Bound'),
            'gs_ltr_grade' => Yii::t('rms', 'Letter Grade'),
            'gs_forProgram' => Yii::t('rms', 'For Program'),
            'gs_gradePoint' => Yii::t('rms', 'Grade Point'),
            'gs_remark' => Yii::t('rms', 'Remark'),
            'gs_scale_status' => Yii::t('rms', 'Grade status'),
            'gs_clearable' => Yii::t('rms', 'Clearable?'),
            'gs_created_by' => Yii::t('rms', 'Created By'),
            'gs_created_at' => Yii::t('rms', 'Created At'),
            'gs_updated_by' => Yii::t('rms', 'Updated By'),
            'gs_updated_at' => Yii::t('rms', 'Updated At'),
            'gs_pass_status' => Yii::t('rms', 'Pass Status'),
        ];
    }

    public function getData()
    {
        $mainData = (object)[
            'gs_ltr_grade' => $this->gs_ltr_grade,
            'gs_forProgram' => $this->gs_forProgram,
            'gs_min' => intval($this->gs_min),
            'gs_max' => intval($this->gs_max),
            'gs_pass_status' => intval($this->gs_pass_status),
            'gs_gradePoint' => floatval($this->gs_gradePoint),
            'gs_updated_by' => intval($this->gs_updated_by),
        ];
        return md5(serialize($mainData));
    }

    public static function getIGrade()
    {
        return self::findOne(['gs_ltr_grade' => 'I']);
    }

    public static function getNGrade()
    {
        return self::findOne(['gs_ltr_grade' => 'NG']);
    }

    public function getGradeInterval()
    {
        return "[" . sprintf("%0.2f", $this->gs_min) . "-" . sprintf("%0.2f", $this->gs_max) . "] = {$this->gs_ltr_grade}";
    }
    public function checkAltered()
    {
        return $this->getData() == $this->data;
    }

    public function unloackedExists()
    {
        return $this->find()->where(['<>', 'gs_locked', 1])->count() > 0;
    }

    public function isValid()
    {
        // return ($this->gs_pass_status != 1);
    }

    public function getClearableGrades($stdObject)
    {
        // setup the grades on grade scale creation
        // grades that can be cleared are marked as gs_clearable = 1.

        // $gsetObj = 
        return $this->find()
            ->where(['gs_forProgram' => $stdObject->std_prog_relId])
            ->andWhere(['gs_clearable' => 1])
            ->all();
    }

    public static function getLetterGradeFromTotalMark($srd2Object, $totalMarkObtained, $programId = null)
    {
        $pgmId = $srd2Object->srd2GroupRel->gs_prog_relId;
        return self::find()
            ->andWhere(':mark between gs_min AND gs_max', [':mark' => $totalMarkObtained])
            ->andWhere(['gs_forProgram' => $pgmId])
            ->andWhere(['gs_pass_status' => 1, 'gs_scale_status' => 1])
            ->andWhere(['OR', ['<>', 'gs_gradePoint', 0], ['gs_ltr_grade' => 'F']])
            ->one();
    }

    public function getInvalidGrades($studentObj, $cGsetObj)
    {
        // ->andWhere([
        //     'gs_forProgram'=>$cGsetObj->gs_prog_relId,
        // ])
        return $this->find()
            ->where(['OR', ['gs_ltr_grade' => 'F'], ['gs_ltr_grade' => 'NG'], ['gs_pass_status' => 0]])
            ->all();
    }

    public static function getInvalidGradeIds(){
        $igrades = self::find()
            ->where([
                'OR', 
                ['gs_ltr_grade' => 'F'], 
                ['gs_ltr_grade' => 'NG'], 
                ['gs_pass_status' => 0]
            ])
            ->all();
        $iGradeIds = [];
        if($igrades){
            $iGradeIds = array_column($igrades,'id');
        }

        return $iGradeIds;
    }

    public function getValidPGLetterGrades()
    {
        $prog = Program::findOne(['program_name' => 'Masters']);
        return $this->find()
            ->where([
                'gs_forProgram' => $prog->id,
                'gs_valid' => 1
            ])
            ->andWhere(['<>', 'gs_gradePoint', 0])
            ->all();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGsForProgram()
    {
        return $this->hasOne(Program::class, ['id' => 'gs_forProgram']);
    }
}
