<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reassessment-history-view">

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
            'ras_std_relId',
            'ras_srd2_relId',
            'ras_course_relId',
            'ras_acadYear_relId',
            'ras_sem_relId',
            'ras_group_relId',
            'ras_clslvl_relId',
            'ras_status',
            'ras_registrarApprove_status',
            'ras_registrarApprove_by',
            'ras_registrarApprove_at',
            'ras_approved_by',
            'ras_bef_grade_relId',
            'ras_app_date',
            'ras_created_at',
            'ras_updated_at',
            'ras_updated_by',
            'ras_aft_grade_relId',
            'ras_prevMark',
            'ras_afterMark',
            'ras_contAssCatag_relId',
        ],
    ]) ?>

</div>
