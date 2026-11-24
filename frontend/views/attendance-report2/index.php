<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\WGroupAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Attendance Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendance-report-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="container-fluid" id="departments_conatiner">
          loading group setups for this semetser.
    </div>
</div>


<?php
$this->registerJs(
    "
    // departments_conatiner
    // actionGetlistReport

    // gsetId,clslvlId,deptId,groupId
    gsetId = null;
    clslvlId = null;
    deptId = null;
    groupId = null;
    requiredList = null;
    coffId = null;
    courseId = null;

    window.printCont = function(deptId,clslvlId,gsetId,groupId){
        params = {
            'deptId':deptId,
            'clslvlId':clslvlId,
            'gsetId':gsetId,
            'groupId':groupId,
            'requiredList':requiredList,
            'coffId':coffId,
            'courseId':courseId,
        };
    myWindow = window.open(
              '/w-group-setup/print-student-list?params='+JSON.stringify(params), 
              'Print Students Group Assignment Report', 
              'height=640,width=700');

        // myWindow.print();
        // myWindow.close();
        return false;
    }


    window.generateHallTicketReport = function(stdId){
        myWindow = window.open(
              '/attendance-report2/get-exam-hall-ticket?params='+JSON.stringify(stdId), 
              'Print Students Exam Hall Ticket', 
              'height=640,width=700');

        myWindow.print();
        // myWindow.close();
        return false;
    }
    

    window.loadStudentsS = function(reqIn){
        courseId = reqIn;
    }


    window.loadStudents = function(gsetId,deptId,clslvlId,groupId){
        $('#StudentContainer').html('<span><i>Loading Students in the group, please wait...</i></span>');
        coffId = $('#deptidddd :selected').data('coff');
        courseId = $('#deptidddd :selected').val();
        $.post('/attendance-report2/get-report',
        {
            gsetId:gsetId,
            clslvlId:clslvlId,
            deptId:deptId,
            groupId:groupId,
            requiredList:requiredList,
            coffId:coffId,
            courseId:courseId

        }).done(function(data){
            $('#StudentContainer').html(data);
        });
    }
    window.loadGroups = function(gsetId,clslvlId,deptId){
        $('#groupContainer').html('<span><i>Loading Groups, please wait...</i></span>');
        $.post('/attendance-report2/get-group',
        {
            gsetId:gsetId,
            clslvlId:clslvlId,
            deptId:deptId

        }).done(function(data){
            $('#groupContainer').html(data);
        });
    }
    window.loadBatch = function(gsetId,deptId){
        $('#batchContainer').html('<span><i>Loading Batches, please wait...</i></span>');
        $.post('/w-group-setup/get-batch',
        {
            gsetId:gsetId,
            deptId:deptId

        }).done(function(data){
            $('#batchContainer').html(data);
        });
    }

    window.loadGroupSetup = function(){
        $.post('/w-group-setup/getlist-report',{}).done(function(data){
            $('#departments_conatiner').html(data);
        });
    }

    loadGroupSetup();
    ",
    $this::POS_READY);

?>