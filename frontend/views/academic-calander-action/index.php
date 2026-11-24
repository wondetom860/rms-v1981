<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AcademicCalanderActionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Academic Calander Action');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-action-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!--  <p>
        <?= Html::a(Yii::t('decirs', 'Create Academic Calander Action'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/academic-calander-action/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new  Academic Calander Action
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                               <?= $form->field($model, 'aca_name')->textInput(['maxlength' => true]) ?>
                                 </div>
                                 <div class="col-md-3">
                                <?= $form->field($model, 'aca_link')->textInput(['maxlength' => true,'placeholder'=>'Optional']) ?>
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
                         Academic Calander Action
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'aca_name',
            [
                'attribute'=>'aca_created_by',
                'value'=>'acaCreatedBy.username',
            ],
            'aca_created_at',
            'aca_link',
            // 'aca_updated_by',
            // 'aca_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
