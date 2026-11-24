<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationScale */

$this->title = Yii::t('rms', 'Create Semester Evaluation Scale');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation Scales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-scale-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
