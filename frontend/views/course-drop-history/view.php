<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseDropHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Drop Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="course-drop-history-view">

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
            'cdh_srd2_relId',
            'cdh_std_relId',
            'cdh_course_relId',
            'cdh_acadYear_relId',
            'cdh_sem_relId',
            'cdh_group_relId',
            'cdh_clslvl_relId',
            'cdh_approved',
            'cdh_approved_by',
            'cdh_status',
            'cdh_created_at',
            'cdh_updated_at',
            'cdh_updated_by',
        ],
    ]) ?>

</div>
