<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */

$this->title = Yii::t('decirs', 'Create Course Offering2 Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Course Offering2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <div class="course-offering2-detail-create"> -->

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

<!-- </div> -->
