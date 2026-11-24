<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Student */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Student',
]) . $model->std_regId;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="student-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'userModel'=>$userModel,
    ]) ?>

</div>
