<?php

$this->title = "Request for Course Reassessment ";
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="Reassessment-default-index">
    <h3>
        <?= $this->title . "(Appeal at the level of Department)" ?>
        <span class="badge label-success pull-right">V2.0</span>
    </h3>
    <div class="row">
        <div class="col-md-12" style="font-size: 9pt;">
            <?= $this->render('../reassessment-history/__reports/reas_request_summery') ?>
        </div>
    </div>
</div>