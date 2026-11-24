<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGrades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touched-grades-form row" style="font-size: 10pt;">
	<h2>Approve Grade Changes </h2>
    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-9" style="border-right: 2px dashed black">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'tg_acadYear_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),['prompt'=>'Select Academic Year','onChange' => 'LoatStudents();']) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tg_sem_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),['prompt'=>'Select Semester','onChange' => 'LoatStudents();']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($model, 'tg_prog_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),[
                    'prompt'=>'Select Program',
                    'onChange'=>' 
                        $.post( "/program/clslvllists",
                            {
                                progId:$(this).val()
                            }, 
                          function( data ) {
                              //console.log("Returned: "+data);
                              $("#touchedgrades-tg_clslvl_relid" ).html( data );
                              LoatStudents();
                              // loadgradeScale();
                            }); ',
                ]) ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'tg_clslvl_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\ClassLevel::find()->all(),'id','clslvl_name'),['prompt'=>'Select Class Level','onChange' => 'LoatStudents();']) ?>
            </div>
            <div class="col-md-12">
                <div class="container-fluid" style="height: 300px; overflow-y: auto; background-color: #8D5; padding: 10px;" id="studentsList_container">
                    
                </div>

            </div>
            
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs(
    "

    window.ApproveRequest = function(requestId,option){
    	if(confirm('Are you sure to change request status?')){
            url = option == 1 ? '/chaged-grades/approve-grade-change' : '/chaged-grades/reject-grade-change';
    		$.post(url,{reqId:requestId,option:option}).done(function(data){
    		if(data == 1){
    			window.location.href = window.location;
    		}else{
    			alert(data);
    		}
    	});
    	}
    }
    window.LoatStudents = function(){
        acadYearId = $('#touchedgrades-tg_acadyear_relid').val();
        semId = $('#touchedgrades-tg_sem_relid').val();
        progId = $('#touchedgrades-tg_prog_relid').val();
        clslvlId = $('#touchedgrades-tg_clslvl_relid').val();

        $('#studentSemesterStatusContainer').html('Loading result...');

        $.post('/approve-grade-change/get-request-list',
        {
            acadYearId:acadYearId,
            semId:semId,
            progId:progId,
            clslvlId:clslvlId

            }).done(function(data){
              $('#studentsList_container').html(data);
              // $('#printIcon').show();
        });
    }
    ",
    $this::POS_READY);

?>
