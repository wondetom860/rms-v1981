<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\ReadmissionHistory */

$this->title = 'Request Re-admission';
$this->params['breadcrumbs'][] = ['label' => 'Readmission Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="readmission-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
