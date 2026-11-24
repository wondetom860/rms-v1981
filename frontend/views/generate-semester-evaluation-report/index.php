<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluation2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation Report');
$this->params['breadcrumbs'][] = $this->title;

?>
<br>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="container-fluid" id="selectionFormContainerDiv">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h4>Generate Semester Evaluation Report</h4>
					</div>
					<div class="panel-body">
						<div class="container-fluid">
							<div class="container" id="courseSelectionContainer">
								Loading your courses...
							</div><hr>
							<i id="printIcon" style="cursor: pointer; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
						</div>
						<div class="container-fluid" id="selectionFormContainer" style="border: 2px dashed black; padding: 10px;">
							Loading Form...
						</div>
					</div>
				</div>
			</div><hr>
			<div class="container-fluid" id="reportContainerDiv"  style="display: none;">
				<div class="panel panel-primary">
					<div class="panel-heading">
						<h5>Semester Evaluation Report</h5>
					</div>
					<div class="panel-body">
						<div class="container-fluid" id="reportContainer"  style="border: 3px dashed black;">
							Loading Form...
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- selectionFormContainer -->
<!-- reportContainer -->

<?php

$this->registerJs(
	"

	window.printCont = function(el){
        // console.log(el);
        var restorePage = $('body').html();
        var printContent = $('#selectionFormContainer').html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
    }

    window.loadInstCourses = function(){
    	$.post('/generate-semester-evaluation-report/get-inst-course',{},function(data){
			$('#courseSelectionContainer').html(data);
			});
    }


	//$('#selectionFormContainer').html('Loading semester Evaluation report.....');

	window.loadEvaluationReport = function(courseOfferingDetailId){
		$.post('/generate-semester-evaluation-report/get-report',{coffDetId:courseOfferingDetailId},function(data){
			$('#selectionFormContainer').html(data);
			});
	}

	//loadEvaluationReport();
	loadInstCourses();
	",
	$this::POS_READY);

?>