<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlAC */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="std-status-calculation-control-ac-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add New Status calculation Control -AC
                    </div>
                    <div class="panel-body">

                       <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Academic',
                                
                            ]
                        ) ?>
                         </div>
                        <div class="col-md-4">

                        <?= $form->field($model, 'sssccAC_adm_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                            [
                                'prompt'=>'Select Admission',
                                
                            ]
                        ) ?>
                          </div>
                          <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_prog_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                            [
                                'prompt'=>'Select Program',
                                'onChange'=>' $.post("/class-level/classyearlists",
                                                        {
                                                            progId:$(this).val()
                                                        }, 
                                                        function( data ) {
                                                        //console.log("Returned: "+data);
                                                      $( "#stdstatuscalculationcontrolac-sssccac_clslvl_relid" ).html( data );
                                                    });
                                                    // coffLoad();',
                            ]
                        ) ?>
                         </div>
                        <div class="col-md-4">

                        <?= $form->field($model, 'sssccAC_clslvl_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            [
                                'prompt'=>'Select Class Level',
                            ]
                        ) ?>
                         </div>
                      <div class="col-md-4">

                        <!--  -->
                        <?= $form->field($model, 'sssccAC_sem_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                            [
                                'prompt'=>'Select Class Semester',
                            ]
                        ) ?>
                         </div>
                         <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_sssn_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\StudentSemesterStatusName::find()->all(), 'id', 'sssn_name'),
                            [
                                'prompt'=>'Select Class Semester status name',
                            ]
                        ) ?>
                         </div>
                        <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_Fs')->textInput([
                            'title'=>'Max. Number of F s allowed',
                        ]) ?>
                         </div>
                      <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_max_CGPA')->textInput([
                            'max'=>4.00,
                            'min'=>0.00,
                            'type'=>'double',
                            'scale'=>0.10,
                            'title'=>'Max Point or this status',
                            'place-holder'=>'4.00',
                        ]) ?>
                          </div>
                           <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_min_CGPA')->textInput([
                            'min'=>0.00,
                            'max'=>4.00,
                            'scale'=>0.10,
                            'type'=>'double',
                            'title'=>'Min Point or this status',
                            'place-holder'=>'2.00',
                        ]) ?>
                             </div>
                      <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_max_SGPA')->textInput([
                            'max'=>4.00,
                            'min'=>0.00,
                            'scale'=>0.10,
                            'type'=>'double',
                            'title'=>'Max Point or this status',
                            'place-holder'=>'4.00',
                        ]) ?>
                            </div>
                        <div class="col-md-4">
                        <?= $form->field($model, 'sssccAC_min_SGPA')->textInput([
                            'min'=>0.00,
                            'max'=>4.00,
                            'scale'=>0.10,
                            'type'=>'double',
                            'title'=>'Min Point or this status',
                            'placeHolder'=>0.00,
                        ]) ?>
                         </div>

                        <?= $form->field($model, 'sssccAC_remark')->textarea(['rows' => 3]) ?>
                    </div>
                </div>
            </div>

        
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- calculationControlContainer -->

<?php

$this->registerJs(
    "

    ",
    $this::POS_READY);

?>