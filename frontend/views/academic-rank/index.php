<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AcademicRankSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Academic Rank');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-rank-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <!--   <p>
        <?= Html::a(Yii::t('decirs', 'Create Academic Rank'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/academic-rank/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new Department
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                               <?= $form->field($model, 'acr_name')->textInput(['maxlength' => true]) ?>
                                 </div>
                                 <div class="col-md-3">
                                <?= $form->field($model, 'acr_desc')->textarea(['rows' => 2]) ?>
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
                         Department
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'acr_name',
            'acr_desc:ntext',
            [
                'attribute'=>'acr_created_by',
                'value'=>'acrCreatedBy.username',
            ],
            'acr_created_at',
            // 'acr_updated_by',
            // 'acr_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div> </div>
