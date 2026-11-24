<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Drop Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="conatiner-fluid">
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="row" style="text-align: center;">
					<div class="col-md-2 form-group">
						<label for="AcadYearSelect">Academic Year</label>
						<select class="form-control" id="AcadYearSelect" onchange="loadReport()">
							<option> - </option>
						</select>
					</div>
					<div class="col-md-2 form-group">
						<label for="SemesterSelect">Semester</label>
						<select class="form-control" id="SemesterSelect"  onchange="loadReport()">
							<option> - </option>
						</select>
					</div>
					<div class="col-md-2 form-group">
						<label for="admissionSelect">Admission</label>
						<select class="form-control"  onchange="loadReport()" id="admissionSelect">
							
						</select>
					</div>
					<div class="col-md-2 form-group">
						<label for="programSelect">Program</label>
						<select class="form-control"  onchange="loadReport()" id="programSelect">
							
						</select>
					</div>
					<div class="col-md-2 form-group">
						<label for="SemesterSelect">Filter</label>
						<select class="form-control"  onchange="loadReport()">
							<option title="Filter All requests" value="ALL">All</option>
							<option title="Filter Approved requests" value="APPROVED">Approved</option>
							<option title="Filter Pending requests" value="PENDING">Pending</option>
							<option title="Filter Rejected requests" value="REJECTED">Rejected</option>
						</select>
					</div>

					<div class="col-md-2 form-group"><br>
						<button class="btn btn-success btn-sm" onclick="loadReport();return false;">Load report</button>
					</div>
				</div>
			</div>
			<i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('printableDiv')">Print<span class="glyphicon glyphicon-print"></span></i>
			<div class="panel-body" style="border: 1px dashed black;" id="printableDiv">
				<h4 style="text-align: center; text-decoration: underline;">COURSE DROP REPORT</h4>
					<p style="text-align: center;">
						Admission: <span id="spanAdmission" style="font-style: italic; text-decoration: underline;">-</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Program:<span style="font-style: italic; text-decoration: underline;" id="spanProgram">-</span>
					</p>

					<p style="text-align: center;">
						Academic Year: <span id="spanAcaadYear" style="font-style: italic; text-decoration: underline;">-</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						Semester:<span style="font-style: italic; text-decoration: underline;" id="spanSemester">-</span>
					</p>
				<div class="conatiner-fluid table-responsive" id="reportContainer">
					
				</div>
			</div>
		</div>
	</div>
</div>

<!-- AcadYearSelect -->
<!-- SemesterSelect -->
<!-- reportContainer -->
<!-- printableDiv -->

<?php

$this->registerJs(
	"

	window.printCont = function(div){
		var restorePage = $('body').html();
        var printContent = $('#'+div).html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
        $('.non-print').show();
	}
	window.loadReport = function(){
		acadYearId=$('#AcadYearSelect').val();
		semId=$('#SemesterSelect').val();
		adm = $('#admissionSelect').val();
		prog = $('#programSelect').val();

		$('#spanAcaadYear').text($('#AcadYearSelect :selected').html());
		$('#spanSemester').text($('#SemesterSelect :selected').html());

		$('#spanAdmission').text($('#admissionSelect :selected').html());
		$('#spanProgram').text($('#programSelect :selected').html());
		
		// Load Add report for selected acdemic year and semester.
		$.post('/course-drop-report/get-report-table',{

			acadYearId:acadYearId,
			semId:semId,
			adm:adm,
			prog:prog

			}).done(function(data){
			$('#reportContainer').html(data);
			$('#printIcon').show();
		});
	}


	window.loadAcads = function(){
		$('#printIcon').hide();
		// load AcadYears and Semesters;
		$.post('/course-drop-report/get-acads',{}).done(function(data){
			try{
				dd = JSON.parse(data);
				$('#AcadYearSelect').html(dd['acadYear']);
				$('#SemesterSelect').html(dd['sem']);

				$('#admissionSelect').html(dd['adm']);
				$('#programSelect').html(dd['prog']);
			}catch(e){
				$('#reportContainer').html(data);
			}
		});
	}
	loadAcads();
	",
	$this::POS_READY);

?>