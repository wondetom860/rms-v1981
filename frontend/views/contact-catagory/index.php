<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContactCatagorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Contact Catagories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contact-catagory-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Contact Catagory'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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

            'cat_name',
            [
                'attribute' => 'cat_created_at',
                'value' => function ($model) {
                    return date_format(date_create($model->cat_created_at), 'D, d M');
                }
            ],
            [
                'attribute' => 'Check',
                'value' => function ($model) {
                    return $model->checkAltered($model) ? 'Valid' : 'Invalid';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'header' => 'Options',
                'visible' => (\yii::$app->user->can('Admin') || \yii::$app->user->can('supper_admin')),
                'options' => [
                    'style' => 'overflow: auto; word-wrap: text-nowrap;',
                ]
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>