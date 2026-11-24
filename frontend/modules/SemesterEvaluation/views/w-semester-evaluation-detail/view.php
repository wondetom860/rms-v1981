<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wsemester Evaluation Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wsemester-evaluation-detail-view">

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
            'sed_se_relId',
            'sed_ep_relId',
            'sed_point_given',
            'data:ntext',
        ],
    ]) ?>

</div>
