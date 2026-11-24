<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Course;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseCatalog */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-catalog-form">
    <div class="container-fluid">
        <div class="row">
            <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Select catalog header enteries and course
                    </div>
                    <div class="panel-body">
                        <!--  -->
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_admission_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                                    [
                                        'prompt' => 'Select Admission',
                                        'onChange' => 'coffLoad()',
                                    ]
                                ) ?>
                            </div>

                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_program_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                    [
                                        'prompt' => 'Select Program',
                                        'onChange' => ' $.post( "' . Yii::$app->urlManager->createUrl('program/clslvllists') . '",
                                        {
                                            progId:$(this).val()
                                        }, 
                                        function( data ) {
                                        //console.log("Returned: "+data);
                                      $( "select#coursecatalog-cocat_clslvl_relid" ).html( data ).trigger("change");
                                    });
                                    coffLoad();',
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_dept_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                    [
                                        'prompt' => 'Select Department',
                                        'onChange' => ' 
                                        $.post( "/specialization/listspec-cp",
                                            {
                                                deptId:$(this).val(),
                                                progId:$("#coursecatalog-cocat_program_relid").val(),
                                                admId:$("#coursecatalog-cocat_admission_relid").val()
                                            },  
                                            function( data ) {
                                            console.log("Returned: "+data);
                                          $( "#coursecatalog-cocat_spec_relid").html( data ).trigger("change");
                                        });
                                        coffLoad();
                                    ',
                                    ]
                                ) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_clslvl_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                    [
                                        'prompt' => 'Select Class Level',
                                        'onChange' => 'coffLoad()'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_spec_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'),
                                    [
                                        'prompt' => 'Select Specialization',
                                        'onChange' => 'coffLoad()'
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_sem_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                                    [
                                        'prompt' => 'Select Semester',
                                        'onChange' => 'coffLoad()'
                                    ]
                                ) ?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_courseMajor')->dropDownList(
                                    [
                                        1 => 'YES',
                                        0 => 'NO',
                                    ],

                                )->hint('Identify whether the course is major or not, the default is YES, Course is major.');
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_courseProject')->dropDownList(
                                    [
                                        1 => 'YES',
                                        0 => 'NO',
                                    ],

                                )->hint('Identify whether the course is PROJECT or not, the default is NO, Course is not PROJECT course.');
                                ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'cocat_courseelectives')->dropDownList(
                                    [
                                        1 => 'YES',
                                        0 => 'NO',
                                    ],

                                )->hint('Identify whether the course is Electives or not.');
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="panel-footer">
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-5">
                                    <?= $form->field($model, 'filterDept')->widget(Select2::class, [
                                        'data' => ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                        'options' => [
                                            'placeholder' => 'Filter courses by department...',
                                            'onChange' => '
                                        $.post("/department/getdeptcourses",{deptId:$(this).val()}).done(function(data){
                                                $("#coursecatalog-cocat_course_relid").html(data);
                                            });
                                        ',
                                        ],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ])->label('Filter courses by department.');
                                    ?>
                                </div>
                                <div class="col-md-5">
                                    <?= $form->field($model, 'cocat_course_relId')->widget(Select2::class, [
                                        'data' => ArrayHelper::map(Course::find()->all(), 'id', 'course_title'),
                                        'options' => ['placeholder' => 'Select Course ...'],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ]);
                                    ?>
                                </div>
                                <div class="col-md-2">
                                    <br>
                                    <div class="form-group" style="margin-top: 5px;">
                                        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Add Catalog') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        <?php echo Html::activeHiddenInput($model, "maxCredit"); ?>
                                        <?php echo Html::activeHiddenInput($model, "collectedCredit"); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Course catalog records
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid table-responsive" id="coff_history" style="height: 450px; overflow: auto;">

                        </div>
                    </div>
                    <!-- <div class="panel-footer">
                        <div class="row">
                            <div class="col-sm-5">
                                Last Updated date:<span id="lastEditDate"></span>
                            </div>
                            <div class="col-sm-5">
                                Last instert date:<span id="lastInsertDate"></span>
                            </div>
                        </div>
                    </div> -->
                </div>
            </div>
        </div>
    </div>

</div>
<?=
$this->registerJs(
    "

    window.deleteCatalog22 = function(catalogId){
        if(confirm('You want to delete catalog?')){
            $.post('/course-catalog/delete-catalog',{id:catalogId}).done(function(data){
                if(data == 1){
                    coffLoad();
                }
                else{
                    $('#coff_history').html(data);
                }
            });
        }
    }
    $('form#{$model->formName()}').on('beforeSubmit',function(e){

            var \$form = $(this);
            // console.log(\$form);
            $.post(
                \$form.attr('action'),
                \$form.serialize(),
                function(result){
                    //console.log(result);
                    if(result == 1){
                        //$('select#coursecatalog-cocat_course_relId').reload();
                        $('#coff_history').html(result);
                        coffLoad();
                        }else{
                            $('#coff_history').html(result);
                        }
                    }
                );
            return false;
        });
    window.coffLoad = function(){
        $('#coff_history').html('Loading....');
        admId = $('#coursecatalog-cocat_admission_relid').val();
        progId = $('#coursecatalog-cocat_program_relid').val();
        deptId = $('#coursecatalog-cocat_dept_relid').val();
        clslvlId = $('#coursecatalog-cocat_clslvl_relid').val();
        specId = $('#coursecatalog-cocat_spec_relid').val();
        semId = $('#coursecatalog-cocat_sem_relid').val();

        //console.log(admId,progId,deptId,clslvlId,specId,semId);

        $.post(
            '/course-catalog/cocathistory',
            {
                admId:admId,
                progId:progId,
                deptId:deptId,
                clslvlId:clslvlId,
                specId:specId == 0?null:specId,
                semId:semId
            },
            function(data){
                //console.log(data);
                $('#coff_history').html(data);
                max = $('#crdtCollected').data('maxcredit');
                collected = Number($('#crdtCollected').val());

                console.log(max,collected);

                $('#coursecatalog-maxcredit').val(max);
                $('#coursecatalog-collectedcredit').val(collected);
            }
        );
    }
    ",
    $this::POS_READY
);
?>