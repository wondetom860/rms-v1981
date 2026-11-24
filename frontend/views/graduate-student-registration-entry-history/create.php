<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentRegistrationEntryHistory */

$this->title = Yii::t('rms', 'Create Graduate Student Registration Entry History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Student Registration Entry Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-registration-entry-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
