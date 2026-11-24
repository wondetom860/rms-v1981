<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Reset user password');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00CF39;">
                    Reset User Password
                </div>
                <div class="panel-body" style="font-size: 9pt;">
                    <div class="container-fluid" id="requestFormCaontainer">
                        <div class="col-md-12">
                            <?php if(Yii::$app->session->hasFlash('message')){ ?>
                                <div class="alert alert-warning  alert-dismissable">
                                    <?= Yii::$app->session->getFlash('message'); ?>
                                </div>
                            <?php }?>
                        </div>
                        <div class="col-md-4" style="border-right: 1px solid black; font-size: 9pt;">
                            <h5>Get User</h5>
                            <div class="container-fluid">
                                <div class="col-md-10">
                                    <input type="text" class="form-control" name="" title="Enter user search key of whom you are to reset password." id="id_input">
                                </div>
                                <div class="col-md-2 pull-left">
                                    <button class="btn pull-right btn-primary" onclick="searchUser();return false;">Search</button>
                                </div>
                            </div>
                            <hr>
                            <div class="container-fluid" id="ressult_container" style="height: 450px; overflow: auto;">
                                
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="container-fluid" id="user_profile_container">
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- requestFormCaontainer -->
<!-- btnContainer -->
<!-- withdrawalFormContainer -->
<!-- enrolledCourseContainer -->

<?php

$this->registerJs(
    "

    window.resetPassword = function(userId){

        $.post('/user/get-user-profile-info',{userId:userId}).done(function(data){
            $('#user_profile_container').html(data);
        });

        // get user profile information
        // console.log('Loading forms '+userId);
    }


    window.searchUser = function(){
        userId2 = $('#id_input').val();
        if( userId2 == 'undefined' || userId2 == '' || userId2 == ' '){
            alert('Input empty, Please enter the one.');
            $('#id_input').focus();
        }else{
            $('#ressult_container').html('Searching...');
            $.post('/admit-student/get-user-by-id',{userId:userId2,ch:1}).done(function(data){
                $('#ressult_container').html(data);
            });
        }
    }

    $('#btnContainer').hide();
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
    ",
    $this::POS_READY);
?>