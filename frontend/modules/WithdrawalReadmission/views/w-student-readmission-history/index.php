<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Student Readmission Histories');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-readmission-history-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Create W Student Readmission History'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'srh_acadYear_relId',
                'value' => function($model){
                    return $model->srhAcadYearRel->sch_year_desc."-".$model->srhSemRel->semester_name;
                }
            ],[
                'attribute' => 'std_name',
                'value' => function($model){
                    return $model->srhStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'srh_return_back_on',
                'value' => function($model){
                    return date_format(date_create($model->srh_return_back_on),"d/m/Y");
                }
            ],
            [
                'attribute' => 'srh_HOD_approved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->srh_HOD_approved ? $model->srhHODApprovedBy->getFullName_av().", on ".date_format(date_create($model->srh_HOD_approved_at),"d/m/Y") : "Pending...";
                    return $apr;
                }
            ],[
                'attribute' => 'srh_approved',
                'format' => 'raw',
                'value' => function($model){
                    $apr = $model->srh_approved ? "Approved" : "Pending...";
                    $apr .= $model->srhApprovedBy?' by '.$model->srhApprovedBy->getFullName_av() : '';
                    $apr .= $model->srh_approved_at?' on '.date_format(date_create($model->srh_approved_at),'d/m/Y') : '';
                    return $apr;
                }
            ],
            // 'srh_swh_relId',
            // 'srh_HOD_approved',
            // 'srh_HOD_approved_at',
            // 'srh_HOD_approved_by',
            // 'srh_approved',
            //'srh_approved_at',
            //'srh_approved_by',
            //'srh_return_back_letter',
            //'remark_HOD:ntext',
            //'remark_registrar:ntext',
            //'srh_created_at',
            //'srh_updated_at',
            //'srh_created_by',
            //'srh_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
