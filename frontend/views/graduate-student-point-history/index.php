<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\GraduateStudentPointHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Graduate Student Point Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduate-student-point-history-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Graduate Student Point History'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'gph_gstd_relId',
                'value' => function($model){
                    $user = $model->gphGstdRel->gstdUserRel;
                    return $user->first_name .' '. $user->middle_name.' ('. $model->gphGstdRel->gstdProgRel->program_name.")" ;
                }
            ],
            [
                'attribute' => 'acadYear',
                'value' => function($model){
                    return $model->gphRegistrationEntryRel->gphr_acadYear;
                }
            ],
            [
                'attribute' => 'gph_SGPA',
                'value' => function($model){
                    return sprintf("%0.2f",$model->gph_SGPA);
                }
            ],
            [
                'attribute' => 'gph_CGPA',
                'value' => function($model){
                    return sprintf("%0.2f",$model->gph_CGPA);
                }
            ],
            'gph_credit_hour_enrolled',
            // 'gph_average_point',
            [
                'attribute' => 'gph_average_point',
                'value' => function($model){
                    return sprintf("%0.3f",$model->gph_average_point);
                }
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'approved_at',
            //'approved_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
