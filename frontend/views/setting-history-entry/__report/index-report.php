<?php 

$table = "";
$gsEntry = \backend\models\SettingHistoryEntry::find()->all();

if ($gsEntry && count($gsEntry) > 0) {
    $table .= "<table class='table table-sm table-striped table-hover table-bordered' style='border:2px solid black;'> ";

    $table .= "<caption style='font-weight:normal;'>Grade Scale Setting History</caption>";

    $table .= "<thead style='background-color:#8D0'>
                <th>#</th>
                <th>Setting Valid upto</th>
                <th>Backuped By</th>
                <th>Backuped On</th>
                <th>Option</th>
            </thead><tbody>";
            $count = 0;
    foreach ($gsEntry as $key) {
        $optBtn ='';
	    if (Yii::$app->user->can('Registrar')) {
	        $optBtn = "<button class='btn btn-sm btn-primary pull-right' onclick='RestoreSetting({$key->id})'>Restore</button>";
	    }

	    $bkUpDate = date("d/m/Y",$key->created_at);

        $table .= "<tr>";
        $table .= "
                <td>".++$count."</td>
                <td>{$key->setting_used_upto}</td>
                <td>{$key->createdBy->getFullName()}</td>
                <td>{$bkUpDate}</td>
                <td style='text-align:center;' id='wt_'{$key->id}>{$optBtn}</td>
                ";
        $table .= "</tr>";
    }
    $table .= "</tbody></table>";

    echo $table;
}else{
    echo "<p class = 'bg-danger'>No grade scale configured yet.</p>";die();
}
?>