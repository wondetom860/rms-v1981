<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudent */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Graduate Student',
]) . $modelUser->first_name.' '. $modelUser->middle_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduate Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $modelUser->first_name.' '. $modelUser->middle_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="graduate-student-update">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_form', [
        'model' => $model,
        'modelUser'=>$modelUser,
    ]) ?>

</div>
