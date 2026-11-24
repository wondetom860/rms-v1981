<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ClearanceAllowedStudent */

$this->title = Yii::t('rms', 'Clearance Allowed Student');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Clearance Allowed Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clearance-allowed-student-create">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
