<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentExemptionearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Payment Exemption');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-redemption-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Create Payment Exemption'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           
            [
                'attribute' => 'acadYear_relId',
                'value' => function($model){
                    return $model->acadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\PaymentRedemption::find()->groupBy('acadYear_relId')->all(),'acadYear_relId',function($model){
                    return $model->acadYearRel->sch_year_desc;
                }),
            ],[
                'attribute' => 'sem_relId',
                'value' => function($model){
                    return $model->semRel->semester_name;
                },'filter' => ArrayHelper::map(\backend\models\PaymentRedemption::find()->groupBy('sem_relId')->all(),'sem_relId',function($model){
                    return $model->semRel->semester_name;
                }),
            ],[
                'attribute' => 'std_relId',
                'value' => function($model){
                    return $model->stdRel->getFullName();
                }
            ],
            
            'reason:ntext',
            [
                'attribute' => 'approved_by',
                'value' => function($model){
                    return $model->approvedBy ? 'Approved By '.$model->approvedBy->getFullName().' on '.date("d/m/Y",$model->approved_at) : 'Not Approved Yet.';
                }
            ],
            
            //'approved_at',
            [
                'attribute' => 'used',
                'value' => function($model){
                    return $model->used?'Used' : 'Not Used Yet.';
                },
                'filter' => ArrayHelper::map(\backend\models\PaymentRedemption::find()->groupBy('used')->all(),'used',function($model){
                    return $model->used?'Used' : 'Not Used Yet.';
                }),
            ],
            'comments:ntext',
            [
                'attribute'=>'Option',
                'format'=>'raw',
                'visible' => Yii::$app->user->can('Registrar'),
                'value'=>function($model){
                    if (!is_null($model->approved_by)) {
                        if ($model->used) {
                            return 'Setting used';
                        }
                        return Html::a(Yii::t('rms', 'Reject'), [''], 
                            [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax'=>0,
                            'title'=>'Reject Approval',
                            'value'=>$model->id,
                            'onclick'=>'changeUsedStatus('.$model->id.');return false;',
                        ]);
                    }else{
                        return Html::a(Yii::t('rms', 'Approve'), [''], 
                            [
                            'class' => 'btn btn-danger btn-xs',
                            'data-pjax'=>0,
                            'title'=>'Approve Request',
                            'value'=>$model->id,
                            'onclick'=>'changeUsedStatus('.$model->id.');return false;',
                        ]);
                    }
                }
            ], 
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => function($model){
                    return $model->used != 1;
                }
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        window.changeUsedStatus = (modelId) => {
            if (confirm('Are you sure to change the approval status?')) {
                $.post('/payment-redemption/change-status',{id:modelId}).done((resp) => { if (resp == 1) { window.location.href = window.location; }else{ alert(resp); } })
            }
        }
    </script>
    <?php Pjax::end(); ?>

</div>
