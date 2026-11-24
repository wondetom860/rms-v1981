<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistory */

$this->title = Yii::t('rms', 'Create Reassessment History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reassessment-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
