<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\SelectionSettingEntry */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Selection Setting Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="selection-setting-entry-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('decris', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decris', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decris', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            
            'acadYear_relId',
            'sem_relId',
            'clslvl_relId',
            'max_count_dept',
            'status',
            'approved_at',
            'approved_by',
            'updated_at',
            'updated_by',
            'created_at',
            'created_by',
            'data:ntext',
        ],
    ]) ?>

</div>
