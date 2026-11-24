<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory */

$this->title = $model->srhStdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Readmission Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="wstudent-readmission-history-view">

    <h3>Student Readmission Request: >> <?= Html::encode($this->title) ?></h3>

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
            [
                'attribute' => 'srh_acadYear_relId',
                'value' => function($model){
                    return $model->srhAcadYearRel->sch_year_desc."-".$model->srhSemRel->semester_name;
                }
            ],[
                'attribute' => 'srh_std_relId',
                'value' => function($model){
                    return $model->srhStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'srh_return_back_on',
                'value' => function($model){
                    return date_format(date_create($model->srh_return_back_on),"d/m/Y");
                }
            ],
            [
                'attribute' => 'srh_HOD_approved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->srh_HOD_approved ? $model->srhHODApprovedBy->getFullName_av().", on ".date_format(date_create($model->srh_HOD_approved_at),"d/m/Y") : "Pending...";
                    $btnHoDApprove = '';
                    if (!$model->srh_HOD_approved && Yii::$app->user->can('DepartmentHead')) {
                        $btnHoDApprove = " >>> <button class='btn btn-xs btn-warning' onclick='ChangeStatusHod($model->id);return false; '>Approve</button>";
                    }
                    return $apr."{$btnHoDApprove}";
                    // return $apr;
                }
            ],[
                'attribute' => 'srh_approved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->srh_approved ? "Approved" : "Pending...";
                    $apr .= $model->srhApprovedBy?' by '.$model->srhApprovedBy->getFullName_av() : '';
                    $apr .= $model->srh_approved_at?' on '.date_format(date_create($model->srh_approved_at),'d/m/Y') : '';
                    
                    $btnLastApprove = '';
                    if (Yii::$app->user->can('Registrar') && $model->srh_HOD_approved) {
                        if ($model->srh_approved) {//first_approval
                            $btnLastApprove = "Approved";
                        }else{
                            $btnLastApprove = " >>> <button class='btn btn-xs btn-warning' onclick='ChangeStatusHod($model->id);return false; '>Approve</button>";
                        }
                    }
                    return $apr."{$btnLastApprove}";
                    // return $apr;
                }
            ],
            [
                'attribute' => 'srh_swh_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $this->render('../w-student-withdrawal-history/__report/withdrawal-sammary',['model' => $model->srhSwhRel]);
                }
            ],[
                'attribute' => 'Section Joined',
                'format' => 'raw',
                'value' => function($model){
                    $gas = $model->getJoinedSection();
                    $btn = "Pending section assignment...";
                    if (Yii::$app->user->can('Admin') && !$gas && $model->srh_HOD_approved && $model->srh_approved) {
                        $btn .= "<button class='btn btn-xs btn-primary pull-right' title='Assign student to a section' onclick='showSections({$model->id});return false;'>Assign</button>";
                    }
                    return $gas ? $gas->gasGsetRel->getGsDisp() : "{$btn}";
                }
            ],
            // 'srh_return_back_letter',
            // 'remark_HOD:ntext',
            // 'remark_registrar:ntext',
            // 'srh_created_at',
            // 'srh_updated_at',
            // 'srh_created_by',
            // 'srh_updated_by',
        ],
    ]) ?>

</div>

<script type="text/javascript">

    __srhId='';
    const assignStudent = (gsetId,stdId) => {
        $.post('/w-group-assignment/create',
        {
            stdList:JSON.stringify([stdId]),
            gid:gsetId
        }).done((resp) => {
            showSections(__srhId);
        });
    }
    const showSections = (readmissionId) => {
        __srhId = readmissionId;
        $('#modal').find('.modal-header').html('Readmission Processing: Section assignment');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/WithdrawalReadmission/w-student-readmission-history/get-active-scetions',{id:readmissionId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
    const showRemovedCourses = (wthdrwlId) => {
        $('#modal').find('.modal-header').html('Witdrawal request detail: Removed courses');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/WithdrawalReadmission/w-student-withdrawal-history/list-removed-courses',{wthdrwlId:wthdrwlId}).done(function(data){
            $('#modalContent').html(data);
        });
    }

    const ChangeStatusHod = (readmissionId) => {
        if (confirm('Are you sure to approve the request?')) {
            // ApproveRequestLast
            $.post('/w-student-readmission-history/approve-request',{reqId:readmissionId,action:1}).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                }else{
                    alert(resp);
                }
            });
        }
    }

    const ChangeStatus = (readmissionId) => {
        if (confirm('Are you sure to approve the request?')) {
            // ApproveRequestLast
            $.post('/w-student-readmission-history/approve-request-last',{reqId:readmissionId,action:1}).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                }else{
                    alert(resp);
                }
            });
        }
    }
</script>
