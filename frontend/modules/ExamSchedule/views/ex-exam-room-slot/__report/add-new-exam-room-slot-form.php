<?php
if(isset($model)){
    // show form here
    echo $this->render('../_form', [
        'model' => $model,
    ]);
    echo "<hr>";
    // show report here
    echo $this->render('exam-room-slots', [
        'model' => $modelDateTimeSlot,
        'addExamRoomSlotBtn' => false,
    ]);
}

?>