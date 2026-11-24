<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
$singleModel = new \backend\models\AddHistory();
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AddHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$singleModel->addPossible();

// $singleModel->maxCredit < ($singleModel->collectedCredit + $singleModel->pendingCredit);

$this->title = Yii::t('rms', 'Course Add Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add-history-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?php if (Yii::$app->user->can('Student')) { ?>
            Creadits Collected In This Semester: <b><i><?= $singleModel->collectedCredit ?></i></b>, 
            Credit Hour to Enroll: [<b><i><?= $singleModel->minCredit."-".$singleModel->maxCredit ?></i></b>],
            Add Approval Pending Credits: <b><i><?= $singleModel->pendingCredit ?></i></b>,
            Free Credit Hrs: <b><i><?= $singleModel->freeCreditHrs ?></i></b>,
        <?php } ?>

        <?= Yii::$app->user->can('Student') &&  $singleModel->possible ? Html::a(Yii::t('rms', 'Request Course Add'), ['create'], ['class' => 'btn btn-success']) : $singleModel->message ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ah_acadYear_relId',
                'value' => function($model){
                    return $model->ahAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\AddHistory::find()->groupBy('ah_acadYear_relId')->all(),'ah_acadYear_relId',function($model){ return $model->ahAcadYearRel->sch_year_desc; }),
            ],
            [
                'attribute' => 'ah_sem_relId',
                'value' => function($model){
                    return $model->ahSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\AddHistory::find()->groupBy('ah_sem_relId')->all(),'ah_sem_relId',function($model){ return $model->ahSemRel->semester_name; }),
            ],
            [
                'attribute' => 'stdId',
                'value' => function($model){
                    return $model->ahStdRel->std_regId;
                }
            ],
            [
                'attribute' => 'first_name',
                'value' => function($model){
                    return $model->ahStdRel->getFullName();
                }
            ],

            [
                'attribute' => 'courseTitle',
                'contentOptions' => ['style' => 'white-space:pre-line; word-wrap:break-word;'],
                'value' => function($model){
                    return $model->ahCourseRel->course_title."[{$model->ahCourseRel->getCourseCredits()}]";
                }
            ],[
                'attribute' => 'ah_addWithGroup_relId',
                'format' => 'raw',
                'value' => function($model){
                    return "<b class='bg-success'>{$model->ahAddWithGroupRel->gsGroupRel->group_name}</b>:".$model->ahAddWithGroupRel->getGsDisp()."";
                }
            ],           
            [
                'attribute' => 'ah_coff_relId',
                'value' => function($model){
                    return $model->ahCoffRel->coff2detInstRel->getFullName();
                },
                'filter' => ArrayHelper::map(\backend\models\AddHistory::find()
                    ->innerJoin('course_offering2_detail','course_offering2_detail.id=add_history.ah_coff_relId')
                    ->groupBy('coff2det_inst_relId')->all(),
                    'ah_coff_relId',
                    function($model){ 
                        return $model->ahCoffRel->coff2detInstRel->getFullName(); 
                    }),
            ],           

            [
                'attribute' => 'ah_approved',
                'format' => 'raw',
                'value' => function($model){
                    $srd2 = $model->ahSrd2Rel;
                    if (Yii::$app->user->can('Instructor')) {
                        if ($model->ah_approved) {
                            // if not graded, allow instructor to remove the approval.
                            if ($srd2) {
                                $tot = $srd2->getTotalMark();
                                if (explode('/', $tot)[0] == 0) {//No assessment made yet
                                    // return "<button class='btn btn-xs btn-warning' title='Cancel Approval? Student registration entry will be permanently removed.' onclick='cahngeStatus({$model->id},2)'>Cancel</button>";
                                    return "<span class='bg-success'>Approved</span>";
                                }else{
                                    return "Assessment. [{$tot}]";
                                }
                            }
                        }else{
                            return "<button class='btn btn-xs btn-primary' title='Approve' onclick='cahngeStatus({$model->id},1)'>Approve</button>";
                        }
                    }else if (Yii::$app->user->can('Student')) {
                        if ($model->ah_approved){
                            return "Request approved.";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Cancel</button>";
                        }
                    }else if (Yii::$app->user->can('Admin')) {
                        if ($model->ah_approved){
                            return "<span class='bg-success'>Approved</span>";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }
                    }else if (Yii::$app->user->can('Registrar')) {
                        if ($model->ah_approved){
                            return "<span class='bg-success'>Approved</span>,<button class='btn btn-xs btn-danger pull-right' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }else{
                            return "Pending approval, <button class='btn btn-xs btn-danger pull-right' title='Cancel request' onclick='cahngeStatus({$model->id},3)'>Delete</button>";
                        }
                    }else{
                        // only for report. No action can be put here.
                        if ($model->ah_approved){
                            return "<span class='bg-success'>Approved</span>";
                        }else{
                            return "<span class='bg-danger'>Pending approval</span>";
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
                    $.post('/approve-course-add/changerequeststatus',{reqId:modelId,actionId:option}).done((reply) => {
                        if (reply == 1) {
                            window.location.href = window.location;//reaload the page
                        } else{
                           alert(reply); 
                        }
                    });
                }
            }else{
                if (confirm('Are you sure to change the status of request?')) {
                    $.post('/add-history/change-status',{id:modelId,option:option}).done((reply) => { if (reply == 1) {
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
