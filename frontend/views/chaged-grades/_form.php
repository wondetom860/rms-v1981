<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGrades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touched-grades-form row" style="font-size: 10pt;">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-6" style="border-right: 2px dashed black">
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
    <div class="col-md-6" style="background-color: #78F; margin-top: 20px; padding: 
    5px;">
        <div class="row">
            <div class="col-md-12">
                <?php echo Html::activeHiddenInput($model, "tg_semreg_relId",['class'=>'hidden_input_2']);?>
                <?php echo Html::activeHiddenInput($model, "tg_srd2_relId",['class'=>'hidden_input_3']);?>
                <?php echo Html::activeHiddenInput($model, "tg_beforeGrade_relId",['class'=>'hidden_input_4']);?>

                <?= $form->field($model, 'tg_afterGrade_relId')->dropdownList([],['prompt'=>'Select Grade']) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'tg_reason')->textarea(['rows' => 3]) ?>
            </div>
            
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs(
    "

    window.loadSrd2Info = function(srd2Id){
        loadgradeScale();
        $('#touchedgrades-tg_srd2_relid').val(srd2Id);
        $('#hidden_input_4').val(0);
        $('#hidden_input_4').val(0);
        return false;
    }

    window.loadgradeScale = function(){
        acadYearId = $('#touchedgrades-tg_acadyear_relid').val();
        semId = $('#touchedgrades-tg_sem_relid').val();
        progId = $('#touchedgrades-tg_prog_relid').val();

        $.post('/chaged-grades/get-list',
        {
            acadYearId:acadYearId,
            semId:semId,
            progId:progId,
            clslvlId:clslvlId

            }).done(function(data){
              $('#touchedgrades-tg_aftergrade_relid').html(data);
              // $('#printIcon').show();
        });

    }
    window.LoatStudents = function(){
        acadYearId = $('#touchedgrades-tg_acadyear_relid').val();
        semId = $('#touchedgrades-tg_sem_relid').val();
        progId = $('#touchedgrades-tg_prog_relid').val();
        clslvlId = $('#touchedgrades-tg_clslvl_relid').val();

        $('#studentSemesterStatusContainer').html('Loading result...');

        $.post('/chaged-grades/get-student-list',
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
