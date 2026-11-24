<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\AddHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Add Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="add-history-view">

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
            'ah_srd2_relId',
            'ah_std_relId',
            'ah_course_relId',
            'ah_acadYear_relId',
            'ah_sem_relId',
            'ah_addWithGroup_relId',
            'ah_coff_relId',
            'ah_approved',
            'ah_approved_by',
            'ah_approved_at',
            'ah_created_at',
            'ah_created_by',
            'ah_updated_by',
            'ah_updated_at',
        ],
    ]) ?>

</div>
