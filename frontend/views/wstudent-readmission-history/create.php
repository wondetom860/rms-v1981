<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\WStudentReadmissionHistory */

$this->title = Yii::t('decirs', 'Create Wstudent Readmission History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Wstudent Readmission Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-readmission-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
