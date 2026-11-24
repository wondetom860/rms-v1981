<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationResult */

$this->title = Yii::t('decirs', 'Create Semester Evaluation Result');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-result-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
