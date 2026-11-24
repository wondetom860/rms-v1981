<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;


$this->title = "Course History";
// $this->params['breadcrumbs'][] = "course";
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="course-index">
	<h3><?= Html::encode($this->title) ?></h3>
  	<div class="row" style="font-size: 9pt; font-weight:normal; font-family: cursive;">
  		<div class="col-md-5 table-responsive">
  			<?php Pjax::begin(); ?>    
  				<?= GridView::widget([
			          'dataProvider' => $dataProvider,
			          'filterModel' => $searchModel,
			          'columns' => [
			              	['class' => 'yii\grid\SerialColumn'],

			              	[
			              		'attribute' => 'course_code',
			              		'value' => function($model){
			              			return $model->getCourseCredits();
			              		}
			              	],
			              	[
			                  	'attribute'=>'course_dept_relId',
			                  	'value'=>'courseDeptRel.dept_code',
			                  	'filter' => ArrayHelper::map(\backend\models\Course::find()->groupBy('course_dept_relId')->all(),'course_dept_relId',function($model){ return $model->courseDeptRel->dept_code; }),
			              	],

			              	'course_title',
			              
			              	[
			                	'attribute'=>'Option',
			                	'format'=>'raw',
			                	'value'=>function($model){
			                    	return Html::a(Yii::t('rms', 'See more'), [''], 
			                        	[
			                        		'class' => 'btn btn-info btn-xs',
			                        		'data-pjax'=>0,
			                        		'title'=>'See more about this course',
			                        		'value'=>$model->id,
			                        		'onclick'=>'showmore('.$model->id.');return false;',
			                    	]);
			                }
			            ], 
			              // ['class' => 'yii\grid\ActionColumn'],
			          ],
			      ]); ?>
			  <?php Pjax::end(); ?>
  		</div>
  		<div class="col-md-7"style='background-color: white;'>
  			<h4>More about course<i class="fa-thin fa-ballot-check pull-right"></i></h4>
  			<div class="container-fluid" id="mode_detail">
  				
  			</div>
  		</div>
  	</div>


  <!-- javascript code -->
  <script type="text/javascript">
  		__modelId = 0;
  		window.changeStatus = (catalogId) => {
  			if (confirm('Are you sure to change course matric status?')) {
  				$.post('/course-catalog/change-status',{id:catalogId}).done((resp) => { if (resp == 1) { showmore(__modelId); }else{ alert(resp); } });
  			}
  		}
      	window.showmore = (modelId) => {
      		$("#mode_detail").html('Loading more...');
      		__modelId = modelId;
      		$.post('/course/show-more',{id:modelId}).done((resp) => { $("#mode_detail").html(resp); });
      	}
      </script>
</div>