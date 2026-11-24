<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalSetting */

$this->title = $model->sectionRel->getClassLevel();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Settings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="depal-setting-view">

    <h3>Department selection setting entry >> <i><?= $model->sectionRel->getClassLevelHeader()."- {$model->sectionRel->academicYearRel->getAcHeader()}" ?></i></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Generate report'), ['#'], ['class' => 'btn btn-warning btn-xs','onclick' => "getEligibleStudents({$model->id});return false;"]) ?>
        <?= Html::a(Yii::t('rms', 'Check and Run allotting'), ['#'], ['class' => 'btn btn-warning btn-xs','onclick' => "runAllotting({$model->id});return false;"]) ?>
        <?= Html::a(Yii::t('rms', 'See Categorized Report'), ['#'], ['class' => 'btn btn-info btn-xs','onclick' => "getEligibleStudents({$model->id});return false;"]) ?>
        <?= Html::a(Yii::t('rms', 'Apply Setting'), ['#'], ['class' => 'btn btn-info btn-xs','onclick' => "showEligibleStudents({$model->id});return false;"]) ?>
        <?= $model->status == $model::STATUS_ACTIVE ? Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']):"" ?>
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
            [
                'attribute' => 'section_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->sectionRel->getClassLevelHeader();
                }
            ],
            'depts_count',
            [
                'attribute' => 'category_IDS',
                'format' => 'raw',
                'value' => function($model){ return $model->stringifyCategories(); }
            ],[
                'attribute' => 'elligible_depts',
                'format' => 'raw',
                'value' => function($model){ 
                    // return $model->stringifyDepartments(); 
                    return $this->render("__report/dept_quota",['model' => $model]);
                }
            ],
            [
                'attribute' => 'start_date',
                'value' => function($model){ return date("d/m/Y",$model->start_date); }
            ],[
                'attribute' => 'end_date',
                'value' => function($model){ return date("d/m/Y",$model->end_date); }
            ],
            [
                'attribute' => 'status',
                'value' => function($model){ return $model->getScheduleStatus(true); }
            ],
            [
                'attribute' => 'created_by',
                'value' => function($model){ return $model->createdBy->username; },
            ],
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d-m-Y",$model->created_at);
                }
            ],
            // 'updated_by',
            // 'updated_at',
        ],
    ]) ?>
</div>
<script type="text/javascript">
    window.getReport = (settingid) =>{
        // 
        window.open('/Depal/depal-choice/get-allotting-report?hypherMode=1&upgraded=true&settingid='+settingid,'title:Student allotment','width:1090,height: 600');
    }
    window.runAllotting = (settingid) =>{
        // 
        window.open('/Depal/depal-choice/run-allotting?hypherMode=1&downgraded=true&settingid='+settingid,'title:Student allotment','width:1090,height: 600');
    }
    
    window.getEligibleStudents = (settingid) =>{
        // 
        window.open('/Depal/depal-categorized-candidates/show-grouped-students?hypherMode=1&settingid='+settingid,'title:Student List','width:1090,height: 600');
    }

    window.showEligibleStudents = (settingid) =>{
        // 
        window.open('/Depal/depal-categorized-candidates/get-grouped-students?hypherMode=1&settingid='+settingid,'title:Student List','width:1090,height: 600');
    }
</script>