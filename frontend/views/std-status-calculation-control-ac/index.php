<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StdStatusCalculationControlAcSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Student Semester Status Calulation Control - AC";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-ac-index">

    <h2><?= Html::encode($this->title) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Add New'), ['create'], ['class' => 'btn btn-sm btn-success']) ?>
    </p>
    </h2>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'sssccAC_adm_relId',
                'value'=>'sssccACAdmRel.admission_name',
            ],
            [
                'attribute'=>'sssccAC_prog_relId',
                'value'=>'sssccACProgRel.program_name',
            ],
            [
                'attribute'=>'sssccAC_sem_relId',
                'value'=>'sssccACSemRel.semester_name',
            ],
            [
                'attribute'=>'sssccAC_clslvl_relId',
                'format'=>'raw',
                'value'=> function($model){
                    $clslvlNum = \backend\models\ClassLevel::findOne(['id'=>$model->sssccAC_clslvl_relId])->clslvl_num;
                    return $clslvlNum == 1?'1<sup>st</sup>':($clslvlNum == 2?'2<sup>nd</sup>':($clslvlNum == 3?'<sup>rd</sup>':$clslvlNum.'<sup>th</sup>'));
                } ,
            ],
            [
                'attribute'=>'sssccAC_sssn_relId',
                'value'=>'sssccACSssnRel.sssn_name',
            ],
            [
                'attribute'=>'sssccAC_acadYear_relId',
                'value'=>'sssccAC_acadYear_relId.program_name',
            ],
            //'id',
            //'sssccAC_acadYear_relId',
            'sssccAC_Fs',
            //'sssccAC_sem_relId',
            //'sssccAC_adm_relId',
            // 'sssccAC_prog_relId',
            // 'sssccAC_sssn_relId',
            // 'sssccAC_max_CGPA',
            // 'sssccAC_min_CGPA',
            // 'sssccAC_max_SGPA',
            // 'sssccAC_min_SGPA',
            // 'sssccAC_clslvl_relId',
            // 'sssccAC_remark:ntext',
            // 'sssccAC_created_by',
            // 'sssccAC_created_at',
            // 'sssccAC_updated_at',
            // 'sssccAC_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
