<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\Attendance */ 

$this->title = 'Attendance Form';
$this->params['breadcrumbs'][] = ['label' => 'Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendance-create">

    <h2><?= Html::encode($this->title) ?></h2>

    <?= $this->render('_attendanceform', [
        'model' => $model,
    ]) ?>

</div>
