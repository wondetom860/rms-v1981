<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\MakeupExamReason */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Makeup Exam Reason',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Makeup Exam Reasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="makeup-exam-reason-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
