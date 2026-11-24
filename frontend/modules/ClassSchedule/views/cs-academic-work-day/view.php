<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAcademicWorkDay */

$this->title = $model->day_name."[{$model->class_start_at} - {$model->class_end_time}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Work Days'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cs-academic-work-day-view">

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
            'day_name',
            'class_start_at',
            'duration_one_class',
            'lunch_time',
            'class_end_time',
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            [
                'attribute' => 'updated_at',
                'value' => function($model){
                    return $model->updated_at ? date("d/m/Y",$model->updated_at):null;
                }
            ],
            
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->created_by ? $model->createdBy->username:null;
                }
            ],[
                'attribute' => 'updated_by',
                'value' => function($model){
                    return $model->updated_by ? $model->updatedBy->username:null;
                }
            ],[
                'attribute' => 'data:ntext',
                'value' => function($model){
                    return $model->checkAltered() ? 'Fine':'Altered';
                }
            ],
        ],
    ]) ?>

</div>
