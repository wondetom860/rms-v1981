<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Instructor */

$this->title = Yii::t('rms', 'Update profile >> {modelClass}: ', [
    'modelClass' => 'Instructor',
]) . $model->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Instructors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->getFullName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="instructor-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
