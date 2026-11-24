<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaDescription */

$this->title = $model->code." : Criteria statement";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Descriptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-criteria-description-view">

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
            [
                'attribute' => 'category_relId',
                'value' => function($model){ return $model->categoryRel->category_name; },
            ],
            'code',
            'description',
            'max_rating_scale',
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>
