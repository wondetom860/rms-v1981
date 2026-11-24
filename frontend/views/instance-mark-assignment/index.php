<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\InstanceMarkAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Instance Mark Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instance-mark-assignment-index">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--  style="background-color: #dd9;" -->
    <div class="container-fluid" style="background-color: #dd9; padding: 5px;">
        <div class="instance-mark-assignment-form row">

            <?php $form = ActiveForm::begin(['action'=>'/instance-mark-assignment/create']); ?>

            <div class="col-md-2">
                <?= $form->field($model, 'ima_course')->dropdownList([],[
                    'prompt'=>'Select Course',
                    'onchange'=>'generatePJAXSearcQuery($(this).val())',
                ])?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'ima_catagorySetup_relId')->dropdownList([],[
                    'prompt'=>'Select Catagory',
                    'onchange'=>'getInstance()',
                ])->label('Catagory') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'ima_instanceName')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'ima_possiblePoint')->textInput() ?>
            </div>
            <div class="col-md-2">
                <div class="form-group"><br>
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

        </div>
    </div>

    <div class="container-fluid table-responsive" style="border: 1px solid black; padding: 5px; margin-top: 10px;">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute'=>'ima_course',
                    'value'=>'imaCatagorySetupRel.catCourseRel.course_title',
                ],
                // [
                //     'attribute'=>'ima_catagorySetup_relId',
                //     'value'=>function($model){
                //         return md5($model->ima_catagorySetup_relId);
                //     }
                // ],
                'ima_instanceName',
                'ima_possiblePoint',
                [
                    'attribute'=>'ima_created_by',
                    'value'=>'imaCreatedBy.first_name',
                ],
                // 'ima_created_at',
                // 'ima_updated_by',
                // 'ima_updated_at',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => [],
                    'header'=>'Actions',
                    'template' => '{view} {update} {delete}',
                    'visibleButtons'=>[
                        'view'=> false,
                         'update'=> function($model, $key, $index){
                              return (is_null($model->ima_approved) || $model->ima_approved == 0) && $model->ima_created_by == Yii::$app->user->identity->id;
                         },
                         'delete'=> function($model, $key, $index){
                              return (is_null($model->ima_approved) || $model->ima_approved == 0)  && $model->ima_created_by == Yii::$app->user->identity->id;
                         },
                    ],
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>

<?php
$this->registerJs(
    "

    // ContAssessCatagory2Search[cat_course_relId]
        window.generatePJAXSearcQuery = function(courseId){
            console.log(courseId);
            $.post('/instance-mark-assignment?InstanceMarkAssignmentSearch%5Bima_course%5D='+courseId+'&InstanceMarkAssignmentSearch%5Bima_instanceName%5D=&InstanceMarkAssignmentSearch%5Bima_possiblePoint%5D=&InstanceMarkAssignmentSearch%5Bima_created_by%5D=',{}).done(function(data){
                $('#w1').html(data);
                });
            $('#instancemarkassignment-ima_instancename').val('');
            $('#instancemarkassignment-ima_possiblepoint').val('');
                loadCatagory(courseId);
            // 
        }

        maxPoint = 0;
        window.getInstance = function(){
            instance = $('#instancemarkassignment-ima_catagorysetup_relid :selected').data('slice');
            maxPoint = $('#instancemarkassignment-ima_catagorysetup_relid :selected').data('max');

            $('#instancemarkassignment-ima_instancename').val(instance);

            $('#instancemarkassignment-ima_possiblepoint').val(maxPoint).prop('max',maxPoint).focus();
        }


        $.post('/cont-assess-catagory2/get-inst-course',{}).done(function(data){
            $('#instancemarkassignment-ima_course').html(data);
            // console.log(data);
            generatePJAXSearcQuery(0);
        }) ;  

        window.loadCatagory = function(courseId){
            coffdet = $('#instancemarkassignment-ima_course :selected').data('coff');
            // console.log(coffdet);
            // $('#contassesscatagory2-cat_coff_relid').val(coffdet);
            $.post('/instance-mark-assignment/get-course-catagory-ima',{courseId:courseId,coffdet:coffdet}).done(function(data){

                $('#instancemarkassignment-ima_catagorysetup_relid').html(data);
                // console.log(data);
            });
        } 
    ",
    $this::POS_READY);
?>
