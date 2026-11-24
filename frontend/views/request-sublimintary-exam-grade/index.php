<?php
/** @var yii\web\View $this */
$this->title = "Supplementary Examination requests..";
?>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4><?= $this->title ?></h4>
		</div>
		<div class="panel-body">
			<div class="container-fluid" id="serequestContainer">
				
			</div>
		</div>
	</div>
</div>
<?php
$this->registerJs(
	"
	window.loadrequests = function(){
		$.post('/request-sublimintary-exam-grade/get-request-report',{})
		.done(function(data){
			$('#serequestContainer').html(data);
		});
	}
	loadrequests();
	",
	$this::POS_READY);
?>

