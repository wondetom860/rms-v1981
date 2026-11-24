<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluationTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-types-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Semester Evaluation - Types
                        <?= Html::a(Yii::t('rms', 'Add New Semester Evaluation Type'), ['create'], ['class' => 'btn btn-success btn-xs pull-right']) ?>
                    </div>
                    <div class="panel-body">
                        <?php Pjax::begin(); ?>
                        <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'semev_type_desc:ntext',
                                'semev_type_level',
                                [
                                    'attribute' => 'semev_type_maxPoint',
                                    'value' => function ($model) {
                                        return !is_null($model->semev_type_maxPoint) ? sprintf("%0.2f", $model->semev_type_maxPoint) : '-';
                                    }
                                ],

                                ['class' => 'yii\grid\ActionColumn'],
                            ],
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>