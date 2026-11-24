<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExaminees */

$this->title = Yii::t('rms', 'Update Ex Examinees: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ex Examinees'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ex-examinees-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
