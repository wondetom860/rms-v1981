<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Semester/Term';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!--  <p>
        <?= Html::a(Yii::t('rms', 'Create Semester'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <div class="col-md-12">
        <?php $form = ActiveForm::begin(['action' => '/semester/create']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                Create new Semester
            </div>
            <div class="panel-body" style="background-color: #dd9;">
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'semester_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'semester_desc')->textarea(['rows' => 1]) ?>
                    </div>

                    <!--  <div class="col-md-2">
                        <?= $form->field($model, 'semester_status')->dropDownList(
                            ['Active'],
                            [
                                'value' => 'Active',
                                'readonly' => true,
                                'title' => 'By default the semester is active',
                            ]
                        ) ?>
                            </div> -->

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
                Semester
            </div>
            <?php Pjax::begin(); ?> <?= GridView::widget([
                   'dataProvider' => $dataProvider,
                   'filterModel' => $searchModel,
                   'columns' => [
                       ['class' => 'yii\grid\SerialColumn'],

                       'semester_name',
                       [
                           'attribute' => 'semester_status',
                           'value' => function ($model) {
                   return $model->semester_status == 1 ? 'ACTIVE' : 'NOT ACTIVE';
               }
                       ],
                       'semester_desc:ntext',
                       [
                           'attribute' => 'semester_created_by',
                           'value' => 'semesterCreatedBy.username',
                       ],
                       // 'semester_created_at',
                       // 'semester_updated_by',
                       // 'semester_updated_at',
               
                       ['class' => 'yii\grid\ActionColumn'],
                   ],
               ]); ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>