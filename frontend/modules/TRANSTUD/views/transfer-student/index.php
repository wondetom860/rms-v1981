<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\TRANSTUD\models\TransferStudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Transfer Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transfer-student-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'New Transfer Student'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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
                'value' => function($model){
                    return  $model->stdRel->std_regId;
                }
            ],
            'transfered_from',
            [
                'attribute' => 'class_level_joined',
                'format' => 'raw',
                'value' => function($model){
                    return $model->classLevelJoined ? $model->classLevelJoined->getClslvlDisp() : Yii::t('rms', 'Unknown');
                }
            ],
            'year_admitted',
            [
                'attribute' => 'year_transfered',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->yearTransfered ? $model->yearTransfered->getAcHeader() : Yii::t('rms', 'Unknown');
                }
            ],
            'sgpa',
            'cgpa',
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