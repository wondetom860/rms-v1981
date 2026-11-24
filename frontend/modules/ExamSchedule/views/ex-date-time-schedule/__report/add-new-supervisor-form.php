<?php
if(isset($model)){
    // show form here
    echo $this->render('_supervisor-form', [
        'model' => $model,
    ]);
    // show report here
    echo $this->render('_supervisor', [
        'model' => $model,
        'addSupervisorBtn' => false,
    ]);
}

?>