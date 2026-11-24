<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationEntryTable */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wevaluation Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-entry-table-view">

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
            'eet_et_relId',
            'eet_evaluator_role',
            'eet_reviewee_role',
            'eet_max_point',
            'eet_created_by',
            'eet_created_at',
            'eet_updated_at',
            'eet_updated_by',
            'data:ntext',
        ],
    ]) ?>

</div>
