<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalSetting */

$this->title = Yii::t('rms', 'Update Depal Setting: {name}', [
    'name' => $model->sectionRel->getClassLevel(),
]);
$model->startdate = date("d-m-Y",$model->start_date);
$model->enddate = date("d-m-Y",$model->end_date);

$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->sectionRel->getClassLevel(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="depal-setting-update">

    <h3><?= Html::encode($this->title).$model->sectionRel->getClassLevelHeader()."- {$model->sectionRel->academicYearRel->getAcHeader()}" ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
