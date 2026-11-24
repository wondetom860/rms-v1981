<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\ReRegistrationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Re-Registrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="re-registration-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right hidden">
            <?= Html::a(Yii::t('rms', 'Request Re-Registration'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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
                'attribute' => 'std_regId',
                'label' => 'Reg. ID',
                'value' => function($model){
                    return $model->stdRel->std_regId;
                }
            ],
            [
                'attribute' => 'std_relId',
                'value' => function($model){
                    return $model->stdRel->getFullName();
                }
            ],
            [
                'attribute' => 'course_relId',
                'value' => function($model){
                    return $model->courseRel->getCourseCredits()." >> ".$model->courseRel->course_title;
                }
            ],
            
            [
                'attribute' => 'prev_gas_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->prevGasRel->getGsDisp();
                }
            ],
            [
                'attribute' => 'new_gas_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->newGasRel? $model->newGasRel->getGsDisp() : NULL;
                }
            ],
        
            // 'new_coff2det_relId',
            'trail_count',
            'reg_approved',
            //'dept_approved_at',
            //'dept_approved_by',
            //'admin_processed',
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