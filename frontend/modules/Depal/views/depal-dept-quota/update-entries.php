<?php 

// $setting = $setting
$models = $setting->depalDeptQoutas;

if ($models && count($models) > 0) {
	echo "<div class='panel panel-default'>
			<div class='panel-heading'><h4>{$setting->getSettingHeader()}</h4></div>
			<div class='panel-body'>";
			$stotal = 0;
	foreach ($models as $model) {
		echo "{$model->deptRel->dept_name} : <input type='number' data-qeid='{$model->id}' data-settingid='{$setting->id}' scale='1' min='0' class='form-control form-{$setting->id}' id='qouta_{$model->id}' value='{$model->q_percentage}' onchange='computeTotal($setting->id); return false;' /><br>";
		$stotal += $model->q_percentage;
	}
	echo "
			<p class='bg-info' id='poo_{$setting->id}'>Total: {$stotal}</p>
		</div>
		<div class='panel-footer'>
			<p id='poo_{$setting->id}'></p>
			<button class='btn btn-xs btn-primary' id='btnoo_{$setting->id}' onclick='updateEntries({$setting->id});return false;'>Update</button>
			<span class='bg-warning pull-right' id='spanoo_{$setting->id}' style='font-size:8pt; font-style: italic;'></span>
		</div>";
}

 ?>

 <script type="text/javascript">
 	// $("#poo_"+settingId)
 	const computeTotal = (settingid) => {
 		// quota_set = fetchInputs(settingid)[1];
 		sum_total = 0;
 		// console.log(quota_set);
 		$.each($('.form-'+settingid),function(){
 			sum_total += Number($(this).val());
 			// console.log(sum_total);
 			if (sum_total !== 100) {
 				$("#spanoo_"+settingid).text('Quota percentage sum is not 100%');
 				$("#btnoo_"+settingid).addClass('btn-disabled');
 			}else{
 				$("#spanoo_"+settingid).text('');
 				$("#btnoo_"+settingid).removeClass('btn-disabled');
 			}
 			$("#poo_"+settingid).text("Total: "+sum_total+"%"); 			
 		});
 		// console.log(sum_total);
 		return sum_total;
 	}
 	const updateEntries = (settingid) => {
 		quota_set = fetchInputs(settingid);
 		sum_total = computeTotal(settingid);
 		if (sum_total !== 100) {
 			$("#spanoo_"+settingid).text('Please check quota percentage allotment.');
 		}else{
 			$.post('/Depal/depal-dept-quota/update-quota',{settingid:settingid,data_set: JSON.stringify(quota_set)}).done((resp) => {
				if (resp == 1) {
					window.location.href = window.location;
				}else{
					$("#spanoo_"+settingid).text(resp);
					alert(resp);
				}
 			});
 		}
 	}

 	const fetchInputs = (settingid) => {
 		quota_set = {};
 		data_input = {};
 		$.each($('.form-'+settingid),function(){
 			data_input[$(this).data('qeid')] = $(this).val();//.push(,)
	    });
	    quota_set[settingid]=data_input;
	    return quota_set;
 	}
 </script>