<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Admission $model */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Admission',
]) . $model->admission_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Admissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->admission_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="admission-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
