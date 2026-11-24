<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SemesterRegistrationControld */

$this->title = Yii::t('rms', 'New Semester Registration Control(default)');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration Controlds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-controld-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
