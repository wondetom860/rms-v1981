<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\GradeScaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Grade Scales');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="grade-scale-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    if (Yii::$app->user->can('Admin')) {
        $form = ActiveForm::begin(['action' => '/grade-scale/create']); ?>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add new grade scale detail
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->field($model, 'gs_forProgram')->dropdownList(
                                        ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                        [
                                            'prompt' => 'Select Program..',
                                        ]
                                    ) ?>

                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'gs_min')->textInput(['placeholder' => '80.50']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'gs_max')->textInput(['placeholder' => '84.00']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'gs_ltr_grade')->textInput(['maxlength' => true, 'placeholder' => 'B+']) ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($model, 'gs_gradePoint')->textInput(['placeholder' => '3.50']) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3"><?= $form->field($model, 'gs_scale_status')->dropdownList(
                                    [
                                        1 => 'Active',
                                        0 => 'Not Active',
                                    ]
                                ) ?></div>
                                <div class="col-md-3"><?= $form->field($model, 'gs_pass_status')->dropdownList(
                                    [
                                        1 => 'Pass',
                                        0 => 'Fail',
                                    ]
                                ) ?></div>
                                <div class="col-md-3"><?= $form->field($model, 'gs_clearable')->dropdownList(
                                    [
                                        1 => 'Yes',
                                        0 => 'No',
                                    ],
                                    ['prompt' => 'Grade clearable?']
                                ) ?></div>
                                <div class="col-md-3"><?= $form->field($model, 'gs_remark')->textarea(['rows' => 1]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer">

                            <div class="form-group">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php ActiveForm::end();
    } ?>
    <?php Pjax::begin(); ?> <?= GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],

               [
                   'attribute' => 'gs_forProgram',
                   'value' => 'gsForProgram.program_name',
                   'filter' => ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
               ],
               [
                   'attribute' => 'gs_min',
                   'value' => function ($model) {
                          return sprintf("%.02f", $model->gs_min);
                      }
               ],
               [
                   'attribute' => 'gs_max',
                   'value' => function ($model) {
                          return sprintf("%.02f", $model->gs_max);
                      }
               ],
               [
                   'attribute' => 'gs_ltr_grade',
                   'value' => 'gs_ltr_grade',
                   'filter' => ArrayHelper::map($model->find()->all(), 'gs_ltr_grade', 'gs_ltr_grade'),
               ],
               [
                   'attribute' => 'gs_gradePoint',
                   'value' => function ($model) {
                          return sprintf("%.02f", $model->gs_gradePoint);
                      }
               ],
               [
                   'attribute' => 'gs_scale_status',
                   'value' => function ($model) {
                          return $model->gs_scale_status ? "Active" : "Not-Active";
                      },
                   'filter' => ArrayHelper::map($model->find()->all(), 'gs_scale_status', function ($model) {
                          return $model->gs_scale_status ? "Active" : "Non-Active";
                      }),
               ],
               [
                   'attribute' => 'gs_pass_status',
                   'value' => function ($model) {
                          return $model->gs_pass_status ? "Pass" : "Fail";
                      },
                   'filter' => ArrayHelper::map($model->find()->all(), 'gs_pass_status', 'gs_pass_status'),
               ],
               [
                   'attribute' => 'gs_clearable',
                   'value' => function ($model) {
                          return $model->gs_clearable == 0 ? 'NO' : 'YES';
                      },
                   'filter' => ArrayHelper::map($model->find()->all(), 'gs_clearable', function ($k) {
                          return $k->gs_clearable == 1 ? 'YES' : 'NO';
                      }),
               ],
               [
                   'attribute' => 'gs_locked',
                   'value' => function ($model) {
                          return $model->gs_locked == 1 ? 'Locked' : "NOT Locked";
                      }
               ],
               [
                   'attribute' => 'Option',
                   'visible' => Yii::$app->user->can('Registrar'),
                   'format' => 'raw',
                   'value' => function ($model) {
                          return $model->gs_locked ?

                              Html::a(
                                  Yii::t('rms', 'UnLock'),
                                  [''],
                                  [
                                      'class' => 'btn btn-warning btn-xs',
                                      'data-pjax' => 0,
                                      'title' => 'Unloack this grade interval.',
                                      'value' => $model->id,
                                      'onclick' => 'changeLockStatus(0,' . $model->id . ');return false;',
                                  ]
                              ) :
                              Html::a(
                                  Yii::t('rms', 'Lock'),
                                  [''],
                                  [
                                      'class' => 'btn btn-danger btn-xs',
                                      'data-pjax' => 0,
                                      'title' => 'Loack grade interval',
                                      'value' => $model->id,
                                      'onclick' => 'changeLockStatus(1,' . $model->id . ');return false;',
                                  ]
                              );
                      }
               ],
               // 'gs_created_by',
               // 'gs_created_at',
               // 'gs_updated_by',
               // 'gs_updated_at',
       
               [
                   'class' => 'yii\grid\ActionColumn',
                   'visible' => Yii::$app->user->can('Admin'),
               ],
           ],
       ]); ?>
    <?php Pjax::end(); ?>
</div>

<script>
    window.changeLockStatus = function (option,id) {
        status = option == 0 ? 'Unlock' : 'Lock';
        if (confirm('Sure to ' + status + ' the grade scale setting?')) {
            $.post('/grade-scale/change-lock-status-single', { option: option,id:id }).done(function (data) {
                if (data == 1) {
                    window.location.href = window.location;
                } else {
                    $('#wt_' + id).html(data);
                }
            });
        }
    }
</script>