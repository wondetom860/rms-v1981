<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\PaymentRedemption */

$this->title = $model->stdRel->getFullName().'  >>  '.$model->reason;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Payment Exemption'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="payment-redemption-view">

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
            [
                'attribute' => 'acadYear_relId',
                'value' => function($model){
                    return $model->acadYearRel->sch_year_desc.'-'.$model->semRel->semester_name;
                }
            ],[
                'attribute' => 'std_relId',
                'value' => function($model){
                    return $model->stdRel->getFullName();
                }
            ],
            
            
            'reason:ntext',
            [
                'attribute' => 'approved_by',
                'value' => function($model){
                    return $model->approvedBy ? 'Approved By '.$model->approvedBy->getFullName().' on '.date("d/m/Y",$model->approved_at) : 'Not Approved Yet.';
                }
            ],
            
            'used',
            [
                'attribute' => 'comments',
                'format' => 'raw',
                'value' => function($model){
                    return $model->comments ? json_decode($model->comments) : null;
                }
            ],
        ],
    ]) ?>

</div>
