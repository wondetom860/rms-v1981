<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\ClassSchedule\models\CsAcademicWorkDaySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Academic Work Days');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-academic-work-day-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Add Academic Work Day'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'day_name',
            'class_start_at',
            'duration_one_class',
            'lunch_time',
            'class_end_time',
            //'created_at',
            //'updated_at',
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->created_by ? $model->createdBy->username : null;
                }
            ],
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
