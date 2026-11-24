<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Feedbacks');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    User Feedbacks
                    <div class="pull-right">
                        <button type="button" title="Add your feedback" onclick="loadModalForm()" class="btn btn-success btn-sm"><i class="glyphicon glyphicon-plus"></i></button>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="container-fluid" style="max-height:450px; overflow: auto;">
                        <div class="row"  id="userFeedbacksContainer">
                            
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
    Modal::begin([
        'header'=>'<h4 id="modalGeneralHeader">Input Feedback</h4>',
        'id'=>'modalGeneral',
        'size'=>'modal-lg',
        'options'=>[
            'style'=>[
                'text-align'=>'left',
            ],
        ],
     ]);
     echo "<div id='modalContentdd'>Loading form...</div>";
     Modal::end();
?>

<!-- requestFormCaontainer -->
<!-- btnContainer -->
<!-- userFeedbacksContainer -->

<?php

$this->registerJs(
    "
    window.showCommentOptions = function(fbId){

        // $('#modalGeneralHeader').parent().css('background-color','#8D0');
        // $('#modalGeneralHeader').html('Feedback Status');
        // $('#modalGeneral').modal('show');
        // $('#modalContent').load(').css('text-align','left');

        $.get('/user-feedback/status?fbId='+fbId).done(function(data){
            if(data == 1){
                loadFeedBacks();
            }else{
                alert('Error While updating feedback status.');
            }
            })
        
    }
    window.loadFeedBacks = function(){
        $.post('/user-feedback/get-feedbacks',{})
        .done(function(data){
            $('#userFeedbacksContainer').html(data);
        });
    }

    loadFeedBacks();

    window.loadModalForm = function(){
        $('#modalGeneralHeader').parent().css('background-color','#8D0');
        $('#modalGeneral').modal('show');
        $.get('/user-feedback/form').done((resp) => {
            $('#modalContentdd').html(resp);
        });
        // .load('/user-feedback/form');
    }

    // $('#wstudentwithdrawalhistory-applyform').on('change',function(event){
    //     if(displayed == 0){
    //         $('#btnContainer').show();
    //         displayed = 1;
    //     }else{
    //         $('#btnContainer').hide();
    //         displayed = 0;
    //     }
    // });

    // displayed = 0;

    // window.changeStatus = function(){
    //     console.log('Changed...');
    // }

    // window.loadApplicationForm = function(){
    //     $.post('/request-withdrawal/get-application-form',{}).done(function(data){
    //         if(data == 1){
    //             $('#requestFormCaontainerMain').show();
    //             // loadApplicationFormReal();
    //         }else{
    //             $('#requestFormCaontainer').html(data);
    //         }
    //     });
    // }

    // // auto load application form
    // loadApplicationForm();
    ",
    $this::POS_READY);
?>