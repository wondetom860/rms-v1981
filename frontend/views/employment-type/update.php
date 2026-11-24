<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\EmploymentType */

$this->title = Yii::t('rms', 'Update Employment Type: {name}', [
    'name' => $model->et_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Employment Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="employment-type-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
