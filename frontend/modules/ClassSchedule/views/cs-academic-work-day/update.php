<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAcademicWorkDay */

$this->title = Yii::t('rms', 'Update Academic Work Day: {name}', [
    'name' => $model->day_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Work Days'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->day_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cs-academic-work-day-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
