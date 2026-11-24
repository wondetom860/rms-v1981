<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\Depal\models\DepalChoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Department Choices');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal'), 'url' => ['/Depal']];
$this->params['breadcrumbs'][] = $this->title;
$modelCategorized = new frontend\modules\Depal\models\DepalCategorizedCandidates();
?>
<div class="depal-choice-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?= \Yii::$app->user->can('Student') ? $modelCategorized->getUnClosedDepalSchedule() : '' ?>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            
            [
                'attribute' => 'std_regId',
                'value' => function($model){ return $model->stdRel->std_regId; }
            ],
            [
                'attribute' => 'std_relId',
                'value' => function($model){ return $model->stdRel->getFullName(); }
            ],[
                'attribute' => 'setting_relId',
                'format' => 'raw',
                'value' => function($model){ return $model->settingRel->sectionRel->getClassLevelHeader(); },
                'filter' => ArrayHelper::map(\frontend\modules\Depal\models\DepalSetting::find()->all(),'id',function($model){ return $model->sectionRel->academicYearRel->getAcHeader();})
            ],
            
            [
                'attribute' =>'categorized_under',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getCategorizedUnder($model->stdRel,$model->settingRel);
                },
            ],[
                'attribute' =>'dept_selected',
                'format' => 'raw',
                'value' => function($model){
                    return $model->dept_selected ? $model->stringifyDepartments() : '-';
                }
            ],
            [
                'attribute' => 'secured_dept_relId',
                'value' => function($model){
                    return $model->secured_dept_relId?$model->securedDeptRel->dept_code : '-';
                },
                'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
            ],
            'finalizad',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => function($model){
                    if(Yii::$app->user->can('Student')){
                        return ($model->settingRel->status == 0);
                    }else{
                        return true;
                    }
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
<script type="text/javascript">
    const placeChoice = (categorizedId) => {
        $('#modal').find('.modal-header').html('List your department choice in order');
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/Depal/depal-choice/get-dept-selection-form',{categorizedId:categorizedId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
</script>

<?php

 Modal::begin([
    'header'=>'<h4>Department Selection window</h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent'>Loading form...</div>";
 Modal::end();

 ?>