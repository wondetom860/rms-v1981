<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration3 */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Registration3',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration3s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="semester-registration3-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
