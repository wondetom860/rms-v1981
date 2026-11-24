<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcActivePrograms */

$this->title = Yii::t('rms', 'Update Active Program: {name}', [
    'name' => $model->academicYearRel->getAcHeader(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Active Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->academicYearRel->getAcHeader(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ac-active-programs-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
        'records' => $records
    ]) ?>

</div>
