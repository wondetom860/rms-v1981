<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use backend\models\Instructor;
use backend\models\User;

/** @var yii\web\View $this */
/* @var $model backend\models\DepartmentHead */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-head-form">

    <?php $form = ActiveForm::begin(); $iModel = Instructor::find()->all()?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Assign new department head
                    </div>
                    <div class="panel-body">
                        
                        <?= $form->field($model, 'dh_dept_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                            [
                                'prompt'=>'Select Deprtment',
                                'onChange'=>'loadInst($(this).val());',
                            ]
                        ) ?>

                        <?= $form->field($model, 'dh_inst_relId')->dropdownList(
                            ArrayHelper::map($iModel, 'id', function($iModel){
                                $uModel = User::findOne(['id'=>$iModel->inst_user_relId]);
                                return $uModel->first_name . ' ' . $uModel->middle_name;
                            }),
                            [
                                'prompt'=>'Select Instructor',
                                'onChange'=>'console.log($(this).val());',
                            ]
                        ) ?>

                        <?= $form->field($model, 'dh_assig_date')->widget(DatePicker::className(),[
                            'pluginOptions'=>[
                                'autoClose'=>true,
                                'format'=>'yyyy-m-dd',
                            ],
                        ]) ?>

                        <?= $form->field($model, 'dh_status')->dropdownList([1 => 'ACTIVE',0=>'TERMINATED']) ?>

                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Current ative department heads.
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="dhContainer">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!-- departmenthead-dh_inst_relid -->
<?=
$this->registerJs(
    "
    window.loadDH = function(deptId){
        $.post('/department-head/dhlists',{},function(data){
                $('#dhContainer').html(data);
            });
    }
    window.loadInst = function(deptId){
        $.post(
        '/department-head/inistlists',
        {deptId:deptId},
        function(data){
            console.log(data);
            $('#departmenthead-dh_inst_relid').html(data);
            });
    }
    loadDH();
    ",
    $this::POS_READY
);
?>