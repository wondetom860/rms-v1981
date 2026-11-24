<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\MakeupExamReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = Yii::t('decirs', 'Makeup Exam Reason');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="makeup-exam-reason-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

   <!--  <p>
        <?= Html::a(Yii::t('decirs', 'Create Makeup Exam Reason'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
     <div class="col-md-12">
                <?php $form = ActiveForm::begin(['action'=>'/makeup-exam-reason/create']); ?>
                <div class="panel panel-primary">
                    <div class="panel-heading">
                         Add new makeup exam reason
                    </div>
                    <div class="panel-body" style="background-color: #dd9;">
                        <div class="row">
                              <div class="col-md-3">
                                <?= $form->field($model, 'mer_desc')->textarea(['rows' => 3]) ?>
                                 </div>
                                  <div class="col-md-3">
                               <?= $form->field($model, 'remark')->textarea(['rows' => 3]) ?>
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
                         Available makeup exam reasons
                    </div>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'mer_desc:ntext',
            'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div></div>
