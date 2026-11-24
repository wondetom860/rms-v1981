<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Course Offering Detail',
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offering'), 'url' => ['/course-offering2/index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="course-offering2-detail-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
