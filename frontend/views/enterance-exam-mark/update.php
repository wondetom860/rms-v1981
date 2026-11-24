<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMark */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Enterance Exam Mark',
]) . $model->eem_subject;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Enterance Exam Marks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="enterance-exam-mark-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
