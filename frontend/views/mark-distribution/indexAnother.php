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
			Continuous Assessment Mark Distribution Report - <b>For Student</b>
		</h4>
		<div class="col-md-6">
			<div class="container-fluid table-responsive" id="courseContainer" style="padding: 5px;">
				
			</div>
		</div>
		<div class="col-md-6" style="border-left: 1px solid black;">
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

	window.showMarkDistributionList = function(courseId){
		// console.log(courseId);

	}


	// 1. Load Instructor courses assigned for this semester.
	$.post('/course2/courselists',{t:1},function(data){
    	//console.log(data);
    	$('#courseContainer').html(data);
    	});
	// 2.on selected course , display the catagory and mark slice detail back.
    	window.markDistribution = function(coffdetId,courseId){
    		console.log(coffdetId,courseId);
    		$.post('/cont-assess-catagory2/get-distribution-report',{coffdet:coffdetId, courseId:courseId}).done(function(data){
    			$('#markDistributionCotainer').html(data);
    		})
    	}
	",
	$this::POS_READY);
?>