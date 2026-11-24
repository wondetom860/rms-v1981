<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
// use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Readmission');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Student Readmission form - request readmission
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="requestFormCaontainer">
                        
                    </div>
                    <?php if (isset($model) && isset($modelSWH)) { ?>
                    <div class="container-fluid" id="requestFormCaontainerMain" style="display: none;">

                        <?php $form = ActiveForm::begin(['action'=>'/w-student-readmission-history/create','options'=>['enctype'=>'multipart/form-data'],]); ?>
                        <div class="row" style="border-top: 1px solid black;">

                            <div class="col-md-6" id="withdrawalform">
                                
                                <div class="container-fluid" style="height: 300px;" style="border: 1px solid gold;" id="withdrawalform">
                                    <script type="text/javascript">
                                        if({$modelSWH->id} == 'Undefined'){return false;};
                                            $('#withdrawalform').html('Loading your withdrawal form...');
                                            $.post('/request-withdrawal/get-application-report',{id:{$modelSWH->id}})
                                            .done(function(data){
                                                $('#withdrawalform').html(data);
                                            });
                                    </script>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                    <?= $form->field($model, 'srh_return_back_on')->widget(DatePicker::className(),[
                                            'pluginOptions'=>[
                                                'autoClose'=>false,
                                                'format'=>'yyyy-m-dd',
                                                ],
                                            ])->label('Return back on')?>

                                        <?php echo Html::activeHiddenInput($model, 'srh_swh_relId');?>
                                    </div>
                                    <div class="col-md-12"><br>
                                        <?= $form->field($model,'applyForm')->checkBox(
                                            [
                                                1=>'YES',
                                                0=>'NO',
                                            ]
                                            )?>
                                    </div>
                                    <div class="col-md-12" style="display: none" id="btnContainer"><br>
                                         <div class="form-group pull-left">
                                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Apply') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>

                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- requestFormCaontainer -->
<!-- btnContainer -->
<!-- withdrawalform -->

<script type="text/javascript">
    // window.loadApplicationFormReal  = function(id){
    //     console.log(id);
    // }
</script>

<?php

$this->registerJs(
    "

    $('#wstudentreadmissionhistory-applyform').on('change',function(event){
        if(displayed == 0){
            $('#btnContainer').show();
            displayed = 1;
        }else{
            $('#btnContainer').hide();
            displayed = 0;
        }
    });

    window.loadApplicationFormReal = function(){
        
    }
    displayed = 0;

    window.changeStatus = function(){
        console.log('Changed...');
    }

    window.loadApplicationForm = function(){
        $.post('/request-readmission/get-application-form',{}).done(function(data){
            try{
                d = JSON.parse(data);
                if(d[0] == 1){
                    $('#requestFormCaontainerMain').show();
                    $('#wstudentreadmissionhistory-srh_swh_relid').val(d[1]);
                    loadApplicationFormReal();
                }else{
                    $('#requestFormCaontainer').html(data);
                }
                }catch(e){
                    $('#requestFormCaontainer').html(data);
                }
        });
    }

    // auto load application form
    loadApplicationForm();
    ",
    $this::POS_READY);
?>