<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Secondary School Attended',
]) . $model->ssa_school_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Secondary School Attendeds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="secondary-school-attended-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
