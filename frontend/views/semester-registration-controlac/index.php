<?php

use backend\models\Admission;
use backend\models\Department;
use backend\models\Program;
use backend\models\SchoolYear;
use backend\models\Semester;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CoursePrerequisitSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration Control(AC)');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-controlr-index">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>
    </h3>
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(['action' => '/semester-registration-controlac/create']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                Create Seemster Load Limit (AC) - <b>By Admin or Registerar</b>
            </div>
            <div class="panel-body" style="background-color: #dd9;">
                <div class="row ">
                    <div class="container-fluid table-responsive">

                        <div class="col-md-2">
                            <?= $form->field($model, 'src_acadYear_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                                [
                                    'prompt' => 'Select Academic Year.'
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_adm_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                                [
                                    'prompt' => 'Select Admission.',
                                    'onChange' => '
                                            $.post("/program/getprogs",{},function(data){
                                                 $("#semesterregistrationcontrolac-src_prog_relid").html(data).trigger("change");
                                                });
                                        ',
                                ]
                            ) ?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'src_prog_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                [
                                    'prompt' => 'Select Program',
                                    'onChange' => ' $.post( "' . Yii::$app->urlManager->createUrl('program/clslvllists') . '",
                                    {
                                        progId:$(this).val()
                                    }, 
                                    function( data ) {
                                    //console.log("Returned: "+data);
                                  $( "select#semesterregistrationcontrolac-src_clslvl_relid" ).html( data ).trigger("change");
                                });
                                // coffLoad();',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_clslvl_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                    'prompt' => 'Select Class Level',
                                    'onChange' => '
                                        $("#semesterregistrationcontrolac-src_sem_relid").trigger("change");
                                    ',
                                ]
                            ) ?>
                        </div>

                        <div class="col-md-2">
                            <?= $form->field($model, 'src_dept_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                [
                                    'prompt' => 'Select department',
                                    'onChange' => '
                                            console.log($(this).val());
                                            $.post(
                                                "/specialization/listspec-cp",
                                                {
                                                    deptId:$(this).val(),
                                                    admId:$("#semesterregistrationcontrolac-src_adm_relid").val(),
                                                    progId:$("#semesterregistrationcontrolac-src_prog_relid").val(),
                                                    },
                                                function(data){
                                                    $("#semesterregistrationcontrolac-src_spec_relid").html(data).trigger("change");
                                                    // console.log(data);
                                                }
                                            );
                                            ',

                                ]
                            ) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_spec_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'),
                                [
                                    'prompt' => 'Select Spec.',
                                    'onChange' => '$("#semesterregistrationcontrolac-src_sem_relid").trigger("change");'
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_sem_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                                [
                                    'prompt' => 'Select semester.',
                                    'onChange' => '
                                  $.post("/w-group-setup/get-group-lists-peryear",
                                    {
                                      acadYearId:$("#semesterregistrationcontrolac-src_acadyear_relid").val(),
                                      semId:$("#semesterregistrationcontrolac-src_sem_relid").val(),
                                      admId:$("#semesterregistrationcontrolac-src_adm_relid").val(),
                                      progId:$("#semesterregistrationcontrolac-src_prog_relid").val(),
                                      clslvlId:$("#semesterregistrationcontrolac-src_clslvl_relid").val(),
                                      deptId:$("#semesterregistrationcontrolac-src_dept_relid").val(),
                                      specId:$("#semesterregistrationcontrolac-src_spec_relid").val()
                                    }, 
                                    function( data ) {
                                        console.log("Returned: "+data);
                                        $("select#semesterregistrationcontrolac-src_group_relid" ).html( data ).trigger("change");
                                    }); 
                                ',
                                ]
                            )->label('Semester') ?>
                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($model, 'src_group_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\WGroup::find()->all(), 'id', 'group_name'),
                                [
                                    'prompt' => 'Select group.',
                                    'onChange' => 'loadInsts()',
                                ]
                            )->label('Group')
                            ?></div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_max_crdthr')->textInput() ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_min_crdthr')->textInput() ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'src_remark')->textarea() ?>
                        </div>

                    </div>
                    <div class="col-md-2"><br>
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="col-md-12">
    <div class="panel panel-primary">

        <div class="panel-body table-responsive">
            <!--  <div class="panel-body container-fluid">   -->
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'src_acadYear_relId',
                        'value' => function ($model) {
                            return is_null($model->srcAcadYearRel) ? '' : $model->srcAcadYearRel->sch_year_desc;
                        },
                        'filter' => ArrayHelper::map(SchoolYear::find()->all(), 'id', 'sch_year_desc')

                    ],

                    [
                        'attribute' => 'src_sem_relId',
                        'value' => function ($model) {
                            return $model->srcSemRel->semester_name;
                        },
                        'filter' => ArrayHelper::map(Semester::find()->all(), 'id', 'semester_name')
                    ],

                    [
                        'attribute' => 'src_adm_relId',
                        'value' => function ($model) {
                            return $model->srcAdmRel ? $model->srcAdmRel->admission_name : 'For all';
                        },
                        'filter' => ArrayHelper::map(Admission::find()->all(), 'id', 'admission_name')
                    ],
                    [
                        'attribute' => 'src_prog_relId',
                        'value' => function ($model) {
                            return $model->srcProgRel ? $model->srcProgRel->program_name : 'For All';
                        },
                        'filter' => ArrayHelper::map(Program::find()->all(), 'id', 'program_name')
                    ],

                    [
                        'attribute' => 'src_dept_relId',
                        'value' => function ($model) {
                            return $model->srcDeptRel ? $model->srcDeptRel->dept_name : 'For All';
                        },
                        'filter' => ArrayHelper::map(Department::find()->all(), 'id', 'dept_name')
                    ],

                    [
                        'attribute' => 'src_clslvl_relId',
                        'value' => function ($model) {
                            return is_null($model->src_clslvl_relId) ? 'All' : \backend\models\ClassLevel::findOne(['id' => $model->src_clslvl_relId])->clslvl_name;
                        }
                    ],
                    [
                        'attribute' => 'src_spec_relId',
                        'value' => function ($model) {
                            return is_null($model->src_spec_relId) ? 'All' : \backend\models\Specialization::findOne(['id' => $model->src_spec_relId])->spec_name;
                        }
                    ],
                    [
                        'attribute' => 'src_group_relId',
                        'value' => function ($model) {
                            if (is_null($model->src_group_relId)) {
                                return 'All';
                            } else {
                                $gset = $model->srcGroupRel;
                                if ($gset) {
                                    return $gset->gsGroupRel->group_name;;
                                } else {
                                    $model->delete(); //setup for non existing group: what waiting here: remove it!!! (::)
                                }
                            }
                        }
                    ],
                    'src_min_crdthr',
                    'src_max_crdthr',
                    // 
                    // 
                    // 'preq_created_by',
                    // 'preq_created_at',
                    // 'preq_updated_by',
                    // 'preq_updated_at',
                    // 'remark:ntext',

                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>