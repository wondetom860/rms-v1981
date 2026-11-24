<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Penality Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<hr>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-5" style="background-color: #dda;">
			<div class="row">
                <div class="col-md-12" id="penalityPayedStudentsContainer" style="padding: 10px;">
                 
                </div>
            </div>
		</div>
		<div class="col-md-7">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Enroll courses for paid student.
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="paymentStatusContainer">
						Waiting for paid student courses...
					</div>
					<div class="col-md-12" style="background-color: #ace; display: none;" id="enrollButtonContainer">
                	<button class="btn btn-success btn-sm" onclick="enrollCourses();return false;">
                		Enroll
                	</button>
                </div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- penalityPayedStudentsContainer -->
<!-- paymentStatusContainer -->
<!-- enrollButtonContainer -->

<?php

$this->registerJs(
	"

	eCourseList = [];
	for1 = null;
	window.enrollCourses = function(){
		if (eCourseList.length) {
			// 
			console.log(typeof for1);
			if(for1 == ''|| typeof for1 == 'undefined'){
				alert('Uknown User');
			}else{
				eCourseListString = JSON.stringify(eCourseList);
				$.post('/penality-registartion/enroll-student-courses',
				{
					eCourseListString:eCourseListString,
					studentId:for1
				}).done(function(data){
					$('#enrollButtonContainer').hide();
					if(data == 1){
						alert('Enroll successful.');
					}else{
						$('#paymentStatusContainer').html(data);
					}
				});
			}
			}else{
				alert('Course List empty!');
			}
	}
	window.addEnroll = function(courseId,coffId){
		// Add/remove the Courseid from the Courselist
        if(jQuery.inArray(coffId,eCourseList) !== -1){ // if item is already selected, remove it because user deselected it.
            eCourseList.splice($.inArray(coffId,eCourseList),1);
        }else{//going to add the item to the list.
            eCourseList.push(coffId);
        }

        if (eCourseList.length) {
            $('#enrollButtonContainer').show();
        }else{
            $('#enrollButtonContainer').hide();
        }

        console.log(eCourseList);
	}
	window.enrollCourseFor = function(studentId,gsetId){
		eCourseList = [];
		for1 = null;
		$('#enrollButtonContainer').hide();
		$('#paymentStatusContainer').html('Loading courses...');
		$.post('/penality-registartion/get-student-course',
			{studentId:studentId,gsetId:gsetId})
			.done(function(data){
				$('#paymentStatusContainer').html(data);
				for1 = $('#wonde_to_ID').val();
			});
	}

	window.loadPayedStudents = function(){
		$('#penalityPayedStudentsContainer').html('Loading Students...');
		$.post('/penality-registartion/get-payed-students',{}).done(function(data){
			$('#penalityPayedStudentsContainer').html(data);
			});
	}



	loadPayedStudents();
	",
	$this::POS_READY);

?>