<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\TRANSTUD\models\ExemptedCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Exempted Courses');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="exempted-course-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Add Exempted Course'), ['create'], ['class' => 'btn btn-success btn-xs hidden']) ?>
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
                'attribute' => 'ts_relid',
                'format' => 'raw',
                'value' => function ($model) {
                    $ts = $model->tsRel;
                    $htt = "{$ts->stdRel->std_regId} : {$ts->stdRel->getFullName()}";
                    return Html::a($htt, ['/TRANSTUD/transfer-student/view', 'id' => $ts->id], []);
                }
            ],
            'course_code',
            'course_name',
            'course_hours',
            'grade_obtained',
            //'grade_point',
            //'grade_point_sum',
            //'equivalent_course_ids:ntext',
            [
                'attribute' => 'approved',
                'format' => 'raw',
                'value' => function($model){
                    if($model->approved == 1){
                        return Html::tag('span', Yii::t('rms', 'Yes'), ['class' => 'label label-success']);
                    } elseif ($model->approved == 0) {
                        return Html::tag('span', Yii::t('rms', 'No'), ['class' => 'label label-danger']);
                    }
                    // return $model->approved ? Yii::t('rms', 'Yes') : Yii::t('rms', 'No');
                }
            ],
            //'created_at',
            //'updated_at',
            //'deleted_at',
            //'deleted_by',
            //'created_by',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>