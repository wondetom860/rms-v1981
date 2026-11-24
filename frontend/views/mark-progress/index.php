<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel app\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course assessment progress');
$this->params['breadcrumbs'][] = $this->title;
$this->registerCssFile('css/charts.css');
?>
<br>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<h3>
				Course assessment progress for this semester
			</h3>
			<div class="container-fluid table-responsive" style="background-color: white;">
				<div class="col-md-12" id="hhh">
					
				</div>
				<div class="col-md-12" id="hhh2">
					<div class="container-fluid table-responsive" id="pieContainer">
						<button class="btn btn-md btn-secondary" onclick="showPieChart2()" style='display:none;'>Show Chart</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php
	// 
	$this->registerJs(
		"

		window.showPieChart2 = function(){
			$.post('/mark-progress/chart',{}).done(function(data){
				$('#pieContainer').html(data);
			})
		}
		// window.showPieChart = function(){
		// 	$.post('/mark-progress/pie',{}).done(function(data){
		// 		$('#pieContainer').html(data);
		// 	})
		// }
		window.getMarkAssessmentProgress = function(){
			$.post('/mark-progress/get-mark-progress-report2',{}).done(function(data){
				$('#hhh').html(data);
			})
		}

		getMarkAssessmentProgress();
		",
		$this::POS_READY
	);

?>


