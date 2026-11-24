<?php
/** @var yii\web\View $this */

$this->title = "School Year Bach info";
if (isset($modelSchYear) && count($modelSchYear) > 0) {
	
}else{
	throw new \yii\web\NotFoundHttpException("School Year Bach Information Empty!", 1);
	
}
?>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-12">
			<div class="panel panel-warning">
				<div class="panel-heading">
					School Year Batch Information
				</div>
				<div class="panel-body">
					<table class="table table-bordered table-hover table-condensed">
						<thead style="background-color: #8DF">
							<th>#</th>
							<th>Academic Year</th>
							<th>Batch</th>
							<th>Option</th>
						</thead>
						<tbody>
							<?php
							$count = 0;
							foreach ($modelSchYear as $SCkey) {
								// check if student admitted in that academic Year::
								$cc = $SCkey->studentAdmitted($SCkey->id);
								// var_dump($cc);die();
								$btn = !($cc) ? "<button class='btn btn-warning btn-xs' onclick='LoadForm({$SCkey->id})'>Set Batch #</button>":'Student Admitted in this Batch';
								$input = "<input value='{$SCkey->sch_bach_number}' class='form-xs' type='number' scale='1' id='input_{$SCkey->id}' min='1'/>";
								$btn2 = "<button class='btn btn-primary btn-xs' onclick='SaveForm({$SCkey->id})'>Save</button>";
								echo "<tr>
									<td>".++$count."</td>
									<td>{$SCkey->sch_year_desc}</td>
									<td>{$SCkey->sch_bach_number}</td>
									<td id='tr_{$SCkey->id}' style='display:none;'>{$input} {$btn2}</td>
									<td>{$btn}<span id='span_{$SCkey->id}'></span></td>
								</tr>";
							}

							?>
						</tbody>
					</table>
				</div>			
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.LoadForm = function(id){
		$('#tr_'+id).toggle('display');
	}

	window.SaveForm = function(id){
		val = Number($('#input_'+id).val());

		console.log(val);
		
		$.post('/batch-info/go',{id:id,batchNum:val}).done((data)=>{
			data == 1 ? window.location.href = window.location : $('#span_'+id).html(data);
		});
	}
</script>
