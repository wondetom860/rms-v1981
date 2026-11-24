<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>

    <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/cont-assess-catagory-name/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create New Continous Assessement Name
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                                 <?= $form->field($model, 'cont_cat_name')->textInput(['maxlength' => true]) ?>
                             </div>
                                 <div class="col-md-3">
                                <?= $form->field($model, 'remark')->textarea(['rows' => true]) ?>
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
                         Contionous Assessement
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'cont_cat_name',
            'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
