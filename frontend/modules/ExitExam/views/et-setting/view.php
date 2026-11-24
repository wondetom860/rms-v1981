<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtSetting */

$this->title = $model->setting_header_info;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main Page'), 'url' => ['/ExitExam/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="et-setting-view">

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
            'setting_header_info',
            'max_trails',
            'pass_mark',
            'active_status',
            'locked',
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:dMY'],
            ],

            'created_by',
            'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>