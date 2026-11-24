<?php 
	use yii\helpers\Html;
	use yii\bootstrap\Modal;
	$modleSemreg = $model;
 ?>

<div class="row">
  	<div class="col-xs-12">
		<h5 class="page-header bg-info"> Semester Registrations</h5>
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive" >
	<?php if ($modleSemreg && count($modleSemreg) > 0) { ?>
		<small class="pull-right bg-info"> <?= $modleSemreg[0]->semregStdRel->getFullName() ?></small>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover">
			<thead style="background-color: #DFD;">
				<th>#</th>
				<th>Class Level</th>
				<th>Semester</th>
				<th>Academic Year</th>
				<th>SGPA</th>
				<th>CGPA</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				$sgpa = " - ";
				$cgpa = " - ";
				$btnDelete = "";
				$btnMore = "";
				foreach ($modleSemreg as $semreg) {
					$btnDelete = "Delete";
					$btnMore = "<button class='btn btn-warning btn-xs' title='Remove this enrollment' onclick='showMore({$semreg->id});return false;'>Remove</button>";

					if ($semreg->semreg_gradeCompleted == 1) {
						$sgpa = sprintf("%0.2f",$semreg->semreg_SGPA);
						$cgpa = sprintf("%0.2f",$semreg->semreg_CGPA);
					}else{
						$sgpa = " - ";
						$cgpa = " - ";
					}
					?>
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $semreg->semregClslvlRel->getClslvlDisp() ?></td>
						<td><?= $semreg->semregSemesterRel->semester_name ?></td>
						<td><?= $semreg->semregAcadYearRel->sch_year_desc ?></td>
						<td><?= $sgpa ?></td>
						<td><?= $cgpa ?></td>
						<td><?= $btnMore ?></td>
					</tr>
					<?php
				}?>
			</tbody>
		</table>
		<script type="text/javascript">
			window.showMore = (semregId) => {
				$('#modal').find('.modal-header').html('More on student enrollment...');
	            $('#modalContent').css('text-align','left');
	            $('#modalContent').html('Loading data...');
	            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
	            $('#modal').modal('show');
	            $.post('/remove-semester-registration-entry/get-more-on-enrollment',{semregId:semregId}).done(function(data){
	                $('#modalContent').html(data);
	            });


				// $("#more_detail_container").html("Loading confirmation...");
				// $.post('/remove-semester-registration-entry/get-more-on-enrollment',{semregId:semregId}).done((resp) => { $("#more_detail_container").html(resp); });
			}
		</script>
		<?php 
	}else{
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading form...</div>";
    Modal::end();
?>

