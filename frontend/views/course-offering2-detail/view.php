<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offering2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-detail-view">

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
            'coff2det_coff2_relId',
            'coff2det_cocat_relId',
            'coff2det_course_relId',
            'coff2det_inst_relId',
            'coff2det_TA_relId',
            'coff2det_created_by',
            'coff2det_created_at',
            'coff2det_updated_at',
            'coff2det_updated_by',
        ],
    ]) ?>

</div>
