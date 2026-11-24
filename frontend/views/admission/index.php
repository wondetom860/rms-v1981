<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AdmissionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Admissions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="admission-index">
    <br>
    <div class="container-fluid">
        <div class="row">
            <div class="col-xs-4 left-padding hidden">
                <?= Html::a(Yii::t('course', 'PDF'), ['/export-data/export-to-pdf', 'model'=>get_class($searchModel)], ['class' => 'btn btn-block btn-warning', 'target'=>'_blank']) ?>
            </div>
            
            <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/admission/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <?= Yii::t('rms','Add new Admission') ?>
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'admission_name')->textInput(['maxlength' => true]) ?>
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
                         Admissions
                         <?= Html::a(Yii::t('course', 'EXCEL'), ['/export-data/export-excel', 'model'=>get_class($searchModel)], ['class' => 'btn btn-sm btn-warning pull-right', 'target'=>'_blank']) ?>
                    </div>
                    <div class="panel-body">
                        <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                'admission_name',
                                [
                                    'attribute'=>'admission_status',
                                    'value'=>function($model){
                                            return $model->admission_status == 1?'ACTIVE':'NOT ACTIVE';
                                        },
                                ],
                                [
                                    'attribute'=>'admission_created_by',
                                    'value'=>function($model){

                                        return \backend\models\User::findOne(['id'=>$model->admission_created_by])->username;
                                    },
                                ],
                                [
                                    'attribute'=>'admission_created_at',
                                    'value'=>function($model){
                                        return date_format(date_create($model->admission_created_at),'d M,Y');
                                    }
                                ],
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
