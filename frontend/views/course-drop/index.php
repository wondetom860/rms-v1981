<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */

$this->title = Yii::t('rms', 'Course Drop Histories');
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- <h2>course Add/index</h2> -->

<?php 
	
	if (Yii::$app->user->can('Student')) {

		?>
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							Course Drop History 
						</div>
						<div class="panel-body">
							<div class="container-fluid" id="CoursedropFromContainer">
								
							</div>
						</div>
					
						<div class="panel-footer">
							
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="panel panel-primary">
						<div class="panel-heading">
							Course Drop requests in this semester.
						</div>
						<div class="panel-body">
							<div class="container-fluid" id="CoursedropReportContainer">
								
							</div>
						</div>
						<div class="panel-footer">
							
						</div>
					</div>
				</div>
			</div>
		</div>

		<?php
		
	}else{
		echo "<p class='bg-danger'>You have nothing to do here.</p>";die();
	}

?>
<!-- CourseDropFromContainer -->
<!-- CourseDropReportContainer -->

<?php
$this->registerJs(
	"
	$('#CoursedropFromContainer').html('Loading your enrolled courses...');

	window.req = function(courseId,srd2Id){
		if(confirm('You want to drop this course?:' )){
			$.post(
				'/course-drop/requestcoursedrop',
				{
					courseId:courseId,
					srd2Id:srd2Id
				},
				function(data){
					if(data == 1){
						loadEnrolledCoursess();
						loadRequestStatus();
						}else{
							$('#CoursedropReportContainer').html(data);
						}
				}
				);
		}
		
	}
	window.loadEnrolledCoursess = function(){
		$.post(
		'/course-drop/getenrolledcourses',
		{},
		function(data){
			$('#CoursedropFromContainer').html(data);
		}

	);
	}
	window.loadRequestStatus = function(){
		$.post(
		'/course-drop/requeststatus',
		{},
		function(data){
			$('#CoursedropReportContainer').html(data);
		}

	);
	}
	loadEnrolledCoursess();
	loadRequestStatus();
	",
	$this::POS_READY
);
?>