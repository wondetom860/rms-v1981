<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration */

$this->title = Yii::t('rms', 'Create Semester Registration');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registrations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
