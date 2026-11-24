<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\grid\GridView;
use yii\widgets\Pjax;
use  backend\models\CourseOffering2DetailSearch;
/** 
 * @var yii\web\View  $this
* @var $searchModel backend\models\CourseOffering2Search
* @var $dataProvider yii\data\ActiveDataProvider 
*/

$this->title = Yii::t('rms', 'Course Offerings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-index">

    <h3><?= Html::encode($this->title) ?></h3> 
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
        if (Yii::$app->user->can('DepartmentHead')) {
    ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Course Offerings
                    </div>
                    <div class="panel-body">
                        <p>
                            <?= Html::a(Yii::t('rms', ' + '), ['create'], ['class' => 'btn btn-success', 'title'=>'Add new course Offering']) ?>
                        </p>
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

                                        return Yii::$app->controller->renderPartial('_coff2Det',
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
                                    'attribute'=>'admission',
                                    'value'=>function($model){
                                        return $model->coff2GroupRel->gsAdmRel->admission_name;
                                    },
                                ],

                                 [
                                    'attribute'=>'program',
                                    'value'=>function($model){
                                        return $model->coff2GroupRel->gsProgRel->program_name;
                                    },
                                ],
                                [
                                    'attribute'=>'department',
                                    'value'=>function($model){
                                        return $model->coff2GroupRel->gsDeptRel->dept_name ;
                                    },
                                ],
                                [
                                    'attribute'=>'clslvl',
                                    'format' => 'raw',
                                    'value'=>function($model){
                                        return $model->coff2GroupRel->gsClslvlRel->getClslvlDisp();
                                    },
                                ],

                                [
                                    'attribute'=>'coff2_group_relId',
                                    'value'=>function($model){
                                        return $model->coff2GroupRel->gsGroupRel->group_name;
                                    },
                                ],
                                //,'visible' => false
                                ['class' => 'yii\grid\ActionColumn'],
                            ],
                        ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        
                    </div>
                    <div class="panel-body">
                        
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>
