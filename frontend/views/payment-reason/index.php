<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Payment Reasons');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-reason-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="payment-reason-form">

        <?php $form = ActiveForm::begin(); ?>

        <div class="container-fluid" style="background-color: #dd9; padding: 12px">
            <div class="row">
                <div class="col-md-4">
                 <?= $form->field($model, 'pr_desc')->textarea(['rows' => 2]) ?>
                </div>
                <div class="col-md-2">
                    <?= $form->field($model, 'pr_amount')->textInput(['rows' => 2]) ?>
                </div>
                <div class="col-md-5">
                     <?= $form->field($model, 'remark')->textInput() ?>     
                </div>
                <div class="col-md-1">
                    <div class="form-group"  style="vertical-align: middle;">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'submitForm']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div><br>

    <?php ActiveForm::end(); ?>

</div>

    <?php

    $this->registerJs(
        "
        $('#w0').on('beforeSubmit',function(e){
                var \$form = $(this);

                $.post(
                    '/payment-reason/create',
                    \$form.serialize(),
                    function(result){
                        if(result == 1){
                            window.location.href = window.location;
                            }
                        }
                    );
                return false;
            });

        // $('#submitForm').click(function(e){
        //     e.preventDefault();
        //     //$('#w0').submit();

        //     // paymentreason-pr_desc
        //     // paymentreason-pr_amount
        //     // paymentreason-remark

        //     console.log('Data: '+$('#paymentreason-pr_desc').val());


        //     });
        // // 
        //$('#paymentTypesContainer').html('Loading Available Payment types.');
        ",
        $this::POS_READY);

    ?>
    <?php

    echo "<div class='container-fluid table-responsive' style='background-color:#ddf;'>
            <table class='table table-striped table-condensed'>
                <thead>
                    <th>#</th>
                    <th>Payment Decription</th>
                    <th>Amount</th>
                    <th>Remark</th>
                    <th>Option</th>
                </thead>
                <caption>Avalable Payment Reasons</caption>
                <tbody>
                    ";
                    $count = 0;
    foreach ($dataProvider as $key) {
        echo "<tr>
                <td>".++$count."</td>
                <td>{$key->pr_desc}</td>
                <td>{$key->pr_amount}Br.</td>
                <td>{$key->remark}</td>
                <td>Opt.</td>
            </tr>";
         }
    echo "
                </tbody>
                <tfoot>
                    <tr><td>  </td></tr>
                    <tr><td colspan='4'  style='background-color:#ace; font-style:italic;'>{$count} records.</td></tr>
                </tfoot>
            </table>
        </div>";
    ?>
</div>
