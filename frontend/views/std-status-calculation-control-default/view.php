<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlDefault */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Std Status Calculation Control Defaults'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-default-view">

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

            'sssccd_adm_relId',
            'sssccd_prog_relId',
            'sssccd_sem_relId',
            'sssccd_sssn_relId',
            'sssccd_max_CGPA',
            'sssccd_min_CGPA',
            'sssccd_remark:ntext',
            'sssccd_created_by',
            'sssccd_created_at',
            'sssccd_updated_at',
            'sssccd_updated_by',
            'sssccd_max_SGPA',
            'sssccd_min_SGPA',
            'sssccd_clslvl_relId',
        ],
    ]) ?>

</div>
