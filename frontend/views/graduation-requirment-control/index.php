<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\GraduationRequirmentControlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Graduation Requirment Control');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-requirment-control-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

 <!--    <p>
        <?= Html::a(Yii::t('decirs', 'Create Graduation Requirment Control'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/graduation-requirment-control/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new Graduation Requirment Control
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                                <?= $form->field($model, 'gr_program_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                    [
                                        'prompt'=>'Select Program',
                                        'onchange'=>'loadSpecs()',
                                    ]
                                ) ?>
                                 </div>
                                 <div class="col-md-3">
                                 <?= $form->field($model, 'gr_maj')->textInput(['maxlength' => true]) ?>
                                  </div>
                                   <div class="col-md-3">
                                <?= $form->field($model, 'gr_min')->textInput(['maxlength' => true]) ?>
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
                         Graduation Requirment Control
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
              [
                'attribute'=>'gr_program_relId',
                'value'=>'grProgramRel.program_name',
            ],
            

           // 'id',
            // [
            //     'attribute'=>'gr_program_relId',
            //     'value'=>'grProgramRel.program_name',
            // ],
            'gr_maj',
            'gr_min',
            'gr_created_at',
            // 'gr_created_by',
            // 'gr_updated_at',
            // 'gr_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
