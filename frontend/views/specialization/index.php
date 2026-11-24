<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SpecializationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = "Specialization/Section";
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="specialization-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!--  <p>
        <?= Html::a(Yii::t('decirs', 'Create Specialization'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
<div class="col-md-12">
    <?php $form = ActiveForm::begin(['action'=>'/specialization/create']); ?>
    <div class="panel panel-primary">
        <div class="panel-heading">
             Create new Specialization
        </div>
        <div class="panel-body" style="background-color: #dd9;">
            <div class="row">
                  <div class="col-md-2">
                     <?= $form->field($model, 'spec_adm_relId')->dropDownList(
                        ArrayHelper::map(
                                \backend\models\Admission::find()->all(), 'id', 'admission_name'
                        ), ['prompt' => 'Select Admission','onchange'=>'loadSpecs()',]
                ) ?>
                     </div>
                     <div class="col-md-2">
                     <?= $form->field($model, 'spec_prog_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                        [
                            'prompt'=>'Select Program',
                            'onchange'=>'loadSpecs()',
                        ]
                    ) ?>
                      </div>
               
                 <div class="col-md-3">
                     <?= $form->field($model, 'spec_dep_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                        [
                            'prompt'=>'Select Department',
                            'onchange'=>'loadSpecs()',
                        ]
                    ) ?>
                </div>
                <div class="col-md-3">
                   <?= $form->field($model, 'spec_name')->textInput(['maxlength' => true]) ?>
                </div>
                <div class="col-md-2">
                   <?= $form->field($model, 'spec_code')->textInput(['maxlength' => true]) ?>
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
            Specialization
        </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'spec_code',
            [
                'attribute'=>'spec_adm_relId',
                'value'=>'specAdmRel.admission_name',
                'filter' => ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
            ],
            [
                'attribute'=>'spec_prog_relId',
                'value'=>'specProgRel.program_name',
                'filter' => ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name')
            ],
            [
                'attribute'=>'spec_dep_relId',
                'value'=>'specDepRel.dept_name',
                'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name')
            ],
            
            'spec_name',
           
            //'spec_status',
            [
                'attribute'=>'spec_created_by',
                'value'=>'specCreatedBy.username',
            ],
             //'spec_desc:ntext',
            // 'spec_created_at',
            // 'spec_updated_at',
            // 'spec_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
