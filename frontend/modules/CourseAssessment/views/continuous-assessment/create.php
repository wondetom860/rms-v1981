<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContinuousAssessment */

$this->title = Yii::t('rms', 'Create Continuous Assessment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Continuous Assessments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="continuous-assessment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
