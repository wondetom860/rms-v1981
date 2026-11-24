<?php echo "Index_Student.<hr>"; ?>

<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Withdrawal Status');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Student Withdrawal Approval Status
                </div>
                <div class="panel-body">
                    <!-- <div class="aler alert-danger" id="updateReportContainer" style="display: none;">
                        
                    </div> -->
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

    // window.approveRequest = function(reqId,action){
    //     action = action==0?2:action;

    //     $.post('/w-student-withdrawal-history/approve-request',{reqId:reqId,action:action})
    //      .done(function(data){
    //         if(data == 1){
    //             loadrequests();
    //         }else{
    //             $('#updateReportContainer').html(data).show();
    //         }
    //         });
    // }

    // $('#wstudentwithdrawalhistory-applyform').on('change',function(event){
    //     if(displayed == 0){
    //         $('#btnContainer').show();
    //         displayed = 1;
    //     }else{
    //         $('#btnContainer').hide();
    //         displayed = 0;
    //     }
    // });

    // displayed = 0;

    // window.changeStatus = function(){
    //     console.log('Changed...');
    // }

    window.loadrequests = function(){
        $('#updateReportContainer').hide();
        $.post('/w-student-withdrawal-history/get-all-requests',{}).done(function(data){
            if(data == 1){
                // requestFormCaontainer();
            }else{
                $('#requestFormCaontainer').html(data);
            }
        });
    }

    // auto load application form
    loadrequests();
    ",
    $this::POS_READY);
?>