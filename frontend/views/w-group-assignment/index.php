<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\WGroupAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Group Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wgroup-assignment-index">

    <h3><?= Html::encode($this->title) ?>
    <p class="pull-right">
        <?php
            if (\Yii::$app->user->can('Admin')) {
                ?>
                <?= Html::a(Yii::t('rms', '+'), ['create'], ['class' => 'btn btn-success btn-sm']) ?>
                <?php
            }

            echo Html::a(Yii::t('rms', 'Under-loaded Students'), 
            ['#'], 
            [
                'class' => 'btn btn-success btn-xs',
                'onclick' => "ListUnderLoadedStudents();return false;"
            ]);
        ?>
    </p></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="container-fluid" id="departments_conatiner">
          loading group setups for this semetser.
    </div>
</div>

<script>
    const ListUnderLoadedStudents = () => {
        window.open(
			'/w-group-assignment/get-under-loaded-students',
			'title="Semester regsitration report"',
			'height=600,width=1000');
    }
    const ListUnderLoadedStudents2 = () => {
        $('#modal').find('.modal-header').html('Under-Loaded Students\' List');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modalContent').addClass('table-reponsive');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/w-group-assignment/get-under-loaded-students', {

        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>

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

    window.printCont = function(deptId,clslvlId,gsetId,groupId){
        params = {
            'deptId':deptId,
            'clslvlId':clslvlId,
            'gsetId':gsetId,
            'groupId':groupId,
            'requiredList':requiredList,
        };
    myWindow = window.open(
              '/w-group-setup/print-student-list?params='+JSON.stringify(params), 
              'Print Students Group Assignment Report', 
              'height=640,width=700');

        // myWindow.print();
        // myWindow.close();
        return false;
    }
    
    window.deleteAssign = function(assId){
        if(confirm('You want to delete the assignment?')){
            $.post('/w-group-assignment/delete-assign',{id:assId}).done(function(data){
                if(data == 1){
                    loadStudents(gsetId,deptId,clslvlId,groupId);
                }else{
                     $('#StudentContainer').html(data);
                }
            });
        }
    }

    window.loadStudentsS = function(reqIn){
        requiredList = reqIn;
    }
    window.loadStudents = function(gsetId,deptId,clslvlId,groupId){
        $('#StudentContainer').html('<span><i>Loading Students in the group, please wait...</i></span>');
        $.post('/w-group-setup/get-students',
        {
            gsetId:gsetId,
            clslvlId:clslvlId,
            deptId:deptId,
            groupId:groupId,
            requiredList:requiredList

        }).done(function(data){
            $('#StudentContainer').html(data);
        });
    }
    window.loadGroups = function(gsetId,clslvlId,deptId){
        $('#groupContainer').html('<span><i>Loading Groups, please wait...</i></span>');
        $.post('/w-group-setup/get-group',
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