<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategory */

$this->title = $model->category_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="depal-category-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            'category_name',
            'comparison_type',
            'lower_bound',
            'upper_bound',
            'attribute',
        ],
    ]) ?>

</div>
