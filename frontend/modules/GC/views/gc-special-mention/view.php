<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcSpecialMention */

$this->title = $model->gsm_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gc-special-mention-view">

    <h3><?= Html::encode($this->title) ?>

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
            'gsm_name',
            [
                'attribute' => 'gsm_minCGPA',
                'value' => function($model){
                    return sprintf('%0.2f',$model->gsm_minCGPA);
                }
            ],
            [
                'attribute' => 'gsm_maxCGPA',
                'value' => function($model){
                    return sprintf('%0.2f',$model->gsm_maxCGPA);
                }
            ],
            [
                'attribute' => 'gsm_created_by',
                'value' => function ($model) {
                    return $model->gsmCreatedBy->username;
                }
            ],
            // 'gsm_created_at',
            // 'gsm_updated_at',
            // 'gsm_updated_by',
        ],
    ]) ?>

</div>