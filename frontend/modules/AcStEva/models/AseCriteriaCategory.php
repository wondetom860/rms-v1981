<?php

namespace frontend\modules\AcStEva\models;

use Yii;
use backend\models\User;

/**
 * This is the model class for table "ase_criteria_category".
 *
 * @property int $id
 * @property string $category_name
 * @property string $profess_list
 * @property int $created_by
 * @property int $created_at
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property User $createdAt
 * @property User $updatedBy
 * @property AseCriteriaDescription[] $aseCriteriaDescriptions
 */
class AseCriteriaCategory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ase_criteria_category';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_name', 'created_by', 'created_at', 'data','profess_list'], 'required'],
            [['created_by', 'created_at', 'updated_at', 'updated_by'], 'integer'],
            [['data','profess_list'], 'string'],
            [['category_name'], 'string', 'max' => 50],
            [['category_name'], 'unique'],
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
            'category_name' => Yii::t('rms', 'Category Name'),
            'created_by' => Yii::t('rms', 'Created By'),
            'created_at' => Yii::t('rms', 'Created At'),
            'updated_at' => Yii::t('rms', 'Updated At'),
            'updated_by' => Yii::t('rms', 'Updated By'),
            'profess_list' => Yii::t('rms', 'Profession Types'),
            'data' => Yii::t('rms', 'Data'),
        ];
    }

    public function getData(){
        $this->data = md5(serialize((object)[
            'category_name'=>$this->category_name,
            'profess_list'=>$this->profess_list,
            'created_by'=>intval($this->created_by),
            'updated_by'=>intval($this->updated_by),
        ]));
    }

    public function getAverageTotal($facultyModel,$acp,$evalType){
        $total = 0;
        $count = 0;
        foreach ($this->aseCriteriaDescriptions as $criteria) {
            $evaluationResultDetails = $criteria->getAseEvaluationResultDetails()
                ->innerJoin('ase_evaluation_result', 'ase_evaluation_result.id = ase_evaluation_result_detail.evaluation_result_relId')
                ->innerJoin('ase_department_evaluation_schedule', 'ase_department_evaluation_schedule.id = ase_evaluation_result.dept_plan_relId')
                ->innerJoin('ase_semester_evaluation', 'ase_semester_evaluation.id = ase_department_evaluation_schedule.header_relId')
                ->andWhere([
                    'evaluee_relId' => $facultyModel->instRel->inst_user_relId,
                    'academic_year_relId' => $acp->academic_year_relId,
                    'criteria_relId' => $criteria->id,
                ])
                ->all();
            if ($evaluationResultDetails) {
                $total += $evaluationResultDetails[0]->average;
                $count++;
            }
        }
        return $count > 0 ? sprintf('%0.2f', ($total / $count)) : 0;
    }

    public function getCategoryCode(){
        $ll = "";
        if ($this->category_name) {
            foreach (explode(' ', $this->category_name) as $word) {
                $ll .= strtoupper($word[0]);
            }
        }

        return $ll;
    }

    /**
     * Gets query for [[CreatedAt]].
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
     * Gets query for [[AseCriteriaDescriptions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAseCriteriaDescriptions()
    {
        return $this->hasMany(AseCriteriaDescription::class, ['category_relId' => 'id']);
    }
}
