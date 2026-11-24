<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\Attendancedetail */

$this->title = 'Create Attendancedetail';
$this->params['breadcrumbs'][] = ['label' => 'Attendancedetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendancedetail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
