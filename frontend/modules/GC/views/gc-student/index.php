<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\GC\models\GcStudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Gc Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-student-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Student'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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
                'attribute' => 'std_relId',
                'value' => function ($model) {
                    return $model->stdRel->std_regId;
                }
            ],
            [
                'attribute' => 'entry_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->entryRel->header_title,['/GC/gc-entry/view','id' => $model->id]);
                }
            ],

            [
                'attribute' => 'graduation_mode',
                'value' => function ($model) {
                    return $model->getGraduationMode();
                }
            ],

            
            'total_credit_hr',
            'course_count',
            //'total_gps',
            //'cgpa',
            //'sgpa',
            //'major_gpa',
            //'last_semreg_relId',
            //'reg_approved',
            //'reg_approved_at',
            //'reg_approved_by',
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