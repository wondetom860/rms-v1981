<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassScheduleDetail */

$label = $model->gsetRel->gsGroupRel->group_name." - {$model->gsetRel->gsDeptRel->dept_code}";
$this->title = Yii::t('rms', 'Update Class Schedule Detail: {name}', [
    'name' => $label,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', ' Class Schedule Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $label, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cs-class-schedule-detail-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
