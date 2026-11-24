<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcademicCalendar\models\AcAcademicYearSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Academic Years');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
// $this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-academic-year-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'New Academic Year'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

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
                    return date('d-m-Y', $model->start_date);
                }
            ],
            [
                'attribute' => 'end_date',
                'value' => function ($model) {
                    return date('d/m/Y h:i A', $model->end_date);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->getStatus();
                }
            ],

            //'created_by',
            //'created_at',
            //'updated_by',
            //'updated_at',
            //'remark:ntext',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>