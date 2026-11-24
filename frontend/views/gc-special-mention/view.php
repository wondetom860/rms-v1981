<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\GcSpecialMention */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-special-mention-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'gsm_maxCGPA',
            'gsm_minCGPA',
            'gsm_name',
            'gsm_created_by',
            'gsm_created_at',
            'gsm_updated_at',
            'gsm_updated_by',
        ],
    ]) ?>

</div>
