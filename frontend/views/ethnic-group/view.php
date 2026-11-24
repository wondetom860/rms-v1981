<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\EthnicGroup */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ethnic Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ethnic-group-view">

    <h3><?= Html::encode($this->title)."({$model->regionRel->region_name})" ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
    </h3>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            [
                'attribute' => 'name',
                'value' => function($model){
                    return "{$model->name}({$model->regionRel->region_name})";
                }
            ],
            [
                'attribute' => 'Students/Users',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/users',['model' => $model]);
                }
            ],
            'remark',
           
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy ? $model->createdBy->username : '-';
                }
            ],
            
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return $model->updated_at ? date("d/m/Y",$model->updated_at) : "-";
                }
            ],

            
            [
                'attribute' => 'updated_by',
                'value' => function($model){
                    return $model->updatedBy ? $model->updatedBy->username : '';
                }
            ],
            // 'data:ntext',
        ],
    ]) ?>

</div>
