<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Information');
$this->params['breadcrumbs'][] = "course";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <div class="col-md-12">
        <?php $form = ActiveForm::begin(['action' => '/course/create']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                Register a course
            </div>
            <div class="panel-body" style="background-color: #dd9;">
                <div class="row">
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_dept_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                            [
                                'prompt' => 'Select Deprtment',
                                'onChange' => ' $.post("/department/deptcode",
                                {
                                    deptId:$(this).val()
                                }, 
                                function( data ) {
                              $( "#course-course_code" ).val(data).focus();
                            });',
                            ]
                        ) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_entry_type')
                            ->dropdownList(
                                [
                                    1 => 'Regular',
                                    2 => 'Transfer',
                                    3 => 'Special'
                                ],
                                [
                                    'prompt' => 'Course Entry Type',
                                ]
                            ) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_type')
                            ->dropdownList(
                                $model->getCourseTypeOptios(),
                                [
                                    'prompt' => 'Course Entry Type',
                                ]
                            ) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_code')->textInput(['maxlength' => true, 'value' => 'CT-']) ?>
                    </div>
                    <div class="col-md-4">
                        <?= $form->field($model, 'course_title')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_crdthr')->textInput() ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_lecture_hour')->textInput() ?>
                    </div>

                    <div class="col-md-2">
                        <?= $form->field($model, 'course_lab_hour')->textInput() ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'course_tutorial_hour')->textInput() ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'course_ETCS')->textInput() ?>
                    </div>
                    <div class="col-md-1"><br>
                        <div class="form-group pull-right" style="margin-top: 7px;">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
                Course
            </div>
            <div class="panel-body table-responsive">
                <?php Pjax::begin(); ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],


                        'course_code',
                        'course_title',
                        'course_crdthr',
                        'course_ETCS',
                        [
                            'attribute' => 'course_dept_relId',
                            'value' => 'courseDeptRel.dept_code',
                            'filter' => ArrayHelper::map(\backend\models\Course::find()->groupBy('course_dept_relId')->all(), 'course_dept_relId', function ($model) {
                                return $model->courseDeptRel->dept_name;
                            }),
                        ],
                        [
                            'attribute' => 'course_entry_type',
                            'value' => function ($model) {
                                return $model->course_entry_type == 1 ? 'Regular' : ($model->course_entry_type == 2 ? 'Transfer' : 'Special');
                            },
                        ],
                        [
                            'attribute' => 'course_type',
                            'format' => 'raw',
                            'value' => function ($model) {
                                // $btnChange = "<button class='btn btn-xs btn-warning pull-right' title='Change Course Type' onclick='changeCourseType({$model->id});return false;'><span class='glyphicon glyphicon-edit'></span></button>";
                                $type = $model->getCourseTypeName();
                                return $type ;
                            },
                            'filter' => $model->getCourseTypeOptios(),
                        ],
                        'course_lecture_hour',
                        'course_lab_hour',
                        'course_tutorial_hour',
                        [
                            'attribute' => 'course_created_by',
                            'header' => 'Reg. By',
                            'value' => 'courseCreatedBy.username',
                        ],
                        // 'course_created_at',
                        // 'course_updated_by',
                        // 'course_updated_at',

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>