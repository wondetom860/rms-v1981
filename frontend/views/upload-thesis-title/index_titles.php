<?php



?>

<div class="container-fluid">
	<?php
	if (isset($titles)) {
		if ($titles && count($titles) > 0) {
			$c = 0;
			foreach ($titles as $Tkey) {
				$file = file_get_contents("uploads/thesisTitles/".$Tkey->tt_title);
				$sts = $Tkey->tt_approved ? 'success':'warning';
				$btn_aprove = $Tkey->tt_approved ? 
					"<button class='btn btn-xs btn-warning pull-right' onclick='changeStatus({$Tkey->id},2)'>Reject</button>":
					"<button class='btn btn-xs btn-success pull-right' onclick='changeStatus({$Tkey->id},1)'>Approve</button>";
				echo "<div class='alert alert-{$sts}'>";
				echo ++$c.". {$file} {$btn_aprove}<br>";
				echo "</div>";
			}
		}else{
			echo "<p class='bg-error'>No title submitted yet</p>";
		}
	}
	?>
</div>