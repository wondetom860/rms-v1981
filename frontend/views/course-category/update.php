<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseCategory */

$this->title = Yii::t('rms', 'Update Course Category: {name}', [
    'name' => $model->catgeory_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->catgeory_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="course-category-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
