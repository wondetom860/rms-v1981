<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;

/**
 * This is the model class for table "w_semester_evaluation".
 *
 * @property integer $id
 * @property integer $se_eet_relId
 * @property integer $se_reviewee_relId
 * @property double $se_total_point_given
 * @property string $se_date_started
 * @property integer $se_acadYear_relId
 * @property integer $se_sem_relId
 * @property integer $se_coff2det_relId
 * @property string $data
 *
 * @property WSeEvaluatorsList[] $wSeEvaluatorsLists
 * @property SchoolYear $seAcadYearRel
 * @property CourseOffering2Detail $seCoff2detRel
 * @property WEvaluationEntryTable $seEetRel
 * @property User $seRevieweeRel
 * @property Semester $seSemRel
 * @property WSemesterEvaluationDetail[] $wSemesterEvaluationDetails
 */
class WSemesterEvaluation extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_semester_evaluation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['se_eet_relId', 'se_reviewee_relId', 'se_date_started', 'se_acadYear_relId', 'se_sem_relId', 'data'], 'required'],
            [['se_eet_relId', 'se_reviewee_relId', 'se_acadYear_relId', 'se_sem_relId', 'se_coff2det_relId'], 'integer'],
            [['se_total_point_given'], 'number'],
            [['se_date_started'], 'safe'],
            [['data'], 'string'],
            [['se_acadYear_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\SchoolYear::className(), 'targetAttribute' => ['se_acadYear_relId' => 'id']],
            [['se_coff2det_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\CourseOffering2Detail::className(), 'targetAttribute' => ['se_coff2det_relId' => 'id']],
            [['se_eet_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WEvaluationEntryTable::className(), 'targetAttribute' => ['se_eet_relId' => 'id']],
            [['se_reviewee_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \common\models\User::className(), 'targetAttribute' => ['se_reviewee_relId' => 'id']],
            [['se_sem_relId'], 'exist', 'skipOnError' => true, 'targetClass' => \backend\models\Semester::className(), 'targetAttribute' => ['se_sem_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'se_eet_relId' => Yii::t('decris', 'Se Eet Rel ID'),
            'se_reviewee_relId' => Yii::t('decris', 'Se Reviewee Rel ID'),
            'se_total_point_given' => Yii::t('decris', 'Se Total Point Given'),
            'se_date_started' => Yii::t('decris', 'Se Date Started'),
            'se_acadYear_relId' => Yii::t('decris', 'Se Acad Year Rel ID'),
            'se_sem_relId' => Yii::t('decris', 'Se Sem Rel ID'),
            'se_coff2det_relId' => Yii::t('decris', 'Se Coff2det Rel ID'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    public function getData($modelObj){
        $mainData = (object)[
            'se_eet_relId'=>intval($modelObj->se_eet_relId),
            'se_reviewee_relId'=>intval($modelObj->se_reviewee_relId),
            'se_sem_relId'=>intval($modelObj->se_sem_relId),
            'se_acadYear_relId'=>intval($modelObj->se_acadYear_relId),
            'se_coff2det_relId'=>intval($modelObj->se_coff2det_relId),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }

    public function evaluated($stdObj,$coff2detId,$eetObj,$revId){
        // $stdObj,$inst[1],$EPETkey->epetEetRel,$inst[0]->id
        $coff2detObj = \backend\models\CourseOffering2Detail::findOne($coff2detId);
        $rec = $this->find()
            ->innerJoin('w_se_evaluators_list','w_se_evaluators_list.sel_se_relId = w_semester_evaluation.id')
            ->where([
                'se_reviewee_relId'=>$revId,
                'se_coff2det_relId'=>$coff2detId,
                'se_eet_relId'=>$eetObj->id,
            ])
            ->andWhere([
                'sel_evaluator_relId'=>$stdObj->stdUserRel->id,
            ])
            ->all();
            
        return ($rec && count($rec) > 0);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWSeEvaluatorsLists()
    {
        return $this->hasMany(WSeEvaluatorsList::className(), ['sel_se_relId' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeAcadYearRel()
    {
        return $this->hasOne(\backend\models\SchoolYear::className(), ['id' => 'se_acadYear_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeCoff2detRel()
    {
        return $this->hasOne(\backend\models\CourseOffering2Detail::className(), ['id' => 'se_coff2det_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeEetRel()
    {
        return $this->hasOne(WEvaluationEntryTable::className(), ['id' => 'se_eet_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeRevieweeRel()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'se_reviewee_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSeSemRel()
    {
        return $this->hasOne(\backend\models\Semester::className(), ['id' => 'se_sem_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getWSemesterEvaluationDetails()
    {
        return $this->hasMany(WSemesterEvaluationDetail::className(), ['sed_se_relId' => 'id']);
    }
}
