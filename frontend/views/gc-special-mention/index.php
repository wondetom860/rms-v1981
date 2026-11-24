<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\GcSpecialMentionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Graduating Class Special Mentions');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid" style="background-color: #888; padding: 10px;">
    <?php $form = ActiveForm::begin(['action'=>'/gc-special-mention/create']); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_maxCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_minCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3"><br>
            <div class="form-group" style="margin-top: 5px;">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="gc-special-mention-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'gsm_maxCGPA',
                'value'=>function($model){
                    return sprintf("%0.3f",$model->gsm_maxCGPA);
                }
            ],
            [
                'attribute'=>'gsm_minCGPA',
                'value'=>function($model){
                    return sprintf("%0.3f",$model->gsm_minCGPA);
                }
            ],
            'gsm_name',
            [
                'attribute'=>'gsm_created_by',
                'value'=>function($model){
                    return $model->gsmCreatedBy->first_name .' '. $model->gsmCreatedBy->middle_name[0];
                }
            ],
            // 'gsm_created_at',
            // 'gsm_updated_at',
            // 'gsm_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
