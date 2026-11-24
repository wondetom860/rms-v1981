<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rms', 'Payment Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Payment requests and status
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" style="height: 50%; overflow-y: auto;" id="paymentStatusContainer">
						Loading
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approve Payment requests
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" style="height: 50%; overflow-y: auto;" id="paymentReportContainer">
						Loading
					</div>
				</div>
				<div class="panel-footer">

				</div>
			</div>
		</div>
	</div>
</div>

<!-- 
	paymentFromContainer
	paymentStatusContainer 
-->
<!-- procReq   BUTTON -->

<?php
$this->registerJs(
	"
	$('#submitForm').click(function(e){
		e.preventDefault();
		procReq();
		});
	
	window.deletePaymentrequest = (reqId) => {
		if(confirm('Are you sure to delete this payment request?')){
			$.post('/payment-request/remove-request',{reqid:reqId}).done(function(data){
				if(data == 1){
					loadPaymentReport();
					loadApprovedPayments();
				}else{
					alert(data);
				}
			});
		}
	}

	window.changeBack = function(reqId){
		if(confirm('Are you sure to cahnge the payment approve?')){
			$.post('/payment-request/change-back',{reqId:reqId}).done(function(data){
				if(data == 1){
					loadPaymentReport();
					loadApprovedPayments
				}else{
					alert(data);
				}
			});
		}
	}

	$('.tr').click(function(){
		$(this).css('background-color','#cde') ;
		alert('Yews');
	});


	window.procReq = function(){
		txn = $('#selfsponsoredpaymenthistory-ssph_txn_num').val();
		paymentReason = $('#selfsponsoredpaymenthistory-ssph_pymntrsn_relid').val();
		courseId = $('#selfsponsoredpaymenthistory-courseid').val();
		$('#paymentFromContainer').html('Processing your payment request.');
		$.post(
			'/payment-request/processpayment',
			{
				txn:txn,
				paymentReason:paymentReason,
				courseId:courseId
				}
			).done(function(data){
				try{
					dd = JSON.parse(data);
					if(dd['ok'] == 1){
						
							$('#selfsponsoredpaymenthistory-ssph_txn_num').val('');
							$('#selfsponsoredpaymenthistory-ssph_pymntrsn_relid').val('');
							alert(dd['message']);
							$('#modal_payment').modal('hide');
							loadPaymentReport();

					}else{
						$('#paymentStatusContainer').html(dd['message']);
					}
					$('#txnNumber').val('');
					}catch(e){
						$('#paymentStatusContainer').html(data);
					}
			});

	}

	window.changeStatus = function(reqId,actionId){

		action_ = (actionId == 0)?'disapprove':'approve';
		aId = (actionId == 0)?'2':actionId;
		//console.log(aId);
		if(confirm('Are you sure to '+action_+' the request?')){
			$.post(
			'/payment-request/changerequeststatus',
			{
				reqId:reqId,
				actionId:aId
			},
			function(data){
				if(data == 1){
					// indicates success.
					// loadCourseAddRequests();
					// loadApprovedCourseAddRequests();
					loadPaymentReport();
					loadApprovedPayments
				}else{
				$('#paymentReportContainer').html(data);
				}
				});
		}
		// console.log('Action selected: '+actionId);
		// alert('REQUEST ID: '+reqId);
	}

	window.loadApprovedPayments = function(){
		// 
		$('#paymentReportContainer').html('Loading your payment request status.');
		$.post(
			'/payment-request/get-approved-payments',
			{

				},
			function(data){
				$('#paymentReportContainer').html(data);
				});
	}

	

	window.loadPaymentReport = function(){
		// 
		$('#paymentStatusContainer').html('Loading your payment request status.');
		$.post('/payment-request/getpaymentreport',{},
			function(data){
				$('#paymentStatusContainer').html(data);
			});
	}
	loadPaymentReport();
	loadApprovedPayments();
	",
	$this::POS_READY
);
?>