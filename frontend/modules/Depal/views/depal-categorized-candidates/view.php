<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategorizedCandidates */

$this->title = $model->settingRel->sectionRel->getClassLevel();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Elligible Candidates'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="depal-categorized-candidates-view">

    <h4><?= $model->settingRel->getSettingHeader() ?></h4>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'setting_relId',
                'value' => function($model){
                    return $model->settingRel->sectionRel->academicYearRel->getAcHeader();
                },
            ],
            [
                'attribute' => 'category_relId',
                'value' => function($model){
                    return $model->categoryRel->category_name;
                }
            ],
            'criteria_name',
            [
                'attribute' => 'candidates',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('__report/grouped-candidates-report',['model' => $model]);
                }
            ],
            [
                'attribute' => 'finalized',
                'format' => 'raw',
                'value' => function($model){
                    $finalized = $model->finalized ? "Finalized" : 'Not Yet';
                    $btnFinalize = $model->finalized ? :"<button class='btn btn-xs btn-warning' onclick='finalizeSelection({$model->id});return false'>Finalize</button>";
                    $btn = $model->finalized ? "<button class='btn btn-xs btn-info' onclick='printReport({$model->id});return false'>Final Report</button>":"<button class='btn btn-xs btn-warning' onclick='printReport({$model->id});return false'>Tentative Report</button>";
                    return "{$finalized} >> {$btnFinalize}<p class='pull-right'>Print report: {$btn}</p>";
                }
            ]
        ],
    ]) ?>

</div>

<script type="text/javascript">
    const finalizeSelection = (modelId) => {
        if (confirm('Are you sure to finalize selection')) {
            $.post('/Depal/depal-categorized-candidates/finalize-selection',{modelId:modelId}).done((resp) => {
                if (resp == 1) { window.location.href = window.location }else{ alert(resp); }
            });
        }
    }
    const printReport = (modelId) => {
        window.open('/Depal/depal-categorized-candidates/get-allotting-report?hypherMode=1&upgraded=true&categorizedid='+modelId,'title:Student allotment','width:1090,height: 600');
    }
</script>
