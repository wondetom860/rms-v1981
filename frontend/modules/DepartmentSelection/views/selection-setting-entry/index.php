<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\DepartmentSelection\models\SelectionSettingEntrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Selection Setting Entries');
$this->params['breadcrumbs'][] = ['label' => "Main", 'url' => ['/DepartmentSelection']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="selection-setting-entry-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('decris', 'Create Selection Setting Entry'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'acadYear_relId',
                'value' => 'acadYearRel.sch_year_desc',
            ],
            [
                'attribute' => 'sem_relId',
                'value' => 'semRel.semester_name',
            ],
            [
                'attribute' => 'clslvl_relId',
                'value' => function($model){
                    return $model->clslvlRel->clslvl_num." => ".$model->clslvlRel->clslvlProgramRel->program_name;
                }
            ],
            'max_count_dept',
            //'status',
            //'approved_at',
            [
                'attribute' => 'date_from',
                'value' => function($model){
                    return date_format(date_create($model->date_from),"d/m/Y");
                }
            ],
            [
                'attribute' => 'date_to',
                'value' => function($model){
                    return date_format(date_create($model->date_to),"d/m/Y");
                }
            ],
            
            //'approved_by',
            //'updated_at',
            //'updated_by',
            //'created_at',
            //'created_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
