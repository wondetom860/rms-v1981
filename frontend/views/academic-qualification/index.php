<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AcademicQualificationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Academic Qualifications');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-qualification-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
<!-- 
    <p>
        <?= Html::a(Yii::t('decirs', 'Add New Academic Qualification'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/academic-qualification/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                          Register new Academic Qualification
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                                <?= $form->field($model, 'acq_name')->textInput(['maxlength' => true]) ?>
                                 </div>
                                 <div class="col-md-3">
                                <?= $form->field($model, 'acq_desc')->textarea(['rows' => 3]) ?>
                                  </div>
                        
                            <div class="col-md-2"><br>
                                <div class="form-group">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
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
                    Academic Qualification
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'acq_name',
            'acq_desc:ntext',
            [
                'attribute'=>'acq_created_by',
                'value'=>'acqCreatedBy.username',
            ],
            'acq_created_at',
            // 'acq_updated_by',
            // 'acq_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
