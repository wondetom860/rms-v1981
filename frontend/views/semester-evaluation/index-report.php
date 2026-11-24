<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluation2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Evaluation Report');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation2-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php Pjax::begin(['id'=>'semester_evaluation_2']); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'semev_acadYear_relId',
                'value'=>'semevAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_acadYear_relId')->all(),'semev_acadYear_relId',function($model){ return $model->semevAcadYearRel->sch_year_desc; })
            ],
            [
                'attribute'=>'semev_sem_relId',
                'value'=>'semevSemRel.semester_name',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_sem_relId')->all(),'semev_sem_relId',function($model){ return $model->semevSemRel->semester_name; })
            ],
            [
                'attribute'=>'semev_evaluee_name',
                'value'=>function($model){
                    return $model->semevEvalueeRel->getFullName();
                    // $user=\backend\models\User::findOne(['id'=>$model->semev_evaluee_relId]);
                    // return $user->first_name .' '.$user->middle_name;
                } ,
            ],
            [
                'attribute'=>'semev_evalType_relId',
                'value'=>'semevEvalTypeRel.semev_type_desc',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_evalType_relId')->all(),'semev_evalType_relId',function($model){ return $model->semevEvalTypeRel->semev_type_desc; })
            ],
            [
                'attribute'=>'semev_averagePoint',
                'value'=>function($model){
                    // $result = \backend\models\SemesterEvaluationReportController::getReport($model->id);
                    $max_point = $model->semevEvalTypeRel->semev_type_maxPoint;
                    return $model->getAverage()."/".sprintf("%0.2f",$max_point);
                }
            ],[
                'attribute'=>'option',
                'format' => 'raw',
                'value'=>function($model){
                    // get at least one course offering detail entry for this instructor, actially department head. 
                    $repButs = "-";
                    $coffDets = $model->getCoffDets();
                    if ($coffDets && count($coffDets) > 0) {
                        $repButs = "";
                        foreach ($coffDets as $coffDet) {
                            $ta = in_array($model->semev_evalType_relId,[5,7,9]) ? (is_null($coffDet->coff2det_TA_relId) ? 0 : $coffDet->coff2det_TA_relId) : 0;
                            $repButs .= "<a class='btn btn-info btn-xs pull-right' title='{$coffDet->coff2detCourseRel->course_title}' onclick='printReport({$coffDet->id},{$ta});return false;'>{$coffDet->coff2detCourseRel->course_code}</a><br>";
                        }
                    }
                    return $repButs ;
                }
            ],[
                'attribute'=>'option',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('AcademicDean'),
                'value'=>function($model){
                    return $model->deletePossible() ? 
                        "
                        <button class='btn btn-xs btn-warning pull-right' title='Permanently remove this entry' onclick='deleteEntry({$model->id});return false'>Delete</button>
                        ":
                        '';
                }
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
<script type="text/javascript">
    window.deleteEntry = (semev2Id) => {
        if (confirm("Are you sure to delete this entry?")) {
            $.post('/semester-evaluation/delete',{semev2Id:semev2Id})
            .done((respone) => { 
                if (respone == 1) { 
                    window.location.href = window.location; 
                }else{ 
                    alert(respone); 
                } 
            });
        }
    }
    window.printReport = (offeringDetailModel,ta)=>{
        // window.showEvaluation = () => {
        myWindow = window.open(
          '/generate-semester-evaluation-report/get-report?coffDetId='+offeringDetailModel+'&ta='+ta+'&__loyout_print=1', 
          'title=Generate Evaluation Report', 
          'height=900,width=800');
    }
</script>