<?php  
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = Yii::t('rms', 'Department Allocation Module (Depal)');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="Depal-default-index">
    <h3><?= $this->title ?> <span class="pull-right bg-info" style="font-style: italic; font-size: 8pt;">V1.0</span> </h3>
    <div class="row">
        <div class="col-md-9" style="font-size: 0.9em;">
            <?= $this->render('../depal-setting/__report/settings') ?>
        </div>
        <div class="col-md-3" style="font-size: 0.87em">
            <?= $this->render('../depal-category/__report/categories') ?>
        </div>
    </div>
</div>
