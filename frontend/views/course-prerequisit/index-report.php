<?php 

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CoursePrerequisitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Course Prerequisite');
$this->params['breadcrumbs'][] = $this->title;

 ?>

<div class="course-prerequisit-index table-responsive">

<h3><?= Html::encode($this->title) ?></h3>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [
            'style'=>'overflow: auto; word-wrap: break-word;'
        ],
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            [
                'attribute'=>'preq_adm_relId',
                'filter' => ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
                'value'=>function($model){
                    return is_null($model->preq_adm_relId)?'All':$model->preqAdmRel->admission_name;
                }
            ],
            [
                'attribute'=>'preq_prog_relId',
                'filter' => ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
                'value'=>function($model){
                    return is_null($model->preq_prog_relId)?'All':$model->preqProgRel->program_name;
                }
            ],

            [
                'attribute'=>'preqMainCourseRel.course_title',
                'value'=> function($model){
                	return $model->preqMainCourseRel->course_title."[{$model->preqMainCourseRel->course_code}]";
                },
            ],
            [
                'attribute'=>'preqPreqCourseRel.course_title',
                'format' => 'raw',
                'value'=>function($model){
                    return $this->render('_report/course-pre-requisites',['model' => $model]) ;
                },
            ],

            // 
            // 
            // 'preq_created_by',
            // 'preq_created_at',
            // 'preq_updated_by',
            // 'preq_updated_at',
            // 'remark:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => [],
                'header'=>'Actions',
                'template' => '{view} {update} {delete}',
                'visibleButtons'=>[
                    'view'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin');
                     },
                     'update'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin');
                     },
                     'delete'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin');
                     },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>

</div>