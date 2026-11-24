<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\EmploymentType */

$this->title = $model->et_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Employment Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="employment-type-view">

    <h3><?= Html::encode($this->title) ?></h3>

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'et_name',
            'cont_renewal_interval_month',
            [
                'attribute' => 'et_created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->et_created_at);
                }
            ],
            [
                'attribute' => 'et_created_by',
                'value' => function($model){
                    return $model->etCreatedBy?$model->etCreatedBy->username:'-';
                }
            ],
            [
                'attribute' => 'et_updated_by',
                'value' => function($model){
                    return $model->et_updated_by ? $model->etUpdatedBy->username : '';
                }
            ],
            [
                'attribute' => 'et_updated_at',
                'value' => function($model){
                    return $model->et_updated_at ? date("d/m/Y",$model->et_updated_at) : '';
                }
            ],
            
            
            
            
            'remarks:ntext',
        ],
    ]) ?>

</div>
