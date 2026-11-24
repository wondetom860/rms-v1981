<?php
// use Yii;
use yii\helpers\Html;
// use yii\grid\GridView;
// use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
// use kartik\date\DatePicker;


// use kartik\select2\Select2;
// use yii\helpers\ArrayHelper;
// use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Readmission approval - Group assignment');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php if(Yii::$app->session->hasFlash('message')){ ?>
                <div class="alert alert-warning  alert-dismissable">
                    <?= Yii::$app->session->getFlash('message'); ?>
                </div>
            <?php }?>
        </div>
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Student Readmission - assign readmitted students to a group
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="requestFormCaontainer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- requestFormCaontainer -->
<!-- btnContainer -->

<?php

$this->registerJs(
    "

    $('#wstudentreadmissionhistory-applyform').on('change',function(event){
        if(displayed == 0){
            $('#btnContainer').show();
            displayed = 1;
        }else{
            $('#btnContainer').hide();
            displayed = 0;
        }
    });

    displayed = 0;

    window.changeStatus = function(){
        console.log('Changed...');
    }

    window.AssignGroup = function(gsetId,stdId){
        if(confirm('Assign Student to the selected group?')){
            $.post('/request-readmission/assign-group',
                {
                    gsetId:gsetId,
                    stdId:stdId
                }).done(function(data){
                    if(data == 1){
                        loadApplicationForm();
                    }else{
                        $('#requestFormCaontainer').html(data);
                    }
            });
        }
    }

    window.loadDeptGroups = function(entry){
        // console.log();return;
        $.post('/w-group-setup/get-group-lists-peryear3',
            {
                acadYearId:entry.acadYearId,
                semId:entry.semId,
                deptId:entry.deptId,
                admId:entry.admId,
                progId:entry.progId,
                clslvlId:entry.clslvlId,
                specId:entry.specId,
            }).done(function(data){
                // console.log(data);
                $('.dept-group-select').html(data);
            });
    }

    window.loadApplicationForm = function(){
        $.post('/request-readmission/get-ungrouped-students',{}).done(function(data){
            try{
                d = JSON.parse(data);
                if(d[0] == 1){
                    $('#requestFormCaontainer').html(d[1]);
                    loadDeptGroups(d[2]);
                }else{
                    $('#requestFormCaontainer').html(data);
                }
                }catch(e){
                    $('#requestFormCaontainer').html(data);
                    loadDeptGroups();
                }
        });
    }

    // auto load application form
    loadApplicationForm();
    ",
    $this::POS_READY);
?>