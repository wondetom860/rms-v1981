echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($stds) ."</i> Students.</p></caption>";
    echo "<thead>
    <th>RegId</th>
    <th>Full Name</th>
    <th>Group</th>
    </thead><tbody>";
foreach ($stds as $std) {
    $student = \backend\models\Student::findOne(['id'=>$std->gas_std_relId]);
    $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
    echo "<tr>
    <td>". $std->std_regId ."</td>
    <td>". $usr->first_name.' '. $usr->middle_name ."</td>
    <td>". \backend\models\WGroup::findOne(['id'=>$grpId])->group_name ."</td>
    </tr>";
}
echo "</tbody></table>";




<!-- Inclide JS inline code to your code -->
<?php
$this->registerJs(
    "
    // Your JS codes here..
    


    ",
    $this::POS_READY
);
?>