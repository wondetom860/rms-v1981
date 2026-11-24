<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGrades */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Touched Grades'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="touched-grades-view">

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
            'tg_acadYear_relId',
            'tg_sem_relId',
            'tg_prog_relId',
            'tg_clslvl_relId',
            'tg_semreg_relId',
            'tg_srd2_relId',
            'tg_beforeGrade_relId',
            'tg_afterGrade_relId',
            'tg_reason:ntext',
            'tg_created_at',
            'tg_created_by',
            'tg_updated_at',
            'tg_updated_by',
            'tg_approved_by',
            'tg_approved_at',
            'tg_operation_completed',
        ],
    ]) ?>

</div>
