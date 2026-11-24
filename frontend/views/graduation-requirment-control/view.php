<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduationRequirmentControl */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Graduation Requirment Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-requirment-control-view">

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
            'gr_program_relId',
            'gr_maj',
            'gr_min',
            'gr_created_at',
            'gr_created_by',
            'gr_updated_at',
            'gr_updated_by',
        ],
    ]) ?>

</div>
