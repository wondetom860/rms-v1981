<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$singleModel = new \backend\models\CourseDropHistory();
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseDropHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$singleModel->dropPossible();

$this->title = Yii::t('rms', 'Course Drop Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-drop-history-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?php if (Yii::$app->user->can('Student')) { ?>
            Creadits Collected In This Semester: <b><i><?= $singleModel->collectedCredit ?></i></b>, 
            Credit Hour to Enroll: [<b><i><?= $singleModel->minCredit."-".$singleModel->maxCredit ?></i></b>],
            Approval Pending drop Credits: <b><i><?= $singleModel->pendingCredit ?></i></b>,
            Free Credit Hrs: <b><i><?= $singleModel->dropableCreditHrs ?></i></b>,
        <?php } ?>

        <?= Yii::$app->user->can('Student') &&  $singleModel->possible ? Html::a(Yii::t('rms', 'Request Course Drop'), ['/course-drop'], ['class' => 'btn btn-success']) : $singleModel->message ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'cdh_acadYear_relId',
                'value' => function($model){
                    return $model->cdhAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseDropHistory::find()->groupBy('cdh_acadYear_relId')->all(),'cdh_acadYear_relId',function($model){ return $model->cdhAcadYearRel->sch_year_desc; })
            ],
            [
                'attribute' => 'cdh_sem_relId',
                'value' => function($model){
                    return $model->cdhSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseDropHistory::find()->groupBy('cdh_sem_relId')->all(),'cdh_sem_relId',function($model){ return $model->cdhSemRel->semester_name; })
            ],
            [
                'attribute' => 'stdId',
                'value' => function($model){
                    return $model->cdhStdRel->std_regId;
                },
            ],
            [
                'attribute' => 'first_name',
                'value' => function($model){
                    return $model->cdhStdRel->getFullName();
                },
            ],
            [
                'attribute' => 'courseTitle',
                'value' => function($model){
                    return "[{$model->cdhCourseRel->course_code}]".$model->cdhCourseRel->course_title."({$model->cdhCourseRel->getCourseCredits()})";
                },
            ],
            [
                'attribute' => 'cdh_clslvl_relId',
                'format' => 'raw',
                'value' => function($model){
                    return "<b>[{$model->cdhStdRel->stdDeptRel->dept_code}]</b>".$model->cdhClslvlRel->getClslvlDisp()."_{$model->cdhClslvlRel->clslvlProgramRel->program_name}";
                },
                'filter' => ArrayHelper::map(\backend\models\CourseDropHistory::find()->groupBy('cdh_clslvl_relId')->all(),'cdh_clslvl_relId',function($model){ $model->cdhClslvlRel->clslvl_num."_{$model->cdhClslvlRel->clslvlProgramRel->program_name}"; })
            ],
            [
                'attribute' => 'cdh_status',
                'format' => 'raw',
                'value' => function($model){
                    // $srd2 = $model->cdhSrd2Rel;
                    if (Yii::$app->user->can('Instructor')) {
                        if ($model->cdh_status) {
                            return "<small class='bg-success'>Request Approved</small>";
                        }else{
                            if (!$model->cdhSrd2Rel) {
                                return "<small class='bg-success'>Approved</small>";
                            }else{
                                // check if the group is withon the advising grojup list.
                                // var_dump($model->isAdvisor());exit;
                                if($model->isAdvisor()){
                                    return "<button class='btn btn-xs btn-primary' title='Approve' onclick='cahngeStatus({$model->id},1)'>Approve</button>";
                                }else{
                                    return "<small class='bg-danger'>Pending approval</small>";
                                }
                                
                            }
                        }
                    }else if (Yii::$app->user->can('Student')) {
                        if ($model->cdh_status){
                            return "<small class='bg-success'>Request Approved</small>";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Cancel</button>";
                        }
                    }else if (Yii::$app->user->can('Admin')) {
                        if ($model->cdh_status){
                            return "<small class='bg-success'>Approved</small>";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }
                    }else if (Yii::$app->user->can('Registrar')) {
                        if ($model->cdh_status){
                            $aby = $model->cdhApprovedBy->getFullName();
                            return "<small class='bg-success'>Approved by <i>{$aby}</i></small>,<button class='btn btn-xs btn-danger pull-right' title='Cancel request?, Student course registration will be restored if possible.' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger pull-right' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }
                    }else{
                        // only for report. No action can be put here.
                        if ($model->cdh_status){
                            return "<small class='bg-success'>Approved</small>";
                        }else{
                            return "<small class='bg-danger'>Pending approval</small>";
                        }
                    }
                }
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        window.cahngeStatus = (modelId,option)=>{
            if (option == 1 || option == 2) {
                if (confirm('Are you sure to change request status?')) {
                    $.post('/approve-course-drop/changerequeststatus',{reqId:modelId,actionId:option}).done((reply) => {
                        if (reply == 1) {
                            window.location.href = window.location;//reaload the page
                        } else{
                           alert(reply); 
                        }
                    });
                }
            }else{
                if (confirm('Are you sure to change the status of request?')) {
                    $.post('/course-drop-history/delete?id='+modelId).done((reply) => { if (reply == 1) {
                            window.location.href = window.location;//reaload the page
                        } else{
                           alert(reply); 
                        } });
                }
            }
        }
    </script>
    <?php Pjax::end(); ?>

</div>
