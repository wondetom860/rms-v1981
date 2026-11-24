<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = Yii::t('rms', 'Payment Request');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="background-color: #dda;">
			<div class="row">
				<?php $form = ActiveForm::begin(); ?>
				<div class="col-md-3">
					<?= $form->field($model, 'ssph_pymntRsn_relId')->dropdownList(
						ArrayHelper::map(\backend\models\PaymentReason::find()->all(), 'id', 'pr_desc'),
						[
							'prompt' => 'Select payment reason',
						]
					) ?>
				</div>
				<?php
				if (!is_null($courseId)) {
				?>
					<div class="col-md-3">
						<?= $form->field($model, 'courseId')->dropdownList(
							ArrayHelper::map(\backend\models\Course::find()->where(['id' => $courseId])->all(), 'id', 'course_title'),
							[
								'readOnly' => TRUE,
								'title' => 'Re-assessing request for the this course.',
							]
						) ?>
					</div>
				<?php
				}
				?>
				<div class="col-md-3">
					<?= $form->field($model, 'ssph_txn_num')->textInput(['rows' => 1]) ?>
				</div>
				<div class="col-md-3"><br>
					<?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm' : 'btn btn-primary btn-sm', 'id' => 'submitForm']) ?>
				</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-md-12"><br>
			<div class="panel panel-primary">
				<div class="panel-heading">
					Payment requests and status
				</div>
				<div class="panel-body">
					<div class="container-fluid table-responsive" id="paymentStatusContainer">
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

	$('.tr').click(function(){
		$(this).css('background-color','#cde') ;
		alert('Yews');
	});



	window.procReq = function(){
		
		if(confirm('Are you sure to proceed the \''+ $('#selfsponsoredpaymenthistory-ssph_pymntrsn_relid :selected').html() + '\' payment process?')){
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
								loadPaymentReport();
								alert(dd['message']);
								$('#modal_payment').modal('hide');
								window.location.href = window.location;

						}else{
							$('#paymentStatusContainer').html(dd['message']);
						}
						$('#txnNumber').val('');
					}catch(e){
						$('#paymentStatusContainer').html(data);
					}
				});
		}
 
	}

	window.loadPaymentReport = function(){
		// 
		$('#paymentStatusContainer').html('Loading your payment request status.');
		$.post(
			'/payment-request/getpaymentreport',
			{

				},
			function(data){
				$('#paymentStatusContainer').html(data);
				});
	}


	window.loadApprovedPayments = function(){
		// GetApprovedPayments
		$('#paymentStatusContainer').html('Loading your payment request status.');
		$.post(
			'/payment-request/getpaymentreport',
			{

				},
			function(data){
				$('#paymentStatusContainer').html(data);loadPaymentReport();
				});
	}

	loadPaymentReport();
	
	",
	$this::POS_READY
);
?>