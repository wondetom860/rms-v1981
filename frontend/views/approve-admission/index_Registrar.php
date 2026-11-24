<?php


use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\User;
use backend\models\StudentType;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Admitted Students');
$this->params['breadcrumbs'][] = $this->title;

?><br>

<div class="container-fluid">
	<div class="panel panel-info">
		<div class="panel-heading" style="text-align: center;">
			<!-- <h4 class="bg-success">Admitted Students for Academic year of <span style="font-weight: bold;"><?= Yii::$app->params['C']['Y'] ?></span></h4> -->
		</div>
		<div class="panel-body">
			<div class="container-fluid table-responsive" id="new_student_info_container" style="height: auto; max-height: 450px overflow-y: auto;">
				
			</div>
			<div class="container-fluid">
				<div class="row" style="background-color: #AF8;">
					<div class="col-md-10">
						<span id="selectedStudCount" style="font-size: 9pt; font-style: italic; float: right;"></span>
					</div>
					<div class="col-md-2 pull-right">
						<button class='btn btn-sm btn-success' class="pull-right" style="float: right;" onclick='setStudetntDept()' title='Assign selected studets to the department'>Assign</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

 Modal::begin([
    'header'=>'<h4 class="bg-success" id="modal_header">Student Admission Completed!!</h4>',
    'id'=>'modal_success',
    'size'=>'modal-sm',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
 echo "<div id='modalFooter' class='container-fluid'><button class='btn btn-sm btn-warning' onclick='submitUserInfo(0)'>Cancel Admission</button></div>";
 Modal::end();
 ?>

 <?php

Modal::begin([
    'header'=>'<h4 class="bg-default" id="modal_header1">Student Admission Completed!!</h4>',
    'id'=>'modal_report',
    'size'=>'modal-lg',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
            'justify-content'=>'left',
            'align-content'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent1' class='jumbotron'>Loading user account form...</div>";
 echo "<div id='modalFooter1' class='container-fluid'><button class='btn btn-sm btn-warning' onclick='submitUserInfo(0)'>Cancel Admission</button></div>";
 Modal::end();
 ?>


<!-- 
new_student_info_container
new_changed_student_info_container
 -->


 <?php

 $this->registerJs(
 	"

 	stdList = [];
 	deptId = null;

 	window.changeBack = function(stdId,action){
 		aa = action == 0?'back-dept': 'back-activation' ;

 		$.post('/approve-admission/'+aa,{stdId:stdId}).done(function(data){
 			if(data == 1){
 				loadAdmittedStudents();
 			}else{
 				$('#modal_success').modal('show');
 				$('#modalContent').html(data);
 			}
 		});
 	}

 	window.loadApplicationForm = function(userId){

 		$.get('/admit-student/admission-form-report',{id:userId}).done(function(data){
 			$('#modal_header1').html('Admission Application Form');
 			$('#modal_header1').parent().css('background-color','#8D0');
 			$('#modal_report').modal('show');
 			$('#modal_report').css('justify-content','left');
 			// $('#modal_success').attr('size') = 'modal-lg';
 			$('#modalFooter1').hide();
 			$('#modalContent1').html(data);
 		});
 	}

 	window.approveAdmission = function(stdId,valid){
 		if(confirm('Are you sure of approving this admission?')){
 			valid = valid== 0?2:valid;
 			$.post('/approve-admission/approve-admission-reg',
 			{
 				stdId:stdId,
 				valid:valid

 			}).done(function(data){
 				try{
 					d = JSON.parse(data);
 					if(d[0] == 1){
						alert('User Name : '+d[1]);
						loadAdmittedStudents();
					}else{
						$('#selectedStudCount').html(data);
					}
 				}catch(e){
 					$('#selectedStudCount').html(data);
 				}
 			});
 		}
 	}
 	window.setStudetntDept = function(stdId,deptId){
 		// consle.log(stdId,deptId);
 		deptId = $('#selected_department :selected').val();
 		if(deptId != undefined){
 			if(confirm('Are You sure for this department change?')){
 				$.post('/approve-admission/change-department',
 				{
 					deptId:deptId, 
 					stdList:JSON.stringify(stdList)

 				}).done(function(data){
 					if(data == 1){
 						loadAdmittedStudents();
 					}else{
 						$('#selectedStudCount').html(data);
 					}
 				});
 			}
 		}
 	}



 	window.addToList = function(stdId){
 		// 
 		// 

 		if(jQuery.inArray(stdId,stdList) !== -1){ // if item is already selected, remove it because user deselected it.
            stdList.splice($.inArray(stdId,stdList),1);
        }else{//going to add the item to the list.
            stdList.push(stdId);
        }
 		// console.log(stdList);
 		$('#selectedStudCount').html(stdList.length+' Student(s) selected.');
 	}

	// action-
 	window.loadAdmittedStudents = function(){
 		stdList = [];
 		deptId = null;
 		$.post('/approve-admission/get-records',{}).done(function(data){
 			d = JSON.parse(data);
 			$('#new_student_info_container').html(d[0]);
 			// $('#new_changed_student_info_container').html(d[1]);
 		});
 	}


 	loadAdmittedStudents();
 	",
 	$this::POS_READY);

 ?>