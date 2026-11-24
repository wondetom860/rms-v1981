<?php

$reqs = frontend\modules\Reassessment\models\ReassessmentHistory::find()
    // ->where([])
    ->orderBy([
        'ras_acadYear_relId' => SORT_DESC,
        'ras_sem_relId' => SORT_DESC,
        'ras_status' => SORT_DESC
    ])
    ->all();
$reqsAll = frontend\modules\Reassessment\models\ReassessmentHistory::find()->all();

$reqsAll_count = count($reqsAll);
$reqsApproved = array_filter($reqs, function ($req) {
    return $req->ras_registrarApprove_status == 1;
});

$un_reqsApproved = array_filter($reqs, function ($req) {
    return $req->ras_registrarApprove_status == NULL;
});
$approved_requests_count = count($reqsApproved);
$un_approved_requests_count = $reqsAll_count - $approved_requests_count;

$un_approved_requests_table = table($un_reqsApproved);

$processed_reqs = array_filter($reqs, function ($req) {
    return ($req->ras_status == 1) && ($req->ras_afterMark != NULL) && ($req->ras_prevMark != NULL);
});
$added_marks = array_filter($processed_reqs, function ($req) {
    return $req->ras_afterMark > $req->ras_prevMark;
});

$deducted_marks = array_filter($processed_reqs, function ($req) {
    return $req->ras_afterMark < $req->ras_prevMark;
});

$unchanged_marks = array_filter($processed_reqs, function ($req) {
    return $req->ras_afterMark = $req->ras_prevMark && ($req->ras_afterMark != NULL || $req->ras_prevMark != NULL);
});

$unchanged_marks_count = count($unchanged_marks);
$added_marks_count = count($added_marks);
$deducted_marks_count = count($deducted_marks);

// $unchanged_marks_table = table($unchanged_marks);

if ($reqs) {
    echo "
    
        <div class='row'>
            <div class='col-md-3'>
                <div class='panel panel-success'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>All Requets</h3>
                    </div>
                    <div class='panel-body'>
                        <h2 style='font-weight: bold; text-align: right; padding-right: 5px;'>".$reqsAll_count."</h2>
                    </div>                
                </div>
                <div class='panel panel-primary'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>Approved Requets</h3>
                    </div>
                    <div class='panel-body'>
                        <h2 style='font-weight: bold; text-align: right; padding-right: 5px;'>".$approved_requests_count."</h2>
                    </div>                
                </div>
            </div>
            <div class='col-md-9' style='min-height: 350px; overflow-x: auto;'>
                <div class='panel panel-danger'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>Unapproved Requets<span class='pull-right'>{$un_approved_requests_count}</span></h3>
                    </div>
                    <div class='panel-body table-responsive' style='font-size: 9pt; min-height: 250px; overflow-x: auto; max-height: 250px;'>
                        ".$un_approved_requests_table."
                    </div>                
                </div>
            </div>
        </div>

        <div class='row'>
            <div class='col-md-4'>
                <div class='panel panel-success'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>All Processed</h3>
                    </div>
                    <div class='panel-body'>
                        <h2 style='font-weight: bold; text-align: right; padding-right: 5px;'>".(count($processed_reqs))."</h2>
                    </div>                
                </div>
            </div>
            <div class='col-md-4'>
                <div class='panel panel-info'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>Mark-Added Corrections</h3>
                    </div>
                    <div class='panel-body'>
                        <h2 style='font-weight: bold; text-align: right; padding-right: 5px;'>{$added_marks_count}</h2>
                    </div>                
                </div>
            </div>
            <div class='col-md-4'>
                <div class='panel panel-warning'>
                    <div class='panel-heading'>
                        <h3 class='panel-title'>Mark-Deducted Corrections</h3>
                    </div>
                    <div class='panel-body'>
                        <h2 style='font-weight: bold; text-align: right; padding-right: 5px;'>{$deducted_marks_count}</h2>
                    </div>                
                </div>
            </div>
        </div>
    
    ";
    
}else{
    echo "<div class='alert alert-info'>No Reassessment Requests Found.</div>";
}

function table($unchanged_marks){
    if($unchanged_marks){
        $rep =  "<table class='table table-condensed table-sm table-bordered table-hover'>";
        $rep .= "<thead style='background-color: #66F;'>
                    <th>#</th>
                    <th>Reg. ID</th>
                    <th>Academic Year</th>
                    <th>Course Infomation</th>
                    <th>Before Grade</th>
                    <th>After Grade</th>
                    <th>Status</th>
                    <th>Options</th>
                </thead><tbody>";
        $count = 0;
        foreach ($unchanged_marks as $index => $req) {
            $ag = $req->ras_aft_grade_relId ? $req->rasAftGradeRel->gs_ltr_grade : '-';
            $status = $req->ras_registrarApprove_status == 1 ? "Approved" : 'Not Approved';
            $rep .= "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$req->rasStdRel->getFullName()}</td>
                        <td>{$req->rasAcadYearRel->sch_year_desc}-{$req->rasSemRel->semester_name}</td>
                        <td>{$req->rasCourseRel->getCourseCredits()}</td>
                        <td>{$req->rasBefGradeRel->gs_ltr_grade}</td>
                        <td>{$ag}</td>
                        <td>{$status}</td>
                        <td><a class='btn-link pull-right' href='/Reassessment/reassessment-history/view?id={$req->id}'>More</a></td>
                    </tr>";
        }
        $rep .= "</tbody></table>";
        return $rep;
    }else{
        echo "No record";
    }
}
