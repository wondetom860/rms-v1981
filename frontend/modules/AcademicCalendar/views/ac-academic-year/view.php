<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcAcademicYear */

$this->title = $model->school_year . "-" . $model->semester . "({$model->season})";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Years'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ac-academic-year-view">

    <h3>Academic Year: <?= Html::encode($this->title) ?>

        <p class="bg-info pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <div class="row" style="background-color:white;">
        <div class="col-md-4">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'college_relId',
                        'value' => function ($model) {
                            return $model->collegeRel->name;
                        }
                    ],
                    'school_year',
                    'semester',
                    'season',
                    [
                        'attribute' => 'start_date',
                        'value' => function ($model) {
                            return date('d M, Y', $model->start_date);
                        }
                    ],
                    [
                        'attribute' => 'end_date',
                        'value' => function ($model) {
                            return date('d M, Y', $model->end_date);
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($model) {
                            return $model->getStatus();
                        }
                    ],
                    [
                        'attribute' => 'GC Students',
                        'label' => 'GC Students',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $btn1= "<button class='btn btn-xs btn-info' style='margin: 0px 3px;' onclick='pringGCList({$model->id});return false;' title='Show GC Students Grade Summery'>Show List</button>";
                            // $btn2= "<button class='btn btn-xs btn-primary' style='margin: 0px 3px;' onclick='pringGCList2({$model->id});return false;' title='Show GC Students Grade Summery(2)'>Show List(2)</button>";
                            return $btn1;//.$btn2;
                        }
                    ],
                ],
            ]) ?>
        </div>
        <div class="col-md-8" style="font-size: 9pt; max-height: 480px; overflow: auto;">
            <?= $this->render('../ac-active-programs/__report/index', ['records' => $model->acActivePrograms, 'options' => true, 'ayId' => $model->id]) ?>
        </div>
    </div>

</div>


<script>
    const pringGCList = function(acyId) {
        myw3 = window.open(
            '/AcademicCalendar/ac-academic-year/graduate-student-list?acyId=' + acyId + "&gsetId=" + 909,
            'title=Graduate Student List',
            'height=1100,width=1300');
    }
</script>