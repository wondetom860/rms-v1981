<?php
if(isset($model)){
    // show form here
    echo $this->render('_offering-form', [
        'model' => $model,
    ]);
    echo "<hr>";
    // show report here
    echo $this->render('offering-detail', [
        'model' => $model,
        'addOfferingDetailBtn' => false,
    ]);
}

?>