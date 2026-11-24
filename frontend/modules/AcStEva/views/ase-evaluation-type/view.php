<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationType */

$this->title = $model->type_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Evaluation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-evaluation-type-view">

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
            'type_name',
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
                'attribute' => 'Criteria Bundles',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('../ase-criteria-bundle/__report/bundle-by-eval-type',['model' => $model]);
                }
            ],
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script type="text/javascript">
    
</script>
