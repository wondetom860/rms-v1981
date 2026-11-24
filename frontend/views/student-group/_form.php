<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-group-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student Group Mgt. >>> Add new Student group.
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'group_name')->textInput(['placeholder'=>'G20']) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'createGroup']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Previously created Groups
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="group_container">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        Current active academic year: <?php echo Yii::$app->params['C']['Y']; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(
    "
    $('#createGroup').on('click',function(e){
        e.preventDefault();
        gname = $('#studentgroup-group_name').val();

        if(gname == '' || gname==' '){
            alert('Please enter correct group name as shown on the field.');
        }else{
            $.post(
                '/student-group/create',
                {gname:gname},
                function(data){
                    //console.log(data);
                    if(data == 1){
                        loadGroupList();
                        }else{
                            $('#group_container').html(data);
                        }
                }
            );
        }
    });
    window.loadGroupList = function(){
        $.post(
            '/student-group/grouplists',
            {},
            function(data){
                console.log(data);
                $('#group_container').html(data);
            }
        );
    }

    loadGroupList();
    ",
    $this::POS_READY
);
?>