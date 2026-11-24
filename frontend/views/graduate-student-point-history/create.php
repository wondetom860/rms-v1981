<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentPointHistory */

$this->title = Yii::t('rms', 'Create Graduate Student Point History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Student Point Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-point-history-create">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
