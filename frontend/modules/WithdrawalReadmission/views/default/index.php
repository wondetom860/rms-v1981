<?php 

$this->title = Yii::t('rms', 'Student Withdrawal - Readmission Module: ');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="AcStEva-default-index">
    <h3><?= $this->title ?> <span class="pull-right bg-info" style="font-style: italic; font-size: 8pt;">V1.1</span> </h3>
    <div class="row">
        <div class="col-md-7" style="font-size: 0.9em;">
            <?= $this->render('../w-student-withdrawal-history/__report/wd_an_report') ?>
        </div>
        <div class="col-md-5" style="font-size: 0.87em">
            <?php //$this->render('../depal-category/__report/categories') ?>
        </div>
    </div>
</div>
