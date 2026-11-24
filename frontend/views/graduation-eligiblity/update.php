<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblity */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Graduation Eligiblity',
]) . $model->geAdmRel->admission_name .''. $model->geProgRel->program_name ;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduation Eligiblities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="graduation-eligiblity-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
