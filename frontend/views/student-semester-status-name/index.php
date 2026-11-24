<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Student Semester Status Name Configuration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-semester-status-name-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(['action'=>'/student-semester-status-name/create']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                 Create new Department
            </div>
            <div class="panel-body" style="background-color: #dd9;">
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'sssn_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-5">
                        <?= $form->field($model, 'sssn_desc')->textarea(['rows' => 2]) ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'sssn_pass_status')->dropdownList([
                            1=>'Pass',2=>'Further Check',3=>'Fail'
                        ],['prompt'=>'Select Pass Status']) ?>
                    </div>
                    <div class="col-md-2"><br>
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
                 Department
            </div>
            <div class="panel-body  table-responsive">
                <?php Pjax::begin(); ?>    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        'sssn_name',
                        [
                            'attribute'=>'sssn_pass_status',
                            'value'=>function($model){
                                return $model->sssn_pass_status == 1?'Pass':($model->sssn_pass_status == 2?'Need Further Check':'Fail');
                            }
                        ],
                        'sssn_desc:ntext',

                        [
                            'class' => 'yii\grid\ActionColumn',
                            'header'=>'Options',
                            'visible'=>(\yii::$app->user->can('Admin')),
                            'options'=>[
                                'style'=>'overflow: auto; word-wrap: text-nowrap;',
                            ]
                        ],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
