<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaBundle */

$this->title = $model->code;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Criteria Bundles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-criteria-bundle-view">

    <h3><?= Html::encode($this->title) ?></h3>

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'code',
            [
                'attribute' => 'eval_type_relId',
                'value' => function($model){
                    return $model->evalTypeRel->type_name;
                },
            ],
            'evaluator_role',
            'candidate_staff_role',
            'average_point',
            'average_percentage',
            [
                'attribute' => 'evaluate_per_course',
                'value' => function($model){
                    return $model->evaluate_per_course ? "YES" : "NO";
                }
            ],
            [
                'attribute' => 'criteria_IDS',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/criteria-in-bundle',['model' => $model]);
                }
            ],[
                'attribute' => 'employment_types',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/employment-types-in-bundle',['model' => $model]);
                }
            ],
            // 'created_at',
            // 'created_by',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script type="text/javascript">
    window.toggleDisplay = (id) => {
        $('#'+id).toggle('display');
    }
</script>
