<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Student Withdrawal Histories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-withdrawal-history-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Student Withdrawal Form'), ['/semester-registration/index-grade-report'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'swh_acadYear_relId',
                'value' => function($model){
                    return $model->swhAcadYearRel->sch_year_desc."-".$model->swhSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory::find()->groupBy('swh_acadYear_relId')->all(),'swh_acadYear_relId',function($model){ return $model->swhAcadYearRel->sch_year_desc; })
            ],[
                'attribute' => 'std_name',
                'value' => function($model){
                    return $model->swhStdRel->getFullName();
                }
            ],[
                'attribute' => 'swh_reason_relId',
                'value' => function($model){
                    return $model->swhReasonRel->withdrawalres_description;
                }
            ],
            
            [
                'attribute' => 'swh_HOD_approved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->swh_HOD_approved ? $model->swhHODApprovedBy->getFullName_av().", on ".date_format(date_create($model->swh_HOD_approved_at),"d/m/Y") : "Pending...";
                    return $apr;
                }
            ],[
                'attribute' => 'swh_lastApproved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->swh_lastApproved ? "Approved" : "Pending...";
                    $apr .= $model->swhLastApprovedBy?' by '.$model->swhLastApprovedBy->getFullName_av() : '';
                    $apr .= $model->swh_lastApproved_at?' on '.date_format(date_create($model->swh_lastApproved_at),'d/m/Y') : '';
                    return $apr;
                }
            ],[
                'attribute' => 'swh_last_dateAttended',
                'format' => 'raw',
                'value' => function($model){
                    if (!$model->section_relId) {
                        $model->getSetStudentSection();
                    }
                    return $model->sectionRel ? $model->sectionRel->gasGsetRel->getGsDisp() : "-" ;
                }
            ],
            // 'swh_HOD_approved_at',
            // 'swh_HOD_approved_by',
            //'swh_returnedBack',
            //'swh_stdSigned',
            //'swh_stdSigned_at',
            //'swh_stdSigned_by',
            //'swh_lastApproved',
            //'swh_lastApproved_at',
            //'swh_lastApproved_by',
            //'swh_supDoc:ntext',
            //

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
