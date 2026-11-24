<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SelfSponsoredPaymentHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Self Sponsored Payment Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Approve pending payment requests
                </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive" id="ApproveFormContainer">
                        Loading...
                    </div>
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Approved payment requests made this semester
                </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive" id="ApproveReportContainer">
                        Loading...
                    </div>
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!--    ApproveFormContainer
        ApproveReportContainer 
-->

<?php
$this->registerJs(
    "
    window.changeStatus = function(stdId, actionId,reqId){
        if(confirm('Approve?')){
            actionId = actionId == 0?2:actionId;
            $.post('/self-sponsored-payment-history/approvepayment',
                {
                    stdId,
                    actionId,
                    reqId
                },
                function(data){
                    if(data == 1){
                        alert('Success');
                        loadRequstReport();
                        loadRequsts();
                        }else{
                            $('#ApproveReportContainer').html(data);
                        }
                    });
        }
    }
    window.loadRequstReport = function(){
        $.post(
            '/self-sponsored-payment-history/getapprovedrequests',
            {},
            function(data){
                $('#ApproveReportContainer').html(data);
            }
        );
    }
    window.loadRequsts = function(){
        $('#ApproveFormContainer').html('Loading payment approval requests of this semester.');
        $.post(
            '/self-sponsored-payment-history/getrequests',
            {},
            function(data){
                $('#ApproveFormContainer').html(data);
            }
        );
    }
    loadRequsts();
    loadRequstReport();
    ",
        $this::POS_READY
    );
?>
