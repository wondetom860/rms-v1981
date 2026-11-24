<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterRegistrationControld */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration-controlac-form">

    <?php $form = ActiveForm::begin(['class'=>'sm']); ?>
    <div class="container-fluid sm">
        <div class="row">
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Control Semester Registration 
                    </div>
                    <div class="panel-body">
                        
                        <?= $form->field($model, 'src_adm_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                            [
                                'prompt'=>'Select Admission',
                                
                            ]
                        ) ?>

                        <?= $form->field($model, 'src_prog_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                            [
                                'prompt'=>'Select Program',
                                'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('program/clslvllists').'",
                                    {
                                        progId:$(this).val()
                                    }, 
                                    function( data ) {
                                    //console.log("Returned: "+data);
                                  $( "select#semesterregistrationcontrold-src_clslvl_relid" ).html( data );
                                });
                                // coffLoad();',
                            ]
                        ) ?>

                     

                        <?= $form->field($model, 'src_clslvl_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                    'prompt'=>'Select Class Level',
                                    'onChange'=>'
                                        loadFreeGroups();
                                    ',
                                ]
                            ) ?>

                       
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Setup wizard #2.
                    </div>
                    <div class="panel-body">
                      
                         <?= $form->field($model, 'src_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Acad. Yr.',
                                
                            ]
                        ) ?>
                           <?= $form->field($model, 'src_sem_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                            [
                                'prompt'=>'Select Class Semester',
                            ]
                        ) ?>

                        <?= $form->field($model, 'src_max_crdthr')->textInput() ?>

                        <?= $form->field($model, 'src_min_crdthr')->textInput() ?>
                        <?= $form->field($model, 'src_remark')->textInput() ?>

                        
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Group setups made for this semester.<i class="bg-secondary"><?php
                        echo ' Semester '. Yii::$app->params['C']['S'].' of Year: '.Yii::$app->params['C']['Y'] ?></i>
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="semesterregistrationcontainer">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <?php ActiveForm::end(); ?>

</div>
<!-- c -->

<?php
$this->registerJs(
    "

    window.loadFreeGroups = function(){
        // 
        admId = $('#semesterregistrationcontrold-src_adm_relid').val();
        clslvlId = $('#semesterregistrationcontrold-src_clslvl_relid').val();
        progId = $('#semesterregistrationcontrold-src_prog_relid').val();
        console.log(admId,progId, clslvlId);
        {
            admId:admId,
            clslvlId:clslvlId,
            progId:progId
            },);
    }
    window.loadGroup = function(){
        $.post('/semester-registration-controld/getlist',{},function(data){
                $('#semesterregistrationcontainer').html(data);
            });
    }
    ",
    $this::POS_READY
);
?>