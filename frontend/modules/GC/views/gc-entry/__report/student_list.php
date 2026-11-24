<?php

if (isset($model)) {
    $gc_students = $model->getGcStudents()->orderBy(['cgpa' => SORT_DESC])->all();
    echo "<table class='table table-bordered table-sm table-hover'>";
    echo "<caption style='color: #000; font-weight:normal;'>
            <h3 style='text-align:center;'>Students In this Entry</h3>
        </caption>";
    echo "<thead>";
    echo "<tr style='background-color: #DAF;'>
            <td>#</td>
            <td>Reg. ID</td>
            <td>Student Name</td>
            <td>SGPA</td>
            <td>CGPA</td>
            <td>Major GPA</td>
            <td>Total CrdtHr.</td>
            <td>Total GPS.</td>
            <td>Courses</td>
            <td>Option</td>
        </tr>";
    echo "</thead><tbody>";
    $count = 0;
    $uapproved_exists = false;
    $approved_exists = false;
    foreach ($gc_students as $gc_std) {
        $option = '';
        if (Yii::$app->user->can('Registrar')) {
            if ($gc_std->reg_approved == 1) {
                $approved_exists = true;
                $option = "<button class='btn btn-xs btn-warning' title='Cancel approval' onclick='changeRegApprovalStatus({$model->id},{$gc_std->id},2);return false'>Cancel</button>";
            } else {
                $uapproved_exists = true;
                $option = "<button class='btn btn-xs btn-primary' title='Approve' onclick='changeRegApprovalStatus({$model->id},{$gc_std->id},1);return false'>Approve</button>";
            }
        } else if (Yii::$app->user->can('Admin')) {
            if ($gc_std->reg_approved == 1) {
                $option = "Approved";
            } else {
                $option = "<button class='btn btn-xs btn-warning' title='Cancel approval' onclick='DeleteFromList({$gc_std->id});return false'>Delete</button>";
            }
        }
        $student = $gc_std->stdRel;
        echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$student->std_regId}</td>
                <td><a href='/student/view?id={$student->id}' target='_blank'>{$student->getFullName()}</a></td>
                <td>{$gc_std->sgpa}</td>
                <td>{$gc_std->cgpa}</td>
                <td>{$gc_std->major_gpa}</td>
                <td>{$gc_std->total_credit_hr}</td>
                <td>{$gc_std->total_gps}</td>
                <td>{$gc_std->course_count}</td>
                <td>{$option}</td>
        </tr>";
    }
    if(Yii::$app->user->can('Registrar')){
        $optionBatch = '';
        if($uapproved_exists){
            $optionBatch ="<button class='btn btn-xs btn-primary'  style='margin: 4px;'onclick='changeApproveStatusBatch({$model->id},1)'>Approve All</button>";
            $optionBatch .= $approved_exists  ? "<button class='btn btn-xs btn-warning' style='margin: 4px;' onclick='changeApproveStatusBatch({$model->id},2)'>Cancel All</button>" : '';
        }else{
            $optionBatch = "<button class='btn btn-xs btn-warning' onclick='changeApproveStatusBatch({$model->id},2)'>Cancel All</button>";
        }
        echo "<tr>
                <td colspan=10 style='padding: 8px; text-align: right;'>{$optionBatch}</td>
        </tr>";
    }
    echo "<tbody></table>";
} else {
    echo "<p class='bg-warning'>Invalid report</p>";
    return;
}
?>


