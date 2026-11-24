<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlAc */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Std Status Calculation Control Acs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-ac-view">

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
            'id',
            'sssccAC_acadYear_relId',
            'sssccAC_Fs',
            'sssccAC_sem_relId',
            'sssccAC_adm_relId',
            'sssccAC_prog_relId',
            'sssccAC_sssn_relId',
            'sssccAC_max_CGPA',
            'sssccAC_min_CGPA',
            'sssccAC_max_SGPA',
            'sssccAC_min_SGPA',
            'sssccAC_clslvl_relId',
            'sssccAC_remark:ntext',
            'sssccAC_created_by',
            'sssccAC_created_at',
            'sssccAC_updated_at',
            'sssccAC_updated_by',
        ],
    ]) ?>

</div>
