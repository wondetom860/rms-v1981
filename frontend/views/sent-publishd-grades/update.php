<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\SentPublishdGrades */

$this->title = Yii::t('rms', 'Update EMail Delivery Address: {name}', [
    'name' => $model->semregRel->semregStdRel->getFullName() . " - " . $model->semregRel->semregAcadYearRel->sch_year_desc . "-" . $model->semregRel->semregSemesterRel->semester_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Published Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->semregRel->semregStdRel->std_regId, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="sent-publishd-grades-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
