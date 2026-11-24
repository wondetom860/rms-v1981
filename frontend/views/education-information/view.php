<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Education Informations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="education-information-view">

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
            'ei_G10_doc_scan:ntext',
            'ei_G12_doc_scan:ntext',
            'ei_enteranceMarkPoint',
            'ei_created_at',
            'ei_updated_at',
            'ei_created_by',
            'ei_updated_by',
        ],
    ]) ?>

</div>
