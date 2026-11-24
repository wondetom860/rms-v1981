<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMark */

$this->title = Yii::t('rms', 'Enter New Enterance Exam Result');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Enterance Exam Marks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enterance-exam-mark-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
