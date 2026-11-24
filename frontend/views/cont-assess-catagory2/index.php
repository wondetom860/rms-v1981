<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContAssessCatagorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Assessment Catagories Setup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory2-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/cont-assess-catagory2/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Add new course continouos assessment catagory setup
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'cat_course_relId')->dropdownList(ArrayHelper::map($model->getDeptCourses(),'id','course_title'),['prompt'=>'Select Course','onchange'=>'generatePJAXSearcQuery($(this).val())'])?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'cat_catagory_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\CatagoryName2::find()->where(['catagory_name'=>'Final Exam'])->all(),'id','catagory_name')
                                    ,[
                                        'prompt'=>'Select Catagory Name...',
                                    ]
                                ) ?>
                            </div>
                             <div class="col-md-3">
                                <?= $form->field($model, 'cat_maxPoint')->textInput() ?>
                            </div>
                            <div class="col-md-3" style="display: none;">
                                <?= $form->field($model, 'cat_coff_relId')->textInput() ?>
                            </div>
                            <div class="col-md-3"><br>
                                <div class="form-group">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Add') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
            
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         <h5 style="text-decoration: underline;">Continouos Course Assessment Catagories setup.</h5>
                    </div>
                    <div class="panel-body">
                        <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                    'attribute'=>'cat_course_relId',
                                    'value'=>'catCourseRel.course_title',
                                ],
                                [
                                    'attribute'=>'cat_catagory_relId',
                                    'value'=>'catCatagoryRel.catagory_name',
                                ],
                                'cat_maxPoint',
                                [
                                    'attribute'=>'cat_created_by',
                                    'value'=>'catCreatedBy.first_name',
                                ],
                                [
                                    'attribute'=>'cat_created_at',
                                    'value'=> function($model){
                                        return date_format(date_create($model->cat_created_at),'D, M d, Y');
                                    } ,
                                ],
                                

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'contentOptions' => [],
                                    'header'=>'Actions',
                                    'template' => '{view} {update} {delete}',
                                    'visibleButtons'=>[
                                        'view'=> false,
                                         'update'=> function($model, $key, $index){
                                              return ($model->cat_created_by == Yii::$app->user->identity->id) || (Yii::$app->user->can('DepartmetnHead'));
                                         },
                                         'delete'=> false,
                                    ]
                                ],
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>

        </div>
    </div></div>

    <?php
    $this->registerJs(
        "
        // ContAssessCatagory2Search[cat_course_relId]
        window.generatePJAXSearcQuery = function(courseId){
            //console.log(courseId);
            $.post('/cont-assess-catagory2?ContAssessCatagory2Search[cat_course_relId]='+courseId+'&ContAssessCatagory2Search[cat_name_relId]=&ContAssessCatagory2Search[cat_maxPoint]=&ContAssessCatagory2Search[cat_created_by]=&ContAssessCatagory2Search[cat_created_at]=',{}).done(function(data){
                $('#w1').html(data);
                });
                loadCatagory(courseId);
            // 
        }

        $.post('/cont-assess-catagory2/get-inst-course',{}).done(function(data){
            $('#contassesscatagory2-cat_course_relid').html(data);
            // console.log(data);
        }) ;  

        window.loadCatagory = function(courseId){
            coffdet = $('#contassesscatagory2-cat_course_relid :selected').data('coff');
            // console.log(coffdet);
            $('#contassesscatagory2-cat_coff_relid').val(coffdet);
            $.post('/cont-assess-catagory2/get-course-catagory',{courseId:courseId,coffdet:coffdet}).done(function(data){

                $('#contassesscatagory2-cat_catagory_relid').html(data);
                // console.log(data);
            });
        }        
        ",
        $this::POS_READY);
    ?>