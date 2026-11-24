<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Admission $model*/

$this->title = $model->admission_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Admissions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admission-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'admission_name',
            'admission_status',
            [
                'attribute' => 'Academic Programs',
                'format' => 'raw',
                'value' => function ($model) {
                    $programs = $model->academicPrograms;
                    if ($programs) {
                        $programNames = array_map(function ($program) {
                            return $program ? $program->getProgramHeader() : '';
                        }, $programs);
                        return implode('<br>', $programNames);
                    }
                    return '';
                }
            ],
            'admission_created_by',
            'admission_created_at',
            'admission_updated_by',
            'admission_updated_at',
        ],
    ]) ?>

</div>