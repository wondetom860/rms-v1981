<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseCategory */

$this->title = $model->catgeory_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="course-category-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right float-right">
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
            'catgeory_name',
            [
                'attribute' => 'pass_grade_relId',
                'value' => function ($model) {
                    return $model->pass_grade_relId ? $model->passGradeRel->gs_ltr_grade : NULL;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date("dMY", $model->created_at);
                }
            ],
            // 'updated_at',
            // 'created_by',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>