<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2Detail */

$this->title = Yii::t('rms', 'Create Semester Registration2 Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
