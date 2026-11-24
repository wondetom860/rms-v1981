<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration3 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration3s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration3-view">

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
            'semreg_stud_relId',
            'semreg_sem_relId',
            'semreg_coff_relId',
            'semreg_coff_relIds',
            'semreg_mark_obtained',
            'semreg_created_on',
            'semreg_created_by',
            'semreg_updated_on',
            'semreg_updated_by',
            'remark:ntext',
        ],
    ]) ?>

</div>
