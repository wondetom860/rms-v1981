<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseCategory */

$this->title = Yii::t('rms', 'Create Course Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-category-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
