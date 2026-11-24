<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ProjectGroupNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Project Group Names');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-group-name-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!--  <p>
        <?= Html::a(Yii::t('decirs', 'Create Project Group Name'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/project-group-name/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Add new Admission
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'pg_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'pg_size')->textInput() ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'remark')->textarea(['rows' => 2]) ?>
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
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Admissions
                    </div>
                    <div class="panel-body">
                        
                        <?php Pjax::begin(); ?>    <?= GridView::widget([
                                'dataProvider' => $dataProvider,
                                'filterModel' => $searchModel,
                                'columns' => [
                                    ['class' => 'yii\grid\SerialColumn'],

                                    'pg_name',
                                    'pg_size',
                                    'remark:ntext',

                                    ['class' => 'yii\grid\ActionColumn'],
                                ],
                            ]); ?>
                        <?php Pjax::end(); ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
</div>
