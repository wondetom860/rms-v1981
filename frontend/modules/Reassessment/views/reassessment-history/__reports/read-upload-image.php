<?php

use yii\helpers\Html;
// how to identify if the file is pdf or image
if ($model->ras_scan_file_ext == 'pdf') {
    echo '<object data="data:application/pdf;base64,' . base64_encode($model->ras_report_scan) . '" type="application/pdf" style="height:88%;width:99%"></object>';
} else {
    echo '<center><img width="98%" height=auto src="data:image/' . $model->ras_scan_file_ext . ';base64,' . base64_encode($model->ras_report_scan) . '"/></enter>';
}
