<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ClearanceAllowedStudent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clearance-allowed-student-form">

    <div class="row">
        <div class="col-md-6" style="border:1px solid black; padding: 5px;">
            <div class="container-fluid" style="padding: 5px; font-size: 10pt; border-bottom: 1px solid black">
                <?= $this->render('includes/__selectForm') ?>
            </div>
            
            <div class="container-fluid" id="listContainer" style="border-bottom: 1px solid black; font-size: 10pt; height: 399px; overflow-y: auto;">
                
            </div>
        </div>
        <div class="col-md-6" id="formContaier">
            
        </div>
    </div>

    <script type="text/javascript">
        window.addStudent = (semregId) =>{
            $('#formContaier').html('Loading Form...');
            $.post('/clearance-allowed-student/get-clearance-form',{semregId:semregId}).done((data) => {
                $('#formContaier').html(data);
            });
        }
    </script>
</div>
