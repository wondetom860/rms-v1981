<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAvailableClassRoom */

$this->title = $model->room;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Class Rooms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cs-available-class-room-view">

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
            
            'room',
            'description:ntext',
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status == 1? 'Available':'Not Available';
                }
            ],[
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy->username;
                }
            ],[
                'attribute' => 'updated_by',
                'value' => function($model){
                    return $model->updated_by?$model->updatedBy->username:null;
                }
            ],
            
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return $model->updated_at?date("d/m/Y",$model->updated_at):null;
                }
            ],[
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            [
                'attribute' => 'data',
                'value' => function($model){
                    return $model->checkAltered() ? 'fine':'altered';
                }
            ],
        ],
    ]) ?>

</div>
