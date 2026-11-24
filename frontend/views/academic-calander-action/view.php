<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalanderAction */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Calander Actions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-action-view">

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
            'aca_name',
            'aca_created_by',
            'aca_created_at',
            'aca_updated_by',
            'aca_updated_at',
        ],
    ]) ?>

</div>
