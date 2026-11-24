<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentRegistrationEntryHistory */

$this->title = Yii::t('rms', 'Update Graduate Student Registration Entry History: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Student Registration Entry Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="graduate-student-registration-entry-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
