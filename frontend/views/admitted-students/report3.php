<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = 'Admited students';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="jumbothron">
				<h3>Admitted students report in Academic Year (<?php echo \Yii::$app->params['C']['Y'];?>).</h3>
			</div>
			<div class="container-fluid" id="acadYearIds">
			</div>
			<div class="container-fluid" id="admittedStudentsReportContainer">
				
			</div>
		</div>
	</div>
</div>

<!-- admittedStudentsReportContainer -->

<?php
	$this->registerJs(
		"
		
		window.loadStudentAdmissionReport = function(acadYearId){
			$.post('/admitted-students/get-report-per-year',{acadYearId:acadYearId}).done(function(data){
				$('#admittedStudentsReportContainer').html(data);
			}); 
		}
		window.loadAcademicYears = function(){
			$.post('/admitted-students/get-acad-years',{}).done(function(data){
				$('#acadYearIds').html(data);
			});
		}
		loadStudentAdmissionReport();
		loadAcademicYears();
		",
		$this::POS_READY
	);
?>