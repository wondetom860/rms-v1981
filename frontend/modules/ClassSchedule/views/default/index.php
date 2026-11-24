<?php 

$this->title = "Class Time Scheduling Module";

// context->action->uniqueId

$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ClassSchedule-default-index">
    <h3><?= $this->title ?></h3>
    <p class="pull-right">
        <code>V-1.0</code>
    </p>
    <div class="row" style="background-color: #FFF;">
        <div class="col-md-12">
            
        </div>
        <div class="col-md-12">
            <?= $this->render('../cs-class-time-distribution/__report/index_report') ?>
        </div>
    </div>
</div>
