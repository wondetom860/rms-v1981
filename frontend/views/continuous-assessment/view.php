<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Continuous Assessments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="continuous-assessment-view">

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
            'ca_coff_relId',
            'ca_semreg_relId',
            'ca_std_relId',
            'ca_assignment_relId',
            'ca_markObtained',
            'ca_created_at',
            'ca_created_by',
            'ca_updated_by',
            'ca_updated_at',
            'ca_approved',
            'ca_approved_at',
            'ca_approved_by',
        ],
    ]) ?>

</div>
