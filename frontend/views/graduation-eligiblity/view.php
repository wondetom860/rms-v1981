<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblity */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Graduation Eligiblities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-eligiblity-view">

    <h1><?= Html::encode($this->title) ?></h1>

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
            'id',
            'ge_adm_relId',
            'ge_prog_relId',
            'ge_num_of_F',
            'ge_min_crdthr',
            'ge_min_CGPA',
            'ge_min_CGPA_major',
            'ge_created_at',
            'ge_created_by',
            'ge_updated_at',
            'ge_updated_by',
        ],
    ]) ?>

</div>
