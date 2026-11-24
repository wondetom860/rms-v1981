<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Payment Request');
$this->params['breadcrumbs'][] = $this->title;
?>
<hr>
<?php $form = ActiveForm::begin(); ?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12" style="background-color: #dda;">
			<div class="row">
                <div class="col-md-3">
                 <?= $form->field($model, 'ssph_pymntRsn_relId')->dropdownList(ArrayHelper::map(\backend\models\PaymentReason::find()->all(),'id','pr_desc'),
                 	[
                 		'prompt'=>'Select payment reason',
                 		'onchange'=>'
                 			//console.log($(this).val());
                 			if($("#selfsponsoredpaymenthistory-ssph_pymntrsn_relid :selected").html() == "Course Re-assessment"){
                 				$("#courseContainer").show();
                 				$.post("/course2/courselists",{dropDown:true}).done(function(data){
                 					$("#selfsponsoredpaymenthistory-courseid").html(data);
                 				});
                 			}else{
                 				$("#courseContainer").hide();	
                 			}
                 		',
                 	]) ?>
                </div>
                
        		<div class="col-md-3" id="courseContainer" style="display: none;">
                 <?= $form->field($model, 'courseId')->dropdownList(ArrayHelper::map(\backend\models\Course::find()->all(),'id','course_title'),
                 	[
                 		'prompt'=>'Select Course',
                 	]) ?>
                </div>
                		
                <div class="col-md-3">
                    <?= $form->field($model, 'ssph_txn_num')->textInput(['rows' => 1]) ?>
                </div>
                <div class="col-md-3"><br>
                    <div class="form-group"  style="vertical-align: middle;">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'submitForm']) ?>
                    </div>
                </div>
            </div>
		</div>
		<?php ActiveForm::end(); ?>
		<div class="col-md-12"><hr>
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
	courseContainer -->

<!-- procReq   BUTTON -->

<?php
$this->registerJs(
	"
	$('#submitForm').click(function(e){
		e.preventDefault();
		procReq();
		})

	$('.tr').click(function(){
		$(this).css('background-color','#cde') ;
		alert('Yews');
	});

	window.procReq = function(){
		txn = $('#selfsponsoredpaymenthistory-ssph_txn_num').val();
		paymentReason = $('#selfsponsoredpaymenthistory-ssph_pymntrsn_relid').val();
		$('#paymentFromContainer').html('Processing your payment request.');
		$.post(
			'/payment-request/processpayment',
			{
				txn:txn,
				paymentReason:paymentReason
				}
			).done(function(data){
				try{
					dd = JSON.parse(data);
					if(dd['ok'] == 1){
						
							$('#selfsponsoredpaymenthistory-ssph_txn_num').val('');
							$('#selfsponsoredpaymenthistory-ssph_pymntrsn_relid').val('');
							alert(dd['message']);
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
	loadPaymentReport();
	",
	$this::POS_READY
);
?>