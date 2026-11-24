<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalChoice */

$this->title = $model->stdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Choices'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="depal-choice-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p>
        <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'std_regId',
                'value' => function($model){ return $model->stdRel->std_regId; }
            ],
            [
                'attribute' => 'std_relId',
                'value' => function($model){ return $model->stdRel->getFullName(); }
            ],[
                'attribute' => 'setting_relId',
                'format' => 'raw',
                'value' => function($model){ return $model->settingRel->sectionRel->getClassLevelHeader(); },
            ],
            
            [
                'attribute' =>'categorized_under',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getCategorizedUnder($model->stdRel,$model->settingRel);
                },
            ],[
                'attribute' =>'dept_selected',
                'format' => 'raw',
                'value' => function($model){
                    return $model->dept_selected ? $model->stringifyDepartments() : '-';
                }
            ],
            [
                'attribute' => 'secured_dept_relId',
                'value' => function($model){
                    return $model->secured_dept_relId?$model->securedDeptRel->dept_code : '-';
                },
            ],
            'finalizad',
        ],
    ]) ?>

</div>
