<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExInvigilators */

$this->title = Yii::t('rms', ' Add Invigilators');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Invigilators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-invigilators-create">

    <?= Yii::$app->request->isAjax ? '' : '<h3>' . Html::encode($this->title) . '</h3>' ?>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>