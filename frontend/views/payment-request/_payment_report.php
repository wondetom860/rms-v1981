<?php
$wreport = "";
if (isset($report) && count($report) > 0) {
    $wreport .= "<table id='report_table' class='table table-hover table-condensed table-xs table-bordered ' style='font-size:10pt;'>";
    $wreport .= "<caption><p class='bg-success'> <i class='bg-danger'>" . count($report) . "</i> Request(s).</p></caption>";
    $wreport .= "<thead style='background-color:#00c0ef;'>
        <th>#</th>
        <th>RegId</th>
        <th>Full Name</th>
        <th>Pymnt Reason</th>
        <th>Sem.</th>
        <th>Acad.Year</th>
        <th>Course</th>
        <th>Txn #</th>
        <th>Req. Status (R)</th>
        <th>Req. Status (C)</th>
        ";
    if (Yii::$app->user->can('CEP Clerk') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
        $wreport .= "<th>Option</th>";
    }
    $wreport .= "</thead><tbody>";
    $c = 0;
    foreach ($report as $reports) {
        if (!Yii::$app->user->can('Student')) {
            // $student = Student::findOne(['id'=>$reports['ssph_std_relId']]);
            $student = $reports->ssphStdRel;
        }
        // $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
        $pp = $reports->ssphPymntRsnRel;
        $upp = is_null($pp) ? '-' : $pp->pr_desc;
        $reqStatusReg = is_null($reports['ssph_registrarApproved']) ? 'Pending' : ($reports['ssph_registrarApproved'] == 1 ? 'Approved' : 'Rejected');
        $reqStatus = is_null($reports['ssph_approved']) ? 'Pending' : ($reports['ssph_approved'] == 1 ? 'Approved' : 'Rejected');
        // $ccc = is_null($reports['ssph_course_relId'])?'N/A':\backend\models\Course::findOne(['id'=>$reports['ssph_course_relId']])->course_title;
        $ccc = $reports->ssphCourseRel ? $reports->ssphCourseRel->course_title : 'N/A';

        $wreport .= "<tr class='tr'>
            <td>" . ++$c . "</td>
            <td>" . $student->std_regId . "</td>
            <td>{$student->getFullName()}</td>
            <td>" . $upp . "</td>
            <td>{$reports->ssphSemRel->semester_name}</td>
            <td>{$reports->ssphAcadYearRel->sch_year_desc}</td>
            <td>" . $ccc . "</td>
            <td>" . $reports['ssph_txn_num'] . "</td>
            <td>" . $reqStatusReg . "</td>
            <td>" . $reqStatus . "</td>";
        if (Yii::$app->user->can('CEP Clerk') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {

            if (Yii::$app->user->can('Registrar') && $reports->ssph_approved == 1) {
                $wreport .= "<td>
                    <select title='select action' onchange='changeStatus(" . $reports['id'] . ",$(this).val())'>
                        <option value=' '> </option>
                        <option value='1'>Approve</option>
                        <option value='0'>Reject</option>
                    </select>
                </td>";
            } else if (Yii::$app->user->can('CEP Clerk') && $reports->ssph_registrarApproved != 1) {
                $wreport .= "<td>
                    <select title='select action' onchange='changeStatus(" . $reports['id'] . ",$(this).val())'>
                        <option value=' '> </option>
                        <option value='1'>Approve</option>
                        <option value='0'>Reject</option>
                    </select>
                </td>";
            } else if (Yii::$app->user->can('Admin') && $reports->ssph_registrarApproved == NULL && $reports->ssph_approved == NULL) {
                // allow admin to delete a request if it is not processed by both CEP and Registrar
                $btnDelete = "<button class='btn btn-xs btn-danger' title='Delete this payment request' onclick='deletePaymentrequest({$reports->id})' >Delete</button>";
                $wreport .= "<td>{$btnDelete}</td>";
            } else {
                $wreport .= "<td> - </td>";
            }
        }
        $wreport .= "</tr>";
    }
    $wreport .= "</tbody></table>";
    echo $wreport;
}
