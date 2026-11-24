<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluation2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation2-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php Pjax::begin(['id'=>'semester_evaluation_2']); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'rowOptions'=>function($model){
            $evalType = \backend\models\SemesterEvaluationTypes::findOne(['id'=>$model->semev_evalType_relId]);
            if ($evalType->semev_type_level == 4) {//Eval type is EVALUATE_INST
                return ['class'=>'success'];
            }else if($evalType->semev_type_level == 5){ //Eval Type is EVALUATE_TAs.
                return ['class'=>'warning','title'=>'Evaluation progress',];
            }else{
                return ['class'=>'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'semev_acadYear_relId',
                'value'=>'semevAcadYearRel.sch_year_desc',
            ],
            [
                'attribute'=>'semev_sem_relId',
                'value'=>'semevSemRel.semester_name',
            ],
            [
                'attribute'=>'semev_evaluee_relId',
                'value'=>function($model){
                    $user=\backend\models\User::findOne(['id'=>$model->semev_evaluee_relId]);
                    return $user->first_name .' '.$user->middle_name;
                } ,
            ],
            [
                'attribute'=>'semev_group_relId',
                'value'=>function($model){
                    if (is_null($model->semev_group_relId)) {
                        return 'N/A';
                    }
                    $gset = \backend\models\WGroupSetup::findOne(['id'=>$model->semev_group_relId]);
                    return \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;
                }
            ], 
            [
                'attribute'=>'semev_course_relId',
                'value'=> function($model){
                    if (is_null($model->semev_course_relId)) {
                        return 'N/A';
                    }else{
                        return \backend\models\Course::findOne(['id'=>$model->semev_course_relId])->course_title;
                    }
                } ,
            ],
            [
                'attribute'=>'semev_evalType_relId',
                'value'=>'semevEvalTypeRel.semev_type_desc',
            ],
            [
                'attribute'=>'semev_averagePoint',
                'value'=>function($model){
                    $result = \backend\models\SemesterEvaluationReportController::getReport($model->id);
                    return $result[0] .'/'.$result[1];
                }
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<?php

$this->registerJs(
    "
    // t = setInterval(function(){
    //     // DEC_12_11_54912
    //     $('#semester_evaluation_2').reload();
    //     },8000);
    ",
    $this::POS_READY
);

?>