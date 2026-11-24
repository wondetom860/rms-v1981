<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseSemesterEvaluation */

$this->title = Yii::t('rms', 'Update Ase Semester Evaluation: {name}', [
    'name' => $model->evaluation_header,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->evaluation_header, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');

$model->startdate = date("d-m-Y",$model->start_date);
$model->enddate = date("d-m-Y",$model->end_date);
?>
<div class="ase-semester-evaluation-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
