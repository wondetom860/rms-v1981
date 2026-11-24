<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\DepartmentSelectEntry */

$this->title = $model->getTitle();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Select Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="department-select-entry-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php if ($model->settingRel->isScheduleActive()) { ?>
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

    <?php }else{
        echo "<p class='bg-warning'>Schedule is closed.</p>";
    } ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'student_relId',
                'value' => function($model){
                    return $model->getStudentFullName();
                }
            ],
            [
                'attribute' => 'setting_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getSettingENtry();
                },
            ],
            
            [
                'attribute' => 'dept_choice',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getDeptSelection();
                }
            ],
            [
                'attribute' => 'approved_dept_relId',
                'value' => function($model){
                    return $model->approved_dept_relId? $model->approvedDeptRel->dept_name : 'NOT APPROVED YET';
                }
            ],
            
            //'approved_at',
            //'approved_by',
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date_format(date_create($model->created_at),"d/M/Y");
                }
            ],

            [
                'attribute' => 'approved_at',
                'value' => function($model){
                    return is_null($model->approved_at)?NULL:date_format(date_create($model->approved_at),"d/M/Y");
                }
            ],
        ],
    ]) ?>

</div>
