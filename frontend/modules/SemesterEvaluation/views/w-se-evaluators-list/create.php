<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSeEvaluatorsList */

$this->title = Yii::t('decris', 'Create Wse Evaluators List');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wse Evaluators Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wse-evaluators-list-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
