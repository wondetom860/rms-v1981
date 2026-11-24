<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\WGroupSetup */

$this->title = $model->gsAdmRel->admission_name."-{$model->gsProgRel->program_name}";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Group Setups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-setup-view container-fluid">

    <h3><?= $model->getGsDisp() ?>

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

    <div class="row" style="background-color: white">
        <div class="col-md-5">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'gs_group_relId',
                        'value' => function($model){
                            return $model->gsGroupRel->group_name;
                        },
                    ],[
                        'attribute' => 'gs_adm_relId',
                        'value' => function($model){
                            return $model->gsAdmRel->admission_name;
                        },
                    ],[
                        'attribute' => 'gs_prog_relId',
                        'value' => function($model){
                            return $model->gsProgRel->program_name;
                        },
                    ],[
                        'attribute' => 'gs_acadYear_relId',
                        'value' => function($model){
                            return $model->gsAcadYearRel->sch_year_desc;
                        },
                    ],[
                        'attribute' => 'gs_dept_relId',
                        'value' => function($model){
                            return $model->gsDeptRel->dept_name;
                        },
                    ],[
                        'attribute' => 'gs_clslvl_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->gsClslvlRel->getClslvlDisp();
                        },
                    ],
                    [
                        'attribute' => 'gs_spec_relId',
                        'value' => function($model){
                            return $model->gs_spec_relId ? $model->gsSpecRel->spec_name : null;
                        },
                    ],[
                        'attribute' => 'gs_sem_relId',
                        'value' => function($model){
                            return $model->gsSemRel->semester_name;
                        },
                    ],
                    [
                        'attribute' => 'gs_active_prog_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->checkAcLink();
                        },
                    ],
                    [
                        'attribute' => 'students',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->getWGroupAssignments()->count();
                        },
                    ],
                    
                    [
                        'attribute' => 'gs_created_at',
                        'value' => function($model){
                            return $model->gs_created_at ? date_format(date_create($model->gs_created_at),"d/m/Y") : null;
                        },
                    ],
                    
                    'gs_status',
                    'gs_size',
                    'remark:ntext',
                ],
            ]) ?>
        </div>
        <div class="col-md-7">
            <div class="container-fluid" style="max-height: 450px; overflow: auto;">
                <?= $this->render('__report/assignments',[
                    'assignments' => $model->getWGroupAssignments()
                        ->innerjoin('student','student.id=w_group_assignment.gas_std_relId')
                        ->innerjoin('user','user.id = student.std_user_relId')
                        ->orderBy([
                            'first_name' => SORT_ASC,
                            'middle_name' => SORT_ASC,
                            'last_name' => SORT_ASC,
                        ])->all()
                        ,
                    'model'=>$model,
                    'pts'=>1
                ]) ?>
            </div>
        </div>
    </div>

</div>
