<?php 

$rooms = \frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->where(['status' => 1])->all();

if ($rooms && count($rooms) > 0) {
	echo "<select class='form-control' id='avaliabe_room' title='Choose Class room'>";
	foreach ($rooms as $room) {
		echo "<option value={$room->id} title='{$room->description}'>{$room->room}</option>";
	}
	echo "</select>";
}else{
	echo "<p class='bg-error'>No class room registered yet.</p>";
}
?>