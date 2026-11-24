<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AddHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="add-history-form table-responsive">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row table-responsive">
        <div class="col-md-12 table-responsive container-fluid" id="courses_for_add" style="background-color:#FFF; font-size: 9pt;">
            
        </div>
        <div class="col-md-12">
            
        </div>
    </div>

    <?php $this->registerJs(
        "

        window.requestAdd = (offeringDetId,stdId)=>{
            if(confirm('You want to add course with this group?')){
                $.post('/course-add/create2',{offeringDetId:offeringDetId,stdId:stdId}).done((reply) => {
                    if(reply == 1){

                    }else{
                        alert(reply)
                    }  
                    laodCoursesForAdd();
                });
            }
        }
        window.laodCoursesForAdd = () => {
            $('#courses_for_add').html('getting course....');
            $.post('/add-history/get-courses-for-add',{}).done((reply) => {
                $('#courses_for_add').html(reply);
            });
        }

        laodCoursesForAdd();
        ",
        $this::POS_READY); ?>
    <?php ActiveForm::end(); ?>

</div>
