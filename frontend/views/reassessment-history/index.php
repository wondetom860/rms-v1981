<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ReassessmentHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Reassessment Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reassessment-history-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
 
            [
                'attribute' => 'ras_acadYear_relId',
                'value' => function($model){
                    return $model->rasAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\ReassessmentHistory::find()->groupBy('ras_acadYear_relId')->all(),'ras_acadYear_relId',function($model){ return $model->rasAcadYearRel->sch_year_desc; }),
            ],
            [
                'attribute' => 'ras_sem_relId',
                'value' => function($model){
                    return $model->rasSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\ReassessmentHistory::find()->groupBy('ras_sem_relId')->all(),'ras_sem_relId',function($model){ return $model->rasSemRel->semester_name; }),
            ],
            [
                'attribute' => 'first_name',
                'value' => function($model){
                    return $model->rasStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'course_title',
                'value' => function($model){
                    return $model->rasCourseRel->course_title;
                }
            ],
            [
                'attribute' => 'ras_registrarApprove_status',
                'header' => 'Option/Status',
                'format' => 'raw',
                'value' => function($model){
                    if (is_null($model->ras_registrarApprove_status)) {
                       if (\Yii::$app->user->can('Registrar')) {
                            return "<button class='btn btn-xs btn-primary' onclick='changeStatus({$model->id},1); return false;' title='Approve request'>Approve</button>";
                        }else if(Yii::$app->user->can('DepartmentHead')){
                            return 'Waiting approval by Registrar';
                        } 
                        return 'Waiting approval by Registrar';
                    }else if ($model->ras_registrarApprove_status == 0) {
                        if (\Yii::$app->user->can('Registrar')) {
                            return "<button class='btn btn-xs btn-warning' onclick='changeStatus({$model->id},1); return false;' title='Accept request'>Change</button>";
                        }else if(Yii::$app->user->can('DepartmentHead')){
                            return 'Request rejected by registrar';
                        }
                        return 'Request rejected by registrar';
                    }else{
                        if (\Yii::$app->user->can('Registrar')) {
                            if (is_null($model->ras_aft_grade_relId) && is_null($model->ras_approved_by)) {
                                return "<button class='btn btn-xs btn-danger' onclick='changeStatus({$model->id},2); return false;' title='Cancel Approval'>Change</button>";
                            }
                        }else if(Yii::$app->user->can('DepartmentHead')){
                            if ($model->ras_registrarApprove_status == 1 && is_null($model->ras_approved_by)) {
                                return "<button class='btn btn-xs btn-primary' onclick='changeStatus({$model->id},1); return false;' title='Accept request'>Change</button>";
                            }else {
                                if ($model->ras_registrarApprove_status == 3) {
                                    return "<span class='bg-warning'>Locked by Registrar</span>";
                                }
                                if (is_null($model->ras_afterMark)) {
                                    return "<button class='btn btn-xs btn-warning' onclick='submitMark({$model->id}); return false;' title='Submit Mark'>Submit Mark</button>";
                                }else{
                                    // check if grade is loacked: 
                                    return $model->rasSrd2Rel->srd2_gradeLocked ? "<span class='bg-info'>Locked</span>": "<button class='btn btn-xs btn-danger' onclick='submitMark({$model->id}); return false;' title='Update request'>Update Mark</button>";
                                }
                            }
                        }

                        if ($model->ras_approved_by == NULL) {
                            return "Waiting approval by Department Head";
                        }else{
                            if (is_null($model->ras_afterMark)) {
                                return "Waiting for mark submission";
                            }else{
                                if (Yii::$app->user->can('Registrar')) {
                                    if ($model->ras_registrarApprove_status != 3) {
                                        return "Mark Submitted,<button class='btn btn-xs btn-warning' onclick='changeStatus({$model->id},3); return false;' title='Lock Operations on this request'>Lock</button>";
                                    }else{
                                        return "Mark Submitted,<button class='btn btn-xs btn-danger' onclick='changeStatus({$model->id},1); return false;' title='Lock Operations on this request'>Un-Lock</button>";
                                    }
                                }
                                $op = $model->ras_registrarApprove_status == 3 ? 'Completed.' : '';
                                return "Mark Submitted, ".$op;
                            }
                        }
                    }
                }
            ],
            [
                'attribute' => 'ras_afterMark',
                'header' => 'Marks/Grades<br><small style="font-size:7pt;">Before Mark|Grade >> After Mark/Grade</small>',
                'format' => 'raw',
                'value' => function($model){
                    $cat = $model->rasContAssCatagRel->cat_maxPoint;
                    $bmark = is_numeric($model->ras_prevMark) ? sprintf("%0.2f",$model->ras_prevMark) : "-";
                    $amark = is_numeric($model->ras_afterMark) ? sprintf("%0.2f",$model->ras_afterMark) : "-";
                    $bgrade = $model->ras_bef_grade_relId ? $model->rasBefGradeRel->gs_ltr_grade : "-";
                    $agrade = $model->ras_aft_grade_relId ? $model->rasAftGradeRel->gs_ltr_grade : "-";
                    $before = "$bmark/$cat | $bgrade";
                    $after = "$amark/$cat | $agrade";
                    return "<span class='pull-right'>{$before} >> {$after}<span>";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => Yii::$app->user->can('Admin') || Yii::$app->user->can('DepartmentHead'),
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        window.submitMark = (modelId)=>{
            $('#modal').find('.modal-header').html('Course Re-assessment History >> Submit Mark');
            $('#modalContent').css('text-align','left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/reassessment-history/update?id='+modelId).done(function(data){
                $('#modalContent').html(data);
            });
        }

        window.changeStatus = (modelId,operation) => {
            if (confirm('Are you sure to change status of request?')) {
                $.post('/reassessment-history/change-status',{id:modelId,operation:operation}).done((resp) => { alert(resp); });
            }
        }
    </script>

    <?php

     Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-md',
     ]);
     echo "<div id='modalContent' class='jumbotron'>Loading form...</div>";
     Modal::end();

     ?>
    <?php Pjax::end(); ?>

</div>
