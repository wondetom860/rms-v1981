<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\MakeupExamReason */

$this->title = Yii::t('decirs', 'Makeup Exam Reason');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Makeup Exam Reasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="makeup-exam-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
