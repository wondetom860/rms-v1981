<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyCourseAssignment */

$this->title = $model->coff2detRel->coff2detCourseRel->getCourseCredits();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Faculty Course Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="faculty-course-assignment-view">

    <h3>Course Title: <?= Html::encode($this->title)." >> ".$model->coff2detRel->coff2detCourseRel->course_title ?></h3>

    <p class="pull-right hidden">
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'attribute' => 'coff2det_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->coff2detRel->coff2detAcProgRel->getAcadYearHeader()." [".$model->coff2detRel->coff2detAcProgRel->getClassLevelHeader()."({$model->coff2detRel->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name})]";
                }
            ],
            [
                'attribute' => 'enrolled students',
                'format' => 'raw',
                'value' => function($model){
                    return $model->coff2detRel->coff2detCoff2Rel->getSemesterRegistration2Details($model->coff2detRel->coff2det_course_relId)->count();
                }
            ],[
                'attribute' => 'Faculty',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/faculty-list',['model' => $model]);
                }
            ],
        ],
    ]) ?>

</div>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading form...</div>";
    Modal::end();
?>
