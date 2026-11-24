<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationTypes */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wevaluation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-types-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('decris', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('decris', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('decris', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'et_name',
            'et_description:ntext',
            'et_per_course',
            'data:ntext',
        ],
    ]) ?>

</div>
