<?php

$this->title = 'About Developers';

if (isset($id)) {
	$t = "<table class='table'>";
	if ($id == 'W') {
		$t .= "<tr>
				<td styele='vartical-align:middle'>
					<img width='100px' height='120px' src='\uploads\photo\devs\img1.jpg' alt=''>
				</td>
				<td>
					Name : Wondemu Toma
					<br>Email : wondemutoma@gmail.com
					<br>Phone : +251 92 623 8472
				</td>
			</tr>";
	}else if($id == 'H'){
		$t .= "<tr>
				<td styele='vartical-align:middle'>
					<img class='img' width='100px' height='120px' src='\uploads\photo\devs\img2.png' alt=''>
				</td>
				<td>
					Name : Hailemariam Nigussie
					<br>Email : ______
					<br>Phone : +251 91 282 8837
				</td>
			</tr>";
	}
	$t .= "</table>";

	echo $t;
	return;
}else{
	echo "<p class='alert alert-error'>Information NOT available</p>";
	return;
}
?>


