<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalander */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Academic Calanders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-view">

    <h3><?= Html::encode($this->title) ?></h3>

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
            [
                'attribute'=>'acad_acadYear_relId',
                'value'=>'acadAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_acadYear_relId')->all(),'acad_acadYear_relId',function($model){ return $model->acadAcadYearRel->sch_year_desc; })
            ],
            [
                'attribute'=>'acad_sem_relId',
                'value'=>function($model){
                    return  $model->acad_sem_relId ? $model->acadSemRel->semester_name : null;
                } ,
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_sem_relId')->all(),'acad_sem_relId',function($model){ $model->acad_sem_relId ? $model->acadSemRel->semester_name : null; })
            ],
            [
                'attribute'=>'acad_action_relId',
                'value'=>'acadActionRel.aca_name',
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_action_relId')->all(),'acad_action_relId',function($model){ $model->acadActionRel->aca_name; })
            ],
            [
                'attribute'=>'acad_adm_relId',
                'value'=>function($model){
                    return $model->acad_adm_relId ? $model->acadAdmRel->admission_name : 'For All Adm.';
                },
            ],
            [
                'attribute'=>'acad_prog_relId',
                'value'=> function($model){
                    return $model->acad_prog_relId ? $model->acadProgRel->program_name : 'For All Prog.';
                } ,
            ],
            [
                'attribute'=>'acad_clslvl_relId',
                'format'=>'raw',
                'value'=>function($model){
                    return $model->acad_clslvl_relId ? $model->acadClslvlRel->getClslvlDisp() : 'For all class Level';
                },
            ],
            

            [
                'attribute'=>'acad_starttime',
                'value'=>function($model){
                    return date_format(date_create($model->acad_starttime),'M d, Y @ h:i A');
                } ,
            ],

            [
                'attribute'=>'acad_finishtime',
                'value'=>function($model){
                    // $stop_date = new DateTime(date_create($model->acad_finishtime)); 
                    // $stop_date->modify('-1 day');
                    return date_format(date_create($model->acad_finishtime),'M d, Y @ h:i A');
                } ,
            ],
            
            [
                'attribute'=>'acad_status',
                'value'=>function($model){
                    return $model->getStatus()[1];
                },
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->all(),'acad_status',function($model){ return $model->getStatus(1); })
            ],
            [
                'attribute'=>'acad_created_by',
                'value'=>'acadCreatedBy.username',
            ],
        ],
    ]) ?>

</div>
