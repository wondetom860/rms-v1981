<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CocatDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Approve course add requests');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
     Modal::begin([
        'header'=>'<h4>More about course add requset. </h4>',
        'id'=>'modal',
        'size'=>'modal-lg',
     ]);
     echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
     Modal::end();
     ?>
<hr>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approve pending course add requests
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="ApproveFormContainer">
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
					Approved course add requests made this semester
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="ApproveReportContainer">
						Loading...
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
	</div>
</div>
<!-- ApproveReportContainer -->
<!-- ApproveFormContainer -->
<!-- modalContent -->
<!-- modal -->

<?php
$this->registerJs(
	"

	window.seeMore = function(reqId){
		console.log('Request Id: '+reqId);

		$('#modal').modal('show');
		$('#modal').find('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
		$('#modalContent').html('Loading...');
		$.post(
			'/approve-course-add/getmoreaboutaddrequest',
			{
				reqId:reqId
			},
			function(data){
				$('#modalContent').html(data);
				});
	}

	
	window.changeStatus = function(reqId,actionId){
		action_ = (actionId == 0)?'disapprove':'approve';
		aId = (actionId == 0)?'2':actionId;
		console.log(aId);
		if(confirm('Are you sure to '+action_+' the request?')){
			$.post(
			'/approve-course-add/changerequeststatus',
			{
				reqId:reqId,
				actionId:aId
			},
			function(data){
				if(data == 1){
					// indicates success.
					loadCourseAddRequests();
					loadApprovedCourseAddRequests();
				}else{
					$('#ApproveReportContainer').html(data);
				}
				});
		}
		console.log('Action selected: '+actionId);
	}

	window.loadApprovedCourseAddRequests = function(deptId){
		$.post(
			'/approve-course-add/getapprovedaddrequests',
			{
			},
			function(data){
				$('#ApproveReportContainer').html(data);
			});
	}

	window.loadCourseAddRequests = function(deptId){
		$.post(
			'/approve-course-add/getaddrequests',
			{
			},
			function(data){
				$('#ApproveFormContainer').html(data);
			});
	}

	loadCourseAddRequests();
	loadApprovedCourseAddRequests();

	",
	$this::POS_READY
);
?>