<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluation2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Approve Course Grade');
$this->params['breadcrumbs'][] = $this->title;

?>
<hr>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					Approve Grades submitted
				</div>
				<div class="panel-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-12">
								<div class="container-fluid" id="deptGroupsContainer" style="max-height: 400px; overflow-y: scroll;">
									Loading....
								</div>
							</div>
						</div>
					</div><hr>
					<div class="container-fluid row">
						<div class="col-md-6 col-sm-12 col-lg-6">
							<div class="container-fluid" id="courseGradeContainer" style="display: none;">
						
							</div>
						</div>
						<div class="col-md-6 col-sm-12 col-lg-6">
							<div class="container-fluid" id="approvedCourseGradeContainer" style="display: none;">
						
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- approvedCourseGradeContainer -->
<?php

$this->registerJs(
	"
	course = null;
	group = null;

	approvedStudents = [];

	window.dropGradeApproval = function(courseId,gsetId,studentId){
		$.post('/approve-grade/drop-grade-approval',{course:courseId,group:gsetId,student:studentId}).done(function(data){
			if(data == 1){
				LoadCourseGrade(course,group);
			}
			else{
				$('#approvedCourseGradeContainer').html(data).show();
			}
		});
	}

	window.saveOperation = function(){
		if(approvedStudents.length > 0){
			total = $('.checkForApprove').length;
			selected = approvedStudents.length;

			if(confirm('Approve Grades for '+selected+'/'+total+' students.')){

				approvedStudentsString = JSON.stringify(approvedStudents);

				$.post('/approve-grade/save-status-change',
					{
						course:course,
						group:group,
						students:approvedStudentsString

					}).done(function(data){
						if(data == 1){
							LoadCourseGrade(course,group);
						}else{
							$('#approvedCourseGradeContainer').html(data).show();
						}
				});
			}
		}else{
			alert('No One selected.');
			LoadCourseGrade(course,group);
		}
	}

	window.approvedGrade = function(studentId){
		if(jQuery.inArray(studentId,approvedStudents) !== -1){ // if item is already selected, remove it because user deselected it.
            approvedStudents.splice($.inArray(studentId,approvedStudents),1);
        }else{//going to add the item to the list.
            approvedStudents.push(studentId);
        }

        //console.log(approvedStudents);
        $('#222').prop('checked',(approvedStudents.length != 0));

        //console.log('Checked count: '+$('.checkForApprove').prop('checked').length);

	}

	window.LoadCourseGrade = function(courseId,gsetId){
		course = courseId;
		group = gsetId;
		$('#courseGradeContainer').html('Loading Submitted Grades....').show();

		$.post('/approve-grade/get-course-grade-for-group',
		{
			course:course,
			group:group
			}).done(function(data){
			$('#courseGradeContainer').html(data);
			$('#approvedCourseGradeContainer').html('Loading Approved Grades....').show();
			$.post('/approve-grade/get-approved-course-grade-for-group',{course:course,group:group}).done(function(data){
				$('#approvedCourseGradeContainer').html(data).show();
				})
			})
	}
	window.loadGroupCourses = function(gsetId){
		group = $('#deptGroups').val();
		//console.log(group);
		$.post('/approve-grade/get-group-courses',{group:gsetId}).done(function(data){
			$('#groupCoursesContainer').html(data);
			LoadCourseGrade(0);
			})
	}
	$('#deptGroupsContainer').html('Loading...');
	$.post('/approve-grade/approve-grade-submission',{},
	function(data){
		$('#deptGroupsContainer').html(data);
	});
	loadGroupCourses();
	",
	$this::POS_READY);

?>