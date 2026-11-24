<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $this->title = Yii::t('decirs', 'Course Offering2 Details');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-detail-index">

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'coff2det_course_relId',
                'value'=>'coff2detCourseRel.course_title'
            ],
            [
                'attribute'=>'coff2det_inst_relId',
                'value'=>function($model){

                    if (is_null($model->coff2det_inst_relId) || is_null($model->coff2detInstRel)) {
                        return 'TBA';
                    }
                    return $model->coff2detInstRel->getFullName();
                }
            ],
            [
                'attribute'=>'coff2det_TA_relId',
                'value'=>function($model){
                    if (is_null($model->coff2det_TA_relId)) {

                        // $course = \backend\models\Course::findOne(['id'=>$model->coff2det_course_relId]);
                        if ($model->coff2detCourseRel->course_lab_hour > 0) {
                            return 'TBA';
                        }else{
                            return 'N/A';
                        }
                    }
                    // $inst = \backend\models\Instructor::findOne(['id'=>$model->coff2det_TA_relId]);
                    $usr = $model->coff2detTARel->instUserRel;
                    return $usr->first_name .' '.$usr->middle_name;
                }
            ],
            [
                'attribute'=>'coff2det_created_by',
                'value'=>'coff2detCreatedBy.first_name',
            ],
            [
                'attribute'=>'coff2det_created_at',
                'value'=>function($model){
                    return date_format(date_create($model->coff2det_created_at),'M d, Y');
                }
            ],
            [
                'attribute'=>'coff2det_registrarApproved',
                'value'=>function($model){
                    return $model->coff2det_registrarApproved ? "Approved" : "Pending approval";
                }
            ],
            // 'coff2det_updated_at',
            // 'coff2det_updated_by',

            [
                'class' => 'yii\grid\ActionColumn',
                'urlCreator' => function ($action, $model, $key, $index) {
                    return Url::to(['course-offering2-detail/'.$action, 'id' => $model->id]);
                },
                'template' => '{view} {update} {delete}',
                'visibleButtons'=>[
                    'view'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin');
                     },
                     'update'=> function($model, $key, $index){
                          return Yii::$app->user->can('DepartmentHead');
                     },
                     'delete'=> function($model, $key, $index){
                          return Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar');
                     },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
