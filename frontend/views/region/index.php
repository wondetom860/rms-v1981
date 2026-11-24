<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AdmissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Region');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region-index">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!-- 
    <p>
        <?= Html::a(Yii::t('decirs', 'Create Region'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/region/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Add new Region
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model,'region_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-4"><br>
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
                         Region
                    </div>
                    <div class="panel-body">
                        <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'region_name',
                              
                                [
                                    'attribute'=>'region_created_by',
                                    'value'=>function($model){

                                        return \backend\models\User::findOne(['id'=>$model->region_created_by])->username;
                                    },
                                ],
                                [
                                    'attribute'=>'region_updated_by',
                                    'value'=>function($model){

                                        return \backend\models\User::findOne(['id'=>$model->region_created_by])->username;
                                    },
                                ],
                                //'region_created_at',
                                // 'admission_updated_by',
                                // 'admission_updated_at',

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
