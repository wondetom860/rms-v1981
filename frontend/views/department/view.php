<?php

// use yii\console\widgets\Table;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View  $this
 *
 * @var backend\models\Department $model 
 */

$this->title = $model->dept_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-view">

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
            'dept_name',
            'dept_location',
            'dept_phone',
            'dept_code',
            'dept_id_prefix',
            [
                'attribute' => 'Catalog Entries',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('/department/__reports/catalogs', ['model' => $model]);
                }
            ],
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    return $model->status == 1 ? 'Active' : 'Inactive';
                }
            ],
            [
                'attribute' => 'dept_created_by',
                'value' => function ($model) {
                    return $model->deptCreatedBy->username;
                }
            ],
            [
                'attribute' => 'dept_created_at',
                'value' => function ($model) {
                    return date('dMY', strtotime($model->dept_created_at));
                }
            ],
            [
                'attribute' => 'dept_updated_by',
                'value' => function ($model) {
                    return $model->deptUpdatedBy ? $model->deptUpdatedBy->username : '-';
                }
            ],
            [
                'attribute' => 'dept_updated_at',
                'value' => function ($model) {
                    return $model->dept_updated_at ? date('dMY', strtotime($model->dept_updated_at)) : '';
                }
            ],
            // 'dept_updated_at',
            // 'dept_updated_by',
        ],
    ]) ?>

</div>