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
		</div>
		<div class="panel-body">
			<h4 style="text-align: center;">Department Changes made for new registered students of this academic year.</h4>
			<div class="container-fluid" id="new_changed_student_info_container">
				
			</div>
		</div>
	</div>
</div>

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
 		$.post('/approve-admission/get-approved-records',{}).done(function(data){
 			d = JSON.parse(data);
 			$('#new_student_info_container').html(d[0]);
 			$('#new_changed_student_info_container').html(d[1]);
 		});
 	}


 	loadAdmittedStudents();
 	",
 	$this::POS_READY);

 ?>