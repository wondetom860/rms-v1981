<?php 

$this->title = Yii::t('rms', 'Academic Staff Performance Evaluation Module (AcStEva)');
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="AcStEva-default-index">
    <h3><?= $this->title ?> <span class="pull-right bg-info" style="font-style: italic; font-size: 8pt;">V2.0</span> </h3>
    <div class="row">
        <div class="col-md-10" style="font-size: 0.9em; max-height: 756px; overflow: auto; padding: 5px;">
            <?= $this->render('../ase-department-evaluation-schedule/__report/get-active-plans') ?>
        </div>
        <div class="col-md-2" style="font-size: 0.87em">
            <?php //$this->render('../ase-evaluation-result/__report/get-evaluation-summery') ?>
        </div>
    </div>
</div>
