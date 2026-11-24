<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\WStudentReadmissionHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Wstudent Readmission Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-readmission-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('decirs', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decirs', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decirs', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'srh_acadYear_relId',
            'srh_sem_relId',
            'srh_swh_relId',
            'srh_std_relId',
            'srh_return_back_on',
            'srh_HOD_approved',
            'srh_HOD_approved_at',
            'srh_HOD_approved_by',
            'srh_approved',
            'srh_approved_at',
            'srh_approved_by',
            'srh_return_back_letter',
            'remark_HOD:ntext',
            'remark_registrar:ntext',
        ],
    ]) ?>

</div>
