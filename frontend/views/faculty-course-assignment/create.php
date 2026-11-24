<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyCourseAssignment */

$this->title = Yii::t('rms', 'Add New Faculty for Course Offering');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Faculty Course Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faculty-course-assignment-create">

    <h4><?= Html::encode($this->title) ?> >> <?= $model->coff2detRel->coff2detAcProgRel->getAcadYearHeader()." >> ".$model->coff2detRel->coff2detAcProgRel->getClassLevelHeader() ?></h4>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
