<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContinuousAssessment */

$this->title = Yii::t('rms', 'Update Continuous Assessment: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Continuous Assessments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="continuous-assessment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
