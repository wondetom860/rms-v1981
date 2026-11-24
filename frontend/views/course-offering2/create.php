<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2 */

$this->title = Yii::t('rms', 'Add new course offering');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offerings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-create">

    <!-- <h3><?= Html::encode($this->title) ?></h3> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
