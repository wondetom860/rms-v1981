<?php 

$table = "";
if ($gs && count($gs) > 0) {
    $table .= "<table class='table table-sm table-striped table-hover table-bordered' style='border:2px solid black;'> ";
    $optBtn ='';
    if (Yii::$app->user->can('Registrar')) {
        $optBtn = $gs[0]->unloackedExists() ? 
            "<button class='btn btn-sm btn-primary pull-right' onclick='changeLockStatus(1)'>Lock</button>":
            "<button class='btn btn-sm btn-warning pull-right' onclick='changeLockStatus(0)'>Unlock</button>";
    }

    $table .= "<caption style='font-weight:normal;'>Grade Scale{$optBtn}</caption>";

    $table .= "<thead style='background-color:#8D0'>
                <th>#</th>
                <th>Program</th>
                <th>Grade</th>
                <th>Min. Point</th>
                <th>Max. Point</th>
                <th>Grade Point</th>
                <th>Remark</th>
                <th>Grade scale status</th>
                <th>Lock status</th>
                <th>Clearable</th>
                <th>Altered</th>
            </thead><tbody>";
            $count = 0;
    foreach ($gs as $key) {
        $optBtn = '';
        $status = $key->gs_locked ? 'Locked':"Not Locked";
        $scale_status = $key->gs_scale_status ? 'Active':"Not Active";
        $clearable = $key->gs_clearable == 1 ? 'Yes':"No";
        $altered = $key->checkAltered() ? 'Fine' : 'Altered';
        $table .= "<tr>";
        $table .= "
                <td>".++$count."</td>
                <td>{$key->gsForProgram->program_name}</td>
                <td>{$key->gs_ltr_grade}</td>
                <td style='text-align:right;'>".number_format($key->gs_min,"2")."</td>
                <td style='text-align:right;'>".number_format($key->gs_max,"2")."</td>
                <td style='text-align:center;'>".number_format($key->gs_gradePoint,"2")."</td>
                <td style='text-align:left;'>{$key->gs_remark}</td>
                <td style='text-align:center;'>{$scale_status}</td>
                <td style='text-align:center;'>{$status}</td>
                <td style='text-align:center;'>{$clearable}</td>
                <td style='text-align:center;' id='wt_'{$key->id}>{$altered}</td>
                ";
        $table .= "</tr>";
    }
    $table .= "</tbody></table>";

    echo $table ;die();
}else{
    echo "<p class = 'bg-danger'>No grade scale configured yet.</p>";die();
}
?>