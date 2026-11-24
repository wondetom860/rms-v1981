<?php

$this->title = Yii::t('rms', 'Graduate Class Students');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="ExitExam-default-index">
    <h3>Graduate Class Students <small>Lists Graduate Students</small><span class="bg-info pull-right float-right">V1.0</span></h3>
</div>

<div class="row">
    <div class="col-md-12 table-responsive">
        <?= $this->render('../gc-entry/__report/gc-entry-summery') ?>
    </div>
</div>
