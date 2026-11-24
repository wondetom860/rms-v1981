<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ClassLevelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('rms', 'Class Level');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="class-level-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/class-level/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new Class Level
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                             <div class="col-md-3">
                                     <?= $form->field($model, 'clslvl_admission_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                            [
                                'option'=>'Select Admission',
                                'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('admission/programlists').'",
                                                        {
                                                            admId:$(this).val()
                                                        }, 
                                                        function( data ) {
                                                        // console.log("Returned: "+data);/
                                                      //$( "select#coursecatalog-coff_clslvl_relid" ).html( data );
                                                    });
                                                   // console.log("Program Change made:");',
                            ]
                        ) ?>
                                 </div>
                                  <div class="col-md-3">
                                      <?= $form->field($model, 'clslvl_program_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                            [
                                'option'=>'Select program',
                                'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('program/clslvllists').'",
                                                        {
                                                            admId:$(this).val()
                                                        }, 
                                                        function( data ) {
                                                        console.log("Returned: "+data);
                                                      //$( "select#coursecatalog-coff_clslvl_relid" ).html( data );
                                                    });
                                                   // console.log("Program Change made:");',
                            ]
                        ) ?>
                                 </div>
                              <div class="col-md-3">
                                   <?= $form->field($model, 'clslvl_name')->textInput(['maxlength' => true]) ?> 
                                 </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'clslvl_num')->textInput(['maxlength'=>true,]) ?> 
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
                         Class Level
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            [
                'attribute'=>'clslvl_admission_relId',
                'value'=>'clslvlAdmissionRel.admission_name',
            ],
            [
                'attribute'=>'clslvl_program_relId',
                'value'=>'clslvlProgramRel.program_name',
            ],
            [
                'attribute' => 'clslvl_name',
                'value' => function ($model)
                {
                    return $model->clslvl_name."({$model->clslvl_num})";
                }
            ],
            //'clslvl_dept_relId',
            [
                'attribute'=>'clslvl_status',
                'value'=>function($model){
                    return $model->clslvl_status == 1?'ACTIVE':'NOT ACTIVE';
                }
            ],
            // 'clslvl_created_by',
            // 'clslvl_created_at',
            // 'clslvl_updated_by',
            // 'clslvl_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
