<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExInvAttendanceForm */

$this->title = Yii::t('rms', 'Create Ex Inv Attendance Form');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ex Inv Attendance Forms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-inv-attendance-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
