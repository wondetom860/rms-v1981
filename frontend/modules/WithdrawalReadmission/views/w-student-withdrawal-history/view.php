<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory */

$this->title = $model->swhStdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Withdrawal Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="wstudent-withdrawal-history-view">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php if ($model->swh_lastApproved): ?>
        
    <?php else: ?>
    <p class="pull-right">
        <?php //Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
     <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'swh_acadYear_relId',
                'value' => function($model){
                    return $model->swhAcadYearRel->sch_year_desc."-".$model->swhSemRel->semester_name;
                }
            ],[
                'attribute' => 'swh_std_relId',
                'value' => function($model){
                    return $model->swhStdRel->getFullName();
                }
            ],[
                'attribute' => 'swh_last_dateAttended',
                'format' => 'raw',
                'value' => function($model){
                    if (!$model->section_relId) {
                        $model->getSetStudentSection();
                    }
                    return $model->sectionRel ? $model->sectionRel->gasGsetRel->getGsDisp() : "-" ;
                }
            ],
            [
                'attribute' => 'swh_reason_relId',
                'value' => function($model){
                    $rmvcs = $model->swhReasonRel->remove_course_enrolls ? "YES" : 'NO';
                    $rems = $model->swhReasonRel->readmittable ? "YES" : 'NO';
                    return $model->swhReasonRel->withdrawalres_description." >> [Readmission Possible: {$rems}, Remove Enrolled Courses: {$rmvcs}]";
                }
            ],[
                'attribute' => 'swh_stdSigned',
                'value' => function($model){
                    return $model->swh_stdSigned ? "Signed":"Not Yet";
                }
            ],
            [
                'attribute' => 'swh_course_removed',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->swh_course_removed) {
                        $c = count($model->removedCourses);
                        return "<a href='#' onclick='showRemovedCourses({$model->id})'>{$c} Course(s)</a>";
                    }else{
                        return "-<button class='btn btn-xs btn-link pull-right' onclick='showSemregRecord({$model->id});return false;'>Semester Status</button>";
                    }
                }
            ],
            [
                'attribute' => 'swh_HOD_approved',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->swh_stdSigned) {
                        $apr = $model->swh_HOD_approved ? $model->swhHODApprovedBy->getFullName_av().", on ".date_format(date_create($model->swh_HOD_approved_at),"d/m/Y") : "Pending...";
                        $btnHoDApprove = '';
                        if (!$model->swh_HOD_approved && Yii::$app->user->can('DepartmentHead')) {
                            $btnHoDApprove = " >>> <button class='btn btn-xs btn-warning' onclick='ChangeStatusHod($model->id);return false; '>Approve</button>";
                        }
                        return $apr."{$btnHoDApprove}";
                        // return $apr;
                    }else{
                       return "Pending Student approval/acceptance.."; 
                    }
                }
            ],[
                'attribute' => 'swh_lastApproved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->swh_lastApproved ? "Approved" : "Pending...";
                    $apr .= $model->swhLastApprovedBy?' by '.$model->swhLastApprovedBy->getFullName_av() : '';
                    $apr .= $model->swh_lastApproved_at?' on '.date_format(date_create($model->swh_lastApproved_at),'d/m/Y') : '';
                    $btnLastApprove = '';
                    if ((Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) && $model->swh_HOD_approved && $model->swh_stdSigned) {
                        if ($model->swh_approved) {//first_approval
                            if (!$model->swh_lastApproved) {
                                $btnLastApprove = " >>> <button class='btn btn-xs btn-danger' onclick='ChangeStatus($model->id);return false; '>Last Approval</button>";
                            }else{
                                // display report print link:
                                $btnLastApprove = " &nbsp;&nbsp;&nbsp;&nbsp; Approval Completed, >>> <button class='btn btn-xs btn-danger' onclick='generateReport($model->id);return false; ' title='Print report of withdrawal request'>Generate Report</button>";
                            }
                        }else{
                            $btnLastApprove = " >>> <button class='btn btn-xs btn-warning' onclick='ChangeStatusHod($model->id);return false; '>First Approval</button>";
                        }
                    }
                    return $apr."{$btnLastApprove}";
                }
            ],
            
            // 'swh_approved_at',
            // 'swh_created_at',
            // 'swh_created_by',
            // 'swh_updated_at',
            // 'swh_updated_by',
            // 'swh_HOD_approved',
            // 'swh_HOD_approved_at',
            // 'swh_HOD_approved_by',
            [
                'attribute' => 'swh_returnedBack',
                'value' => function($model){
                    return $model->swh_returnedBack ? "Yes" : "Not yet.";
                }
            ],
            // 'swh_stdSigned_at',
            // 'swh_stdSigned_by',
            // 'swh_lastApproved',
            // 'swh_lastApproved_at',
            // 'swh_lastApproved_by',
            // 'swh_supDoc:ntext',
            // 'swh_last_dateAttended',
        ],
    ]) ?>

</div>
<script type="text/javascript">
    const generateReport = (wthdrwlId) => {
        // window.open('/request-withdrawal/get-application-report',)
        window.open('/request-withdrawal/get-application-report2?hypherMode=1&upgraded=true&id='+wthdrwlId,'title:Student Withdrawal request report','width:1090,height: 600');
    }
    const ChangeStatusHod = (wthdrwlId) => {
        if (confirm('Are you sure to approve the request?')) {
            // ApproveRequestLast
            $.post('/w-student-withdrawal-history/approve-request',{reqId:wthdrwlId,action:1}).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                }else{
                    alert(resp);
                }
            });
        }
    }

    const ChangeStatus = (wthdrwlId) => {
        if (confirm('Are you sure to approve the request?')) {
            // ApproveRequestLast
            $.post('/w-student-withdrawal-history/approve-request-last',{reqId:wthdrwlId,action:1}).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                }else{
                    alert(resp);
                }
            });
        }
    }
    const showSemregRecord = (wthdrwlId) => {
        $('#modal').find('.modal-header').html('Witdrawal request detail: Semester grade report');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/WithdrawalReadmission/w-student-withdrawal-history/get-student-semester-status',{wthdrwlId:wthdrwlId}).done(function(data){
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
</script>
