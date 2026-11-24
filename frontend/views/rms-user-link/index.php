<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\RmsUserLinkSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'HRMS - Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="rms-user-link-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Yii::$app->user->can('Admin') ?
                Html::a(Yii::t('rms', 'Link Account'), ['create'], ['class' => 'btn btn-success btn-xs']) :
                Html::a(Yii::t('rms', 'Link Account'), ['create', 'id' => Yii::$app->user->identity->id], ['class' => 'btn btn-success btn-xs']) ?>
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
                'attribute' => 'person_relId',
                'value' => function ($model) {
                    return $model->getFullNameHrms();
                }
            ],
            [
                'attribute' => 'rms_user_id',
                'value' => function ($model) {
                    return $model->getFullNameRms();
                }
            ],

            [
                'attribute' => 'rms_inst_id',
                'value' => function ($model) {
                    return $model->rmsInstRel ? $model->rmsInstRel->instDepRel->dept_name : null;
                }
            ],
            [
                'attribute' => 'rms_std_id',
                'value' => function ($model) {
                    $spec = $model->rmsStdRel && $model->rmsStdRel->stdSpecRel ? ' > ' . $model->rmsStdRel->stdSpecRel->spec_name : '';
                    return $model->rmsStdRel ? ($model->rmsStdRel->std_regId . " >> " . $model->rmsStdRel->stdDeptRel->dept_code . $spec) : null;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    // if (Yii::$app->language == 'am') {

                    // }else{

                    // }

                    return date_format(date_create($model->created_at), 'd/m/Y');
                }
            ],
            //'created_by',
            //'updated_by',
            //'updated_at',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>