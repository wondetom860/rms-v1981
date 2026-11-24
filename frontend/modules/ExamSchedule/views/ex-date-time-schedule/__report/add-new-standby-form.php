<?php
if(isset($model)){
    // $model->start_date_str = date('d-m-Y', $model->start_date);
    // $model->end_date_str = date('d-m-Y', $model->end_date);

    // show form here
    echo $this->render('_stdby-form', [
        'model' => $model,
    ]);
    // show report here
    echo $this->render('_standby', [
        'model' => $model,
        'addStandbyBtn' => false,
    ]);
}

?>