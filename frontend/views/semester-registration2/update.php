<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2 */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Registration2',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="semester-registration2-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
