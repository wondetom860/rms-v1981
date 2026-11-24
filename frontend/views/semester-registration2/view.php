<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2 */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Registration2s'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-view">

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
            'semreg_std_relId',
            'semreg_acadYear_relId',
            'semreg_clslvl_relId',
            'semreg_semester_relId',
            'semreg_SGPA',
            'semreg_CGPA',
            'semreg_approved',
            'semreg_approved_by',
            'semreg_approved_at',
            'semreg_created_by',
            'semreg_updated_by',
            'semreg_created_at',
            'semreg_updated_at',
        ],
    ]) ?>

</div>
