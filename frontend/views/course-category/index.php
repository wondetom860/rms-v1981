<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CourseCategorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Categories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-category-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right float-right">
            <?= Html::a(Yii::t('rms', 'Create Course Category'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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

            'catgeory_name',
            [
                'attribute' => 'pass_grade_relId',
                'value' => function ($model) {
                    return $model->pass_grade_relId ? $model->passGradeRel->gs_ltr_grade : NULL;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date("dMY", $model->created_at);
                }
            ],
            // 'updated_at',
            //'created_by',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>