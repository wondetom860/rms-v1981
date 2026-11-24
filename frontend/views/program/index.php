<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ProgramSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */


$this->title = Yii::t('decirs', 'Programs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
     <div class="col-md-3">
        <?php $form = ActiveForm::begin(['action'=>'/program/create']); ?>
        <div class="panel panel-primary">
            <div class="panel-heading">
                 Create/Add new Program
            </div>
            <div class="panel-body" style="background-color: #dd9;">
                <div class="row">
                    <div class="col-md-12">
                         <?= $form->field($model, 'program_adm_relId')->dropDownList(ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),['prompt'=>'Select Admission']) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'program_name')->textInput(['maxlength' => true]) ?>
                    </div>
                         <div class="col-md-12">
                              <?= $form->field($model, 'program_duration')->textInput() ?> 
                         </div>
                         <div class="col-md-12">
                              <?= $form->field($model, 'total_crdt_hrs')->textInput() ?> 
                         </div>
                      <div class="col-md-12">
                         <?= $form->field($model, 'program_desc')->textarea(['rows' => 2]) ?>  
                     </div>
                        
                    <div class="col-md-12"><br>
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <div class="col-md-9" style="font-size: 9pt;">
        <div class="panel panel-primary">
            <div class="panel-heading">
                 Programs
            </div>
            <div class="panel-body">
                <?php Pjax::begin(); ?>    <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],

                        [
                            'attribute'=>'program_adm_relId',
                            'value'=>'programAdmRel.admission_name',
                        ],
                        'program_name',
                        'program_duration',
                        // [
                        //     'attribute'=>'program_status',
                        //     'value'=>function($model){
                        //         return $model->program_status == 1?'ACTIVE':'NOT ACTIVE';
                        //     },
                        // ],
                        'program_prefix',
                        'total_crdt_hrs',
                        'program_desc:ntext',
                        [
                            'attribute'=>'program_created_by',
                            'value'=>'programCreatedBy.username',
                        ],
                        // 'program_created_at',
                        // 'program_updated_by',
                        // 'program_updated_at',

                        ['class' => 'yii\grid\ActionColumn'],
                    ],
                ]); ?>
                <?php Pjax::end(); ?>
            </div>
        </div>
    </div>
</div>
