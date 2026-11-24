<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2 */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Course Offering Entry',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offerings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="course-offering2-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
