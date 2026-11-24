<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker; 

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Withdrawal Application From');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Student Withdrawal form - request withdrawal
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="requestFormCaontainer">
                        
                    </div>
                    <div class="container-fluid" id="requestFormCaontainerMain" style="display: none;">
                        <div class="wstudent-withdrawal-history-form">

                        <?php $form = ActiveForm::begin(['action'=>'/w-student-withdrawal-history/create']); ?>
                        <span>Select The reason why you are applying withdrawal. The reasons you provide will be approved by your department Head, and then by the registrar.</span>
                        <p class="bg-info">NOTE: The courses you enrolled for this semester(if any) will be removed.</p>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-md-9">
                                <?= $form->field($model, 'swh_reason_relId')->widget(Select2::classname(), [
                                        'data' => ArrayHelper::map(WithdrawalReason::find()->where(['withdrawalres_type'=>'NORMAL'])->all(), 'id', 'withdrawalres_description'),
                                        'options' => ['placeholder' => 'Select reason ...'],
                                        //'disabled'=>'true',
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ]); 
                                ?>  
                            </div>
                            <div class="col-md-2"><br>
                                <?= $form->field($model,'applyForm')->checkBox(
                                    [
                                        1=>'YES',
                                        0=>'NO',
                                    ]
                                    )?>
                            </div>
                            <div class="col-md-1" style="display: none" id="btnContainer"><br>
                                 <div class="form-group pull-left">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Apply') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                        <?php ActiveForm::end(); ?>

                    </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- requestFormCaontainer -->
<!-- btnContainer -->

<?php

$this->registerJs(
    "

    $('#wstudentwithdrawalhistory-applyform').on('change',function(event){
        if(displayed == 0){
            $('#btnContainer').show();
            displayed = 1;
        }else{
            $('#btnContainer').hide();
            displayed = 0;
        }
    });

    displayed = 0;

    window.changeStatus = function(){
        console.log('Changed...');
    }

    window.loadApplicationForm = function(){
        $.post('/request-withdrawal/get-application-form',{}).done(function(data){
            if(data == 1){
                $('#requestFormCaontainerMain').show();
                // loadApplicationFormReal();
            }else{
                $('#requestFormCaontainer').html(data);
            }
        });
    }

    // auto load application form
    loadApplicationForm();
    ",
    $this::POS_READY);
?>