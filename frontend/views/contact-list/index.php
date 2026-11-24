<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContactListSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Contact Address');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-list-index">

    <h3><?= Html::encode($this->title) ?>

    <p class="pull-right">
        <?= Yii::$app->user->can('Admin') ? Html::a(Yii::t('rms', 'Create Contact List'), ['create'], ['class' => 'btn btn-success btn-xs']) :'' ?>
    </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'cl_catagory_relId',
                'value'=>'clCatagoryRel.cat_name',
            ],
            'cl_full_name',
            'cl_phone_office',
            'cl_phone_mobile',
            'cl_phone_ext',
            //'cl_created_at',
            //'cl_updated_at',
            //'cl_created_by',
            //'cl_updated_by',
            //'data:ntext',

            [
                'attribute'=>'Check',
                'value'=> function($model){
                    return $model->checkAltered($model) ? 'Valid':'Invalid';
                } 
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header'=>'Options',
                'visible'=>(\yii::$app->user->can('Admin') || \yii::$app->user->can('supper_admin')),
                'options'=>[
                    'style'=>'overflow: auto; word-wrap: text-nowrap;',
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
