<?php

use backend\models\Admission;
use backend\models\ClassLevel;
use backend\models\Program;
use backend\models\Semester;
use backend\models\StudentSemesterStatusName;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StdStatusCalculationControlDefaultSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Student Semester Status Calulation Control";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-default-index">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'New Status Calculation Control (Default)'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'sssccdAdmRel.admission_name',
                'value' => 'sssccdAdmRel.admission_name',
                'filter' => ArrayHelper::map(Admission::find()->all(), 'admission_name', 'admission_name')
            ],
            [
                'attribute' => 'sssccdProgRel.program_name',
                'value' => 'sssccdProgRel.program_name',
                'filter' => ArrayHelper::map(Program::find()->all(), 'program_name', 'program_name')

            ],
            [
                'attribute' => 'sssccdClslvlRel.clslvl_num',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->sssccdClslvlRel->getClslvlDisp();
                },
                'filter' => ArrayHelper::map(ClassLevel::find()->all(), 'clslvl_num', 'clslvl_name')
            ],
            [
                'attribute' => 'sssccdSemRel.semester_name',
                'value' => 'sssccdSemRel.semester_name',
                'filter' => ArrayHelper::map(Semester::find()->all(), 'semester_name', 'semester_name')

            ],
            [
                'attribute' => 'sssccdSssnRel.sssn_name',
                'value' => 'sssccdSssnRel.sssn_name',
                'filter' => ArrayHelper::map(StudentSemesterStatusName::find()->all(), 'sssn_name', 'sssn_name')

            ],
            [
                'attribute' => 'sssccd_min_SGPA',
                'value' => function ($model) {
                    return sprintf("%0.4f", $model->sssccd_min_SGPA);
                }
            ],
            [
                'attribute' => 'sssccd_max_SGPA',
                'value' => function ($model) {
                    return sprintf("%0.4f", $model->sssccd_max_SGPA);
                    // return $model->sssccd_max_SGPA == 0?' - ':;
                }
            ],
            [
                'attribute' => 'sssccd_min_CGPA',
                'value' => function ($model) {
                    return sprintf("%0.4f", $model->sssccd_min_CGPA);
                    // return $model->sssccd_min_CGPA == 0?' - ':;
                }
            ],
            [
                'attribute' => 'sssccd_max_CGPA',
                'value' => function ($model) {
                    return sprintf("%0.4f", $model->sssccd_max_CGPA);
                    // return $model->sssccd_max_CGPA == 0?' - ':;
                }
            ],
            //'sssccd_remark:ntext',
            [
                'attribute' => 'sssccdCreatedBy.username',
                'value' => 'sssccdCreatedBy.username',
            ],




            // 'sssccd_created_by',
            // 'sssccd_created_at',
            // 'sssccd_updated_at',
            // 'sssccd_updated_by',
            // 'sssccd_max_SGPA',
            // 'sssccd_min_SGPA',
            // 'sssccd_clslvl_relId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


<?php
$this->registerJs(
    "
    $('selection').change(function(){
        console.log('neemsk');
        $(this).parent().parent().hightLight();
    })

    ",
    $this::POS_READY
);

?>