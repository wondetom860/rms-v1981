<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2Detail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-detail-view">

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
            'srd2_semreg_relId',
            'srd2_coff_relId',
            'srd2_course_relId',
            'srd2_grade_relId',
            'srd2_coadd_flag',
            'srd2_corptd_flag',
            'srd2_codrpd_relId',
            'srd2_corProject',
            'srd2_enrollApproved',
            'srd2_enrollApproved_by',
            'srd2_enrolApprovrd_at',
            'srd2_group_relId',
            'srd2_updated_by',
            'srd2_updated_at',
            'srd2_created_at',
            'srd2_created_by',
        ],
    ]) ?>

</div>
