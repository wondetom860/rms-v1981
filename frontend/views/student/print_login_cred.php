<?php 

if ($models && count($models) > 0) {
	$content = prepareContent($models);
	echo "
		<div class='container-fluid'>
			<div class='row'>
				{$content}
			</div>
		</div>
	";
}


function prepareContent($models){
	$rep = "";
	foreach ($models as $keyModelStudent) {
		$rep .= "<div class='col-md-3' style='border: 1px dashed black; padding:5px; height:90px;'>
				<p style='border: 1px solid black; border-radius: 10px; padding:5px;'>
					{$keyModelStudent->getFullName()}<br>{$keyModelStudent->std_regId}<br>{$keyModelStudent->stdUserRel->username}
				</p>
			</div>";
	}

	return $rep;
}
?>