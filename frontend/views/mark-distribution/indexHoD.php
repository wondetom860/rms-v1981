<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\InstanceMarkAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Mark Distribution');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
	<div class="row">
		<h4 style="text-align: center; text-decoration: underline; box-shadow: 1px black">
			Continuous Assessment Mark Distribution Report - <b>For Course Department Head</b>
		</h4>
		<div class="col-md-6">
			<div class="container-fluid" id="courseContainer" style="border: 1px solid black; height: 530px; overflow: auto;">
				Loading your department courses' assessment plan
			</div>
		</div>
		<div class="col-md-6">
			<div class="container-fluid" id="markDistributionCotainer">
				
			</div>
		</div>
	</div>
</div>

<!-- markDistributionCotainer -->
<!-- courseContainer -->
<?php
$this->registerJs(
	"
	window.approveMD  = function(instance_liist,option){
		
		if(confirm('You want to change continouos Assessment mark distribution status?')){
			// console.log(instance_liist);
			option = option==0?2:option;
			$.post('/instance-mark-assignment/change-md-status',{imaList:instance_liist,option:option}).done(function(data){if(data > 1){
				// refresh page
				alert('Operation successfull.');
				window.location.href = window.location;
				}
			});
		}
		
	}
	// 1. Load Instructor courses assigned for this semester.
	$.post('/course2/courselists',{t:1},function(data){
    	//console.log(data);
    	$('#courseContainer').html(data);
    	});

	// 2.on selected course , display the catagory and mark slice detail back.
    	window.markDistribution = function(coffdetId,courseId){
    		// console.log(coffdetId,courseId);
    		$('#markDistributionCotainer').html('Laoding assessment break-down...');
    		$.post('/cont-assess-catagory2/get-distribution-report',{coffdetid:coffdetId, courseId:courseId}).done(function(data){
    			$('#markDistributionCotainer').html(data);
    		})
    	}
	",
	$this::POS_READY);
?>