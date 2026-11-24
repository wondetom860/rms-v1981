<?php

if (isset($response) && is_array($response)) {
    echo "<table class='table table-bordered table-striped table-condensed table-hover table-sm'>";

    echo "<thead>
            <tr>
            <th>#</th>
            <th>RegID</th>
            <th>Student Name</th>
            <th>Recipient Email</th>
            <th>Relation</th>
            <th>Option</th>
        </tr>
    </thead><tbody>";
    $count = 0;
    foreach ($response as $row) {
        $ecInfo = $row->semregStdRel->emergencyContactInfo;
        $option = '';
        $email = '';
        $relation = '';

        if (!empty($ecInfo->ec_email)) {
            $relation = $ecInfo->ec_relationShip;
            $email = $ecInfo->ec_email;
            $option = "<input type='checkbox' name='selected_students[]' onchange='addToList({$row->id});return false;' value='" . $row->id . "'>";
        } else {
            $relation = '-';
            $email = '-';
        }
        echo "<tr>";
        echo "<td>" . ++$count . "</td>";
        echo "<td>" . $row->semregStdRel->std_regId . "</td>";
        echo "<td>" . $row->semregStdRel->getFullName() . "</td>";
        echo "<td>" . $email . "</td>";
        echo "<td>" . $relation . "</td>";
        echo "<td>" . $option . "</td>";
        echo "</tr>";
    }
    echo "<tr>
        <td colspan=6 style='text-align:center'>
            <hr>
            <button class='btn btn-success pull-right float-right' onclick='importStudentRecords()'>Import Selected Students</button></td>
    </tr>";
    echo "</tbody></table>";
} else {
    echo "<div class='alert alert-info'>No response received.</div>";
}

// var_dump($response);
