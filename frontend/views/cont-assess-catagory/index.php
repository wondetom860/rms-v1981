<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContAssessCatagorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Course Continouos Assessment Catagories Setup');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/cont-assess-catagory/create']); ?>
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
                                <?= $form->field($model, 'cat_name_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\ContAssessCatagoryName::find()->where(['cont_cat_name'=>'Final Exam'])->all(),'id','cont_cat_name')
                                    ,[
                                        'prompt'=>'Select Catagory Name...',
                                    ]
                                ) ?>
                            </div>
                             <div class="col-md-3">
                                <?= $form->field($model, 'cat_maxPoint')->textInput() ?>
                            </div>
                            <div class="col-md-3"><br>
                                <div class="form-group">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Add') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>

            <div class="col-md-12">
                <?php if(Yii::$app->session->hasFlash('message')){ ?>
                    <div class="alert alert-warning  alert-dismissable">
                        <?= Yii::$app->session->getFlash('message'); ?>
                    </div>
                <?php }?>
            </div>

            
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         <h5 style="text-decoration: underline;">Continouos Course Assessment Catagories setup.</h5>
                    </div>
                    <div class="panel-body">
                        <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                    'attribute'=>'cat_course_relId',
                                    'value'=>'catCourseRel.course_title',
                                ],
                                [
                                    'attribute'=>'cat_name_relId',
                                    'value'=>'catNameRel.cont_cat_name',
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
                                

                                ['class' => 'yii\grid\ActionColumn'],
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
        window.generatePJAXSearcQuery = function(courseId){
            //console.log(courseId);
            $.post('/cont-assess-catagory?ContAssessCatagorySearch[cat_course_relId]='+courseId+'&ContAssessCatagorySearch[cat_name_relId]=&ContAssessCatagorySearch[cat_maxPoint]=&ContAssessCatagorySearch[cat_created_by]=&ContAssessCatagorySearch[cat_created_at]=',{}).done(function(data){
                $('#w1').html(data);
                });
            // 
        }        
        ",
        $this::POS_READY);
    ?>