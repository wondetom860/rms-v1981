<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\NationalitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Nationality');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nationality-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

  <!--   <p>
        <?= Html::a(Yii::t('decirs', 'Add New Nationality'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/nationality/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Create new Department
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                               <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
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
                         Department
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
