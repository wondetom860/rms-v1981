<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Wevaluation Point Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-point-entry-table-view">

    <h3><?= Html::encode($this->title) ?></h3>

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

            'epet_eet_relId',
            'epet_ep_relId',
            'epet_max_point',
            'data:ntext',
        ],
    ]) ?>

</div>
