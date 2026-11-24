<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcStudent */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gc-student-view">

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
            'entry_relId',
            'graduation_mode',
            'total_credit_hr',
            'course_count',
            'total_gps',
            'cgpa',
            'sgpa',
            'major_gpa',
            'last_srd2_relId',
            'reg_approved',
            'reg_approved_at',
            'reg_approved_by',
            'created_at',
            'updated_at',
            'deleted_at',
            'deleted_by',
            'created_by',
            'updated_by',
            'data:ntext',
        ],
    ]) ?>

</div>
