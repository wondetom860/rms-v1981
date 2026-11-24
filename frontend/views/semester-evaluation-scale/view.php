<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationScale */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Scales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-scale-view">

    <h3><?= Html::encode($this->title) ?></h3>

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
            'ses_name',
            'ses_maxPoint',
            'ses_minPoint',
            'ses_pass_status',
            'remark:ntext',
        ],
    ]) ?>

</div>
