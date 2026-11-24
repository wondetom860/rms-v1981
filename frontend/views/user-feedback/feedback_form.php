<?php


use yii\helpers\Html;
use yii\grid\GridView;

?>

<div class="containet-fluid">
	<div class="row">
		<div class="col-md-12">
			<label for="userName">User</label>
			<input type="text" name="name" value="Current User" readonly="true" id="userName" class="form-control">
		</div>
		<div class="col-md-12">
			<label for="feedBackText">Your Feedback Here</label>
			<textarea cols="5" rows="4" required="" tabindex="1" class="form-control" id="feedBackText" placeholder="Enter here">
				
			</textarea>
		</div>
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-12">
					<input type="checkBox" name="Accept" id="checkBox" onchange="change()" value="0">
				</div>
				<div class="col-md-12 bg-danger" id="errorMessage" style="display: none; margin-top: 5px;">
					
				</div>
				<div class="col-md-12">
					<button class="btn btn-primary btn-flat btn-block" onclick="submit()" title="Save feedback" id="submitForm" style="display: none; margin-top: 5px;">Save</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
$this->registerJs(
	"
	ckecked  =0;
	window.submit = function(){
		htmltext = $('#feedBackText').val();
		if(htmltext == '' || htmltext == ' '){
			$('#errorMessage').html('Empty Message, Please enter your feedback text.').show();
		}else{
			$.post('/user-feedback/store',{htmltext:htmltext}).done(function(data){
				if(data == 1){
					alert('Successfully accepted your feedback. Thanks for your reply.');
					$('#modalGeneral').modal('hide');
				}else{
					$('#errorMessage').html('Sorry, There occured an error while storing your feedback. Please retry again. ['+data).show();
				}
			});
		}
	}
	window.change = function(){
		if(ckecked == 1){
			ckecked = 0;
			$('#checkBox').val(ckecked);
			$('#submitForm').hide();
		}else{
			ckecked = 1;
			$('#submitForm').show();
			$('#checkBox').val(ckecked);
		}
	}
	",
	$this::POS_READY);
?>