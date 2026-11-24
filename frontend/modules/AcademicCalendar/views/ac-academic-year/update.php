<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcAcademicYear */

$this->title = Yii::t('rms', 'Update Academic Year: {name}', [
    'name' => $model->school_year."-".$model->semester,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Years'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->school_year."-".$model->semester, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ac-academic-year-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
