<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel app\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'My Grades');
$this->params['breadcrumbs'][] = $this->title;
?>
<hr>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12 col-sm-12 col-lg-12">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4>Grade Reports per semester</h4>
				</div>
				<div class="panel-body">
					<div class="container-fluid">
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 1 Semster - I
									</div>
									<div class="panel-body">
										<div class="container-fluid"  id="1_I">
											<button onclick="loadSemesterGrade(1,1);">
												Load Grade report 1_1
											</button>
										</div>
									</div>				
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 1 Semster - II
									</div>
									<div class="panel-body">
										<div class="container-fluid"  id="1_II">
											<button onclick="loadSemesterGrade(1,2);">
												Load Grade report 1_1
											</button>
										</div>
									</div>				
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 2 Semster - I
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="2_I">
											<button onclick="loadSemesterGrade(2,1);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 2 Semster - II
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="2_II">
											<button onclick="loadSemesterGrade(2,2);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 3 Semster - I
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="3_I">
											<button onclick="loadSemesterGrade(3,1);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>

							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 3 Semster - II
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="3_II">
											<button onclick="loadSemesterGrade(3,2);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 4 Semster - I
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="4_I">
											<button onclick="loadSemesterGrade(4,1);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>

							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 4 Semster - II
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="4_II">
											<button onclick="loadSemesterGrade(4,2);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading">
										Year - 5 Semster - I
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="5_I">
											<button onclick="loadSemesterGrade(5,1);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>

							<div class="col-md-6">
								<div class="panel panel-primary">
									<div class="panel-heading" style="background-color: #dd9;">
										Last Semester - You are GC @ here
									</div>
									<div class="panel-body">
										<div class="container-fluid" id="5_II">
											<button onclick="loadSemesterGrade(5,2);">
												Load Grade report
											</button>
										</div>
									</div>				
								</div>
							</div>
						</div>
					</div>
				</div>				
			</div>
		</div>
		<!-- <div class="col-md-6 col-sm-12 col-lg-6">
			<div class="panel panel-primary">
				<div class="panel-heading">
					
				</div>
				<div class="panel-body">
					
				</div>				
			</div>
		</div> -->
	</div>
</div>

<?php
	
$this->registerJs(
	"
	window.loadSemesterGrade = function(clslvl,semester){
		//alert('Ready');
		$.post('/my-grades/get-semester-grades',
			{
				clslvl:clslvl,
				semester:semester,

			}).done(function(data){
				try{
					//dd = JSON.parse(data);
					rData = JSON.parse(data);
					classlevel = rData['clslvl'];
					semester = rData['semester'];
					gradeTable = rData['gradeTable'];

					$('#'+classlevel+'_'+semester).html(gradeTable);
				}catch( e){
					semester = semester==1?'I':'II';
					$('#'+clslvl+'_'+semester).html(data);
					// $('#1_I').html(data).focus();
				}
			//console.log('Class Level: '+rData[0]);
			
		});
	}

	// loadSemesterGrade(0,0);
	",
	$this::POS_READY);

?>