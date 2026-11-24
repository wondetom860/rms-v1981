<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CocatDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Approve course drop requests');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
     Modal::begin([
        'header'=>'<h4>More about course drop requset. </h4>',
        'id'=>'modal',
        'size'=>'modal-lg',
     ]);
     echo "<div id='modalContent'>Loading course drop request details...</div>";
     Modal::end();
     ?>

<div class="container-fluid"><br>
	<div class="row">
		<div class="col-md-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approval pending course drop requests
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="ApproveFormContainer" style="max-height: 500px; overflow-y: auto; ">
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
					Approved course drop requests in this semester
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
		$('#modalContent').html('Request Id: '+reqId);
	}

	
	window.changeStatus = function(reqId,actionId){
		action_ = (actionId == 0)?'cancel':'approve';
		aId = (actionId == 0)?'2':actionId;
		console.log(aId);
		if(confirm('Are you sure to '+action_+' the request?')){
			$.post(
			'/approve-course-drop/changerequeststatus',
			{
				reqId:reqId,
				actionId:aId
			},
			function(data){
				if(data == 1){
					// Success.
					loadApprovedCourseDropRequests();
					loadCourseDropRequests();
				}else{
					$('#ApproveReportContainer').html(data);
				}
				});
		}
		// console.log('Action selected: '+actionId);
	}
    
	window.loadApprovedCourseDropRequests = function(deptId){
		$.post(
			'/approve-course-drop/getapproveddroprequests',
			{
			},
			function(data){
				$('#ApproveReportContainer').html(data);
			});
	}

	window.loadCourseDropRequests = function(deptId){
		$.post(
			'/approve-course-drop/getdroprequests',
			{
			},
			function(data){
				$('#ApproveFormContainer').html(data);
			});
	}

	loadCourseDropRequests();
	loadApprovedCourseDropRequests();

	",
	$this::POS_READY
);
?>