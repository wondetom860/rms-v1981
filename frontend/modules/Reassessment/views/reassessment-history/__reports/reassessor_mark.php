<?php

/**
 * 
 * @var frontend\modules\Reassessment\models\ReassessmentHistory $model
 */

use backend\models\GradeScale;
use frontend\modules\Reassessment\models\ReassessorMark;

// $model = reassessmentHistoryModel
if (isset($model)) {
    if ($model->ras_contAssCatag_relId) {
        $contAssCatagModel = $model->rasContAssCatagRel;
    } else {
        $assessments = $model->rasSrd2Rel->getContinuousAssessmentEntries();

        $contAssCatagModel = $assessments ? $assessments[0]->caCatagorySetupRel : NULL;

        if ($contAssCatagModel) {
            $model->ras_contAssCatag_relId = $contAssCatagModel->id;
            // $model->getData();
            $model->save();
            $contAssCatagModel = $model->rasContAssCatagRel;
        }
    }

    if ($contAssCatagModel && $model->ras_registrarApprove_status !== NULL) {
        // $assessments = $contAssCatagModel->getContinuousAssessment2s()
        //     ->where([
        //         'ca_std_relid' => $model->ras_std_relId,
        //         'ca_semreg_relId' => $model->rasSrd2Rel->srd2_semreg_relId
        //     ])
        //     ->all();
        $submitButton = '';
        if (count($model->reassessmentCommittees) == 3) {
            if (Yii::$app->user->can('DepartmentHead') && !isset($print) && !$model->rasSrd2Rel->srd2_gradeLocked) {
                $submitButton = $model->oneApproved() ? '' : "<button class='btn btn-xs btn-warning pull-right' onclick='submitRaMark({$model->id});return false;'>Submit Mark</button>";
            }

            if ($model->rasSrd2Rel->srd2_gradeLocked && (!$model->rasSrd2Rel->srd2_gradeApproved || $model->ras_registrarApprove_status)) {
                $model->rasSrd2Rel->srd2_gradeLocked = 0;
                $model->rasSrd2Rel->save(false);
                $submitButton = $model->oneApproved() ? '' : "<button class='btn btn-xs btn-warning pull-right' onclick='submitRaMark({$model->id});return false;'>Submit Mark</button>";
            }
        }
        $assessments = $model->rasSrd2Rel->getContinuousAssessmentEntries();
        $reassessorMarkModel = new ReassessorMark();
        if ($assessments) {
            echo "<table class='table table-condensed table-sm table-bordered' style='font-size:9pt;'>";
            echo "<thead style='background-color: #8D0'>
                    <th>#</th>
                    <th>Assessment Instance<b class='pull-right'>(Weight%)</b></th>
                    <th>Original</th>
                    <th>Changed To{$submitButton}</th>
                    <th>Change</th>
                </thead><tbody>";
            $cc =  0;
            $total_before = 0;
            $total_after = null;
            foreach ($assessments as $contAs) {
                $ima_name = $contAs->caMarkAssignmentRel->ima_instanceName;
                $ima_max_point = sprintf("%01.2f", $contAs->caMarkAssignmentRel->ima_possiblePoint);
                $rm_exists = ReassessorMark::find()
                    ->where([
                        'ras_ima_relId' => $contAs->ca_markAssignment_relId,
                        'ras_relId' => $model->id,
                    ])
                    ->one();
                $rmark = null;
                if (!$rm_exists) {
                    $reassessorMarkModel->createENtry($contAs, $model->id);
                } else {
                    $rmark = $rm_exists;
                }
                $total_before += $rmark ? $rmark->ras_original_mark : 0;
                $rmark_afterMark = '';
                $cahnge = '-';
                // $acieved =  sprintf("%01.2f", $contAs->ca_markObtained);
                $original_mark = $acieved = sprintf('%0.2f', $rmark ? $rmark->ras_original_mark : 0);
                if ($rmark && $rmark->ras_changed_to) {
                    $total_after += $rmark->ras_changed_to;
                    $rmark_afterMark = $rmark->ras_changed_to ? sprintf("%0.2f", $rmark->ras_changed_to) : null;
                    $cahnge = ($rmark_afterMark && ($rmark_afterMark > $acieved)) ? "<span class='badge label-success'>Added</span>" : (($rmark_afterMark && ($rmark_afterMark == $acieved)) ? "<span class='badge label-primary'>Unchanged</span>" : "<span class='badge label-warning'>Deducted</span>");
                }
                echo "<tr>
                        <td>" . ++$cc . "</td>
                        <td>{$ima_name}<b class='pull-right'>({$ima_max_point}%)</b></td>
                        <td style='text-align: right;'>{$original_mark}</td>
                        <td style='text-align: right;'>{$rmark_afterMark}</td>
                        <td>{$cahnge}</td>
                    </tr>";
                // echo "<p class='bg-secondary'>{$ima_name} : <span class='badge label-success'>{$acieved}/{$ima_max_point}</span></p>";
            }
            $srd2Model = $model->rasSrd2Rel;
            $total_change = $total_after ? ($total_after > $total_before ? 'Added' : ($total_after == $total_before ? 'Unchanged' : 'Deducted')) : ('');
            $grade_after = $total_after ? GradeScale::getLetterGradeFromTotalMark($model->rasSrd2Rel, $total_after, $model->ras_group_relId) : '';
            if ($total_after && !$srd2Model->srd2_gradeLocked && $model->allApproved()) {
                // update course assessmenet entries
                if ($srd2Model->srd2_total_mark != $total_after) {
                    $model->updateContinousAssessment();
                    $srd2Model->srd2_total_mark = $total_after;
                    $srd2Model->srd2_grade_relId = $grade_after->id;
                    $srd2Model->srd2_gradeApproved = NULL;
                    $srd2Model->srd2_requested_reassessment = 1;
                    $srd2Model->update(false);
                    $srd2Model->refresh();
                }
                // var_dump((is_null($model->ras_aft_grade_relId)) || ($grade_after->id != $model->ras_aft_grade_relId) || ($model->ras_afterMark != $total_after));exit;
                if ((is_null($model->ras_aft_grade_relId)) || ($grade_after->id != $model->ras_aft_grade_relId) || ($model->ras_afterMark != $total_after)) {
                    $grade_after = $srd2Model->srd2GradeRel ?? $grade_after;
                    $model->ras_afterMark = $total_after;
                    $model->ras_prevMark = $total_before;
                    $model->ras_aft_grade_relId = is_object($grade_after) ? $grade_after->id : null;
                    $model->save(false);

                    // var_dump($model->ras_aft_grade_relId);exit;
                }
            }
            $grade_after = is_object($grade_after) ? $grade_after->gs_ltr_grade : '';
            // $grade_change = $total_change;
            // $grade_before = $model->rasSrd2Rel->srd2GradeRel ? $model->rasSrd2Rel->srd2GradeRel->gs_ltr_grade : '-';
            $grade_before = $total_before ? GradeScale::getLetterGradeFromTotalMark($model->rasSrd2Rel, $total_before, $model->ras_group_relId) : '';
            $grade_before = is_object($grade_before) ? $grade_before->gs_ltr_grade : '';
            echo "<tr style='font-weight:bold;'>
                    <td colspan=2 style='text-align:center;'>Total</td>
                    <td style='text-align: right'>{$total_before}</td>
                    <td style='text-align: right'>{$total_after}</td>
                    <td>{$total_change}</td>
                </tr>";
            echo "<tr style='font-weight:bold;'>
                <td colspan=2 style='text-align:center;'>Letter Grade</td>
                <td style='text-align: center;'>{$grade_before}</td>
                <td style='text-align: center;' colspan=2>{$grade_after}</td>
            </tr>";
            echo "<tbody></table>";
        } else {
            echo "<p class='bg-warning'>Marksheet detail not found.</p>";
        }
    } else {
        echo "<p class='bg-warning'>Waiting for Registrar approval, or there is a problem with continuous asssessment.</p>";
    }
} else {
    echo "<p class='bg-warning'>Invalid request.</p>";
}

// continuous