<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use  backend\models\CourseOffering2DetailSearch;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Offerings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-index">
	<h3><?= Html::encode($this->title) ?></h3> 
	<?php Pjax::begin(); ?>    
	<?= GridView::widget([
	    'dataProvider' => $dataProvider,
	    'filterModel' => $searchModel,
	    'columns' => [
	        [
	            'class'=>'kartik\grid\ExpandRowColumn',
	            'value'=>function($model,$key,$index,$column){
	                return GridView::ROW_COLLAPSED;
	            },
	            'detail'=>function($model,$key,$index,$column){
	                $searchModel2 = new CourseOffering2DetailSearch();
	                $searchModel2->coff2det_coff2_relId = $model->id;
	                $dataProvider2 = $searchModel2->search(Yii::$app->request->queryParams);

	                return Yii::$app->controller->renderPartial('../course-offering2/_coff2Det',
	                    [
	                        'dataProvider'=>$dataProvider2, 
	                        'searchModel'=>$searchModel2,
	                ]);

	            }
	        ],
	        [
	            'attribute'=>'coff2_acadYear_relId',
	            'value'=>'coff2AcadYearRel.sch_year_desc',
	            'filter' => ArrayHelper::map(\backend\models\CourseOffering2::find()->groupBy('coff2_acadYear_relId')->all(),'coff2_acadYear_relId',function($model){ return $model->coff2AcadYearRel->sch_year_desc; }),
	        ],
	        [
	            'attribute'=>'coff2_sem_relId',
	            'value'=>'coff2SemRel.semester_name',
	            'filter' => ArrayHelper::map(\backend\models\CourseOffering2::find()->groupBy('coff2_sem_relId')->all(),'coff2_sem_relId',function($model){ return $model->coff2SemRel->semester_name; })
	        ],
	        [
	            'attribute'=>'clslvl',
	            'format' => 'raw',
	            'value'=>function($model){
	                return $model->coff2detAcProgRel->getClassLevelHeader();
	            },
	            'filter' => ArrayHelper::map(\backend\models\ClassLevel::find()->all(),'id',function($model){
	            	return "Year-".$model->clslvl_num."({$model->clslvlProgramRel->program_name})";
	            }),
	        ],
	        
	        [
	            'attribute'=>'department',
	            'value'=>function($model){
	                return $model->coff2GroupRel->gsDeptRel->dept_name ;
	            },
	            'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
	        ],

	        [
	            'attribute'=>'coff2_group_relId',
	            'value'=>function($model){
	                return $model->coff2GroupRel->gsGroupRel->group_name;
	            },
	        ],
	        [
                'attribute' => 'course_offerings',
                'format' => 'raw',
                'value' => function($model){
                    $courses = $model->courseOffering2Details;
                    if (count($courses) > 0) {
                        // $rr = 
                        $msg =  "<span style='font-size:8pt;'>".count($courses)." course(s).<span>";
                    }else{
                        $msg =  "<span class='warning' style='font-size:8pt;'>Pending student section assignment.</span>";
                    }
                    $pending = 0;
                    foreach ($courses as $coff2det) {
                    	$pending += $coff2det->coff2det_registrarApproved == 1 ? 0:1;
                    }

                    $btn = '';
                    if ($pending == 0 && ($model->coff2_lock_status != 1)) {
                    	$model->coff2_lock_status = 1;
                    	$model->update(false);

                    }else if($pending > 0 && ($model->coff2_lock_status == 1)){
                    	$model->coff2_lock_status = 0;
                    	$model->update(false);
                    }

                    $btn = $pending > 0 ? "<button class='btn btn-xs btn-warning pull-right' onclick='approveOffering({$model->id});return false;'>Approve</button> [{$pending}]" : "<font class='pull-right; bg-success'>Approved</font>";

                    return $msg.$btn;
                }
            ],

	        ['class' => 'yii\grid\ActionColumn','visible' => false],
	    ],
	]); ?>
	<script type="text/javascript">
		window.approveOffering = (coff2Id) => {
			if (confirm('Approve offering?')) {
				$.post('/course-offering2/approve-offering',{coff2Id:coff2Id}).done((resp) => {
					if (resp == 1) { window.location.href = window.location }else{ alert(resp); }
				});
			}
		}
	</script>
	<?php Pjax::end(); ?>
    
