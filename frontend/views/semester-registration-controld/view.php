<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterRegistrationControld */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Registration Controlds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-controld-view">

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
            'src_adm_relId',
            'src_prog_relId',
            'src_sem_relId',
            'src_clslvl_relId',
            'src_sssn_relId',
            'src_max_crdthr',
            'src_min_crdthr',
            'src_remark:ntext',
            'src_created_by',
            'src_created_at',
            'src_updated_at',
            'src_updated_by',
        ],
    ]) ?>

</div>
