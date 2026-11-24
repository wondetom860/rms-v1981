<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicQualification */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Qualifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-qualification-view">

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
            'acq_name',
            'acq_desc:ntext',
            'acq_created_by',
            'acq_created_at',
            'acq_updated_by',
            'acq_updated_at',
        ],
    ]) ?>

</div>
