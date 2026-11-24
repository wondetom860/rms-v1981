<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Semester Course Coordinator');
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="semester-completion-check-index">
    <div class="container-fluid">
        <div class="row" style="font-size: 10pt;">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Semester Course Coordinator Assignment, By <b>Department Head</b>
                </div>
                <div class="panel-body">
                    <div class="col-md-12 col-lg-12 colsm-12">
                        <div class="container-fluid table-responsive" id="formContainer">
                            Loading Your department Courses offered for this semester.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(
    "

    window.removeCordinator = function(id){
        if(confirm('Assign as course co-ordinator?')){
            $.post('/assign-semester-course-cordinator/remove-cordinator',
                {
                    id:id
                }
                ).done(function(data){
                if(data == 1){
                    loadCourses();
                }
            });
        }
    }
    window.assignCordinator = function(instId,coffId){
        courseId = $('#inst_select_'+coffId+' :selected').data('course');
        admId = $('#inst_select_'+coffId+' :selected').data('adm');
        if(instId != $('#inst_select_'+coffId+' :selected').data('inst')){ 
            alert('Invalid Input!!');
            return;
        }else{
            if(confirm('Assign as course co-ordinator?')){
                $.post('/assign-semester-course-cordinator/set-cordinator',
                    {
                        courseId:courseId,
                        instId:instId,
                        admId:admId
                    }
                    ).done(function(data){
                    if(data == 1){
                        loadCourses();
                    }
                });
            }
        }
    }

    window.loadCourses = function(){
        $.post('/assign-semester-course-cordinator/get-minst-course',{}).done(function(data){
            $('#formContainer').html(data);
        });
    }

    loadCourses();
    ", $this::POS_READY);
?>
