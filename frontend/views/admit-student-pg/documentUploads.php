<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Profile Photo';
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="container-fluid">

	<div class="panel panel-info">
		<div class="panel-body">
			<div class="container-fluid">
				<div class="row">
					<div class="col-md-12">
						<div class="panel panel-primary">
							<div class="panel-heading">
								<h2>Upload Profile Image</h2>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-md-6" style="border-right: 1px solid black;">
										<h4>Get for whom you want to change profile picture.</h4>
										<div class="container-fluid">
											<div class="col-md-10">
												<input type="text" class="form-control" name="" title="Enter user search key of whom you are to change profile image." id="id_input">
											</div>
											<div class="col-md-2 pull-left">
												<button class="btn pull-right btn-primary" onclick="searchUser();return false;">Search</button>
											</div>
										</div>
										<hr>
										<div class="container-fluid" id="ressult_container">
											
										</div>
									</div>
									<div class="col-md-6" style="border-left: 1px solid black; display: none;" id="profileImageContainer">
										<h4>Profile Image</h4>

										<?php $form = ActiveForm::begin(['id'=>'profileUpload','options'=>['enctype'=>'multipart/form-data'],'action'=>'/admit-student/upload-photo']); ?>

										<div class="container-fluid" id="imageContainer">
											<img src="/uploads/avatar.png" alt="Profile Photo" id="user_image" height="250" width="auto">
											<?php echo Html::activeHiddenInput($model, 'id_for_file');?>
											<?= $form->field($model,'file')->fileInput(['required'=>true])?>
											<button>Upload</button>
										</div>

										<?php ActiveForm::end(); ?>

									</div>
								</div>
							</div>
						</div>
						

					</div>
					<!-- <div class="col-md-6">
						<h2>Upload Grade 8 document (transicript)</h2>
					</div>
					<div class="col-md-6">
						<h2>Upload Grade 10 documnet (transicript)</h2>
					</div>
					<div class="col-md-6">
						<h2>Upload Grade 12 document (transcript)</h2>
					</div> -->
				</div>
			</div>
		</div>
	</div>
	
</div>
<!-- ressult_container -->

<?php
$this->registerJs(
	"

	userId = null;

	window.submitProfile = function(){
		// $('#profileUpload').submit();

		// $.ajax({
		// 	type:'POST',
		// 	url:form.attr('action'),
		// 	data:form.serialize(),
		// 	success:function(data){
		// 		if(data == 1){
		// 			loadImagePRE(userId);
		// 		}else{
		// 			alert('Error while uploading user profile photo. Please try again.');
		// 		}
		// 	},
		// 	error:function(data){
		// 		$('#profileImageContainer').html(data);
		// 	}
		// });
		// // console.log(form);
	}

	window.loadImagePRE = function(userId1){
		userId = userId1;
		// console.log(userId);
		$('#profileImageContainer').hide();
		$('#user1-file').val('');
		// get user uploadeed photo.
		$('#user1-id_for_file').val(userId1);
		$.post('/admit-student/get-user-image',{userId:userId1}).done(function(data){
			$('#user_image').attr('src','/uploads/'+data);
			$('#profileImageContainer').show();
		});
	}
	window.searchUser = function(){
		userId2 = $('#id_input').val();
		if( userId2 == 'undefined' || userId2 == '' || userId2 == ' '){
			alert('Input empty, Please enter the one.');
			$('#id_input').focus();
		}else{
			$('#ressult_container').html('Searching...');
			$.post('/admit-student/get-user-by-id',{userId:userId2}).done(function(data){
				$('#ressult_container').html(data);
			});
		}
	}
	",
	$this::POS_READY);
?>