<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\DepartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('rms', 'Department');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!--  <p>
        <?= Html::a(Yii::t('rms', 'Create Department'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/department/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new Department
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                                <?= $form->field($model, 'dept_name')->textInput(['maxlength' => true]) ?>
                                 </div>
                                 <div class="col-md-3">
                                <?= $form->field($model, 'dept_location')->textInput(['maxlength' => true]) ?>
                                  </div>
                           
                             <div class="col-md-2">
                                <?= $form->field($model, 'dept_phone')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($model, 'dept_code')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-2">
                                <?= $form->field($model, 'dept_id_prefix')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-2"><br>
                                <div class="form-group">
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
                         Department
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'dept_code',
            'dept_name',
            'dept_location',
            'dept_phone',
            'dept_id_prefix',
            // 
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status ? "Active":"Inactive";
                }
            ],
            // 'dept_created_at',
            // 'dept_updated_at',
            // 'dept_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div> </div></div>
