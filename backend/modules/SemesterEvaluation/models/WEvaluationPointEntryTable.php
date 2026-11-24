<?php

namespace backend\modules\SemesterEvaluation\models;

use Yii;
use \frontend\models\CourseOffering2Detail;

/**
 * This is the model class for table "w_evaluation_point_entry_table".
 *
 * @property integer $id
 * @property integer $epet_eet_relId
 * @property integer $epet_ep_relId
 * @property double $epet_max_point
 * @property string $data
 *
 * @property WEvaluationEntryTable $epetEetRel
 * @property WEvaluationPoints $epetEpRel
 */
class WEvaluationPointEntryTable extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'w_evaluation_point_entry_table';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['epet_eet_relId', 'epet_ep_relId', 'data'], 'required'],
            [['epet_eet_relId', 'epet_ep_relId'], 'integer'],
            [['epet_max_point'], 'number'],
            [['data'], 'string'],
            [['epet_eet_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WEvaluationEntryTable::className(), 'targetAttribute' => ['epet_eet_relId' => 'id']],
            [['epet_ep_relId'], 'exist', 'skipOnError' => true, 'targetClass' => WEvaluationPoints::className(), 'targetAttribute' => ['epet_ep_relId' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('decris', 'ID'),
            'epet_eet_relId' => Yii::t('decris', 'Evaluation Entry'),
            'epet_ep_relId' => Yii::t('decris', 'Evaluation Key (Point)'),
            'epet_max_point' => Yii::t('decris', 'Max. Point'),
            'data' => Yii::t('decris', 'Data'),
        ];
    }

    public function getRevieweeModelInst($srd2Obj,$studentObj = NULL){//
        $studentObj = is_null($studentObj) ? $srd2Obj->srd2SemregRel->semregStdRel : $studentObj;

        $coff2Det = CourseOffering2Detail::findOne([
                    'coff2det_coff2_relId' => $srd2Obj->srd2_coff_relId,
                    'coff2det_course_relId' => $srd2Obj->srd2_course_relId,
        ]);

        $instName = 'TBA';
        $course = $srd2Obj->srd2CourseRel;

        if ($coff2Det && count($coff2Det) > 0) {
            // check for INST
            if (!is_null($coff2Det->coff2det_inst_relId)) {
                return [$coff2Det->coff2detInstRel->instUserRel,$coff2Det->id];
            }
        }
        return [$instName,NULL];
    }

    public function getRevieweeModelTA($srd2Obj,$studentObj = NULL){
        $studentObj = is_null($studentObj) ? $srd2Obj->srd2SemregRel->semregStdRel : $studentObj;

        $coff2Det = CourseOffering2Detail::findOne([
                    'coff2det_coff2_relId' => $srd2Obj->srd2_coff_relId,
                    'coff2det_course_relId' => $srd2Obj->srd2_course_relId,
        ]);

        $instName = 'TBA';
        $course = $srd2Obj->srd2CourseRel;

        if ($coff2Det && count($coff2Det) > 0) {
            // check for INST
            if (!is_null($coff2Det->coff2det_TA_relId)) {
                return [$coff2Det->coff2detTARel->instUserRel,$coff2Det->id];
            }
        }
        return [$instName,NULL];
    }

    public function listEETs(){
        $et = WEvaluationEntryTable::find()->all();

        $retObj = [];
        if ($et && count($et) > 0) {
            foreach ($et as $ETkey) {
                $retObj[] = [
                    'id'=>$ETkey->id,
                    'name_et'=>$ETkey->eetEtRel->et_name.' [ '.$ETkey->eet_evaluator_role.' => '.$ETkey->eet_reviewee_role.' ] ',
                ];
            }
        }

        return $retObj;
    }

    public function getData($modelObj){
        $mainData = (object)[
            'epet_eet_relId'=>intval($modelObj->epet_eet_relId),
            'epet_ep_relId'=>intval($modelObj->epet_ep_relId),
            'epet_max_point'=>doubleval($modelObj->epet_max_point),
        ];
        return md5(serialize($mainData));
    }

    public function checkAltered($modelObj){
        return $this->getData($modelObj) == $modelObj->data;
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpetEetRel()
    {
        return $this->hasOne(WEvaluationEntryTable::className(), ['id' => 'epet_eet_relId']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEpetEpRel()
    {
        return $this->hasOne(WEvaluationPoints::className(), ['id' => 'epet_ep_relId']);
    }
}
