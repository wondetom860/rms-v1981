<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\DepartmentSelection\models\DepartmentSelectEntrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Department Select Entry');
$this->params['breadcrumbs'][] = ['label' => "Main", 'url' => ['/DepartmentSelection']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-select-entry-index" style="font-size: 10pt;">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right hidden">
        <?= Html::a(Yii::t('decris', 'Create Department Select Entry'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        // 'rowOption' => 'raw',
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'student_relId',
                'value' => function($model){
                    return $model->getStudentFullName();
                }
            ],
            [
                'attribute' => 'setting_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getSettingENtry();
                },
                'filter' => ArrayHelper::map(\frontend\modules\DepartmentSelection\models\DepartmentSelectEntry::find()->groupBy('setting_relId')->all(),'setting_relId',function($model){ return $model->getSettingENtry(1); }),
            ],
            
            [
                'attribute' => 'dept_choice',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getDeptSelection();
                }
            ],
            [
                'attribute' => 'approved_dept_relId',
                'value' => function($model){
                    return $model->approved_dept_relId? $model->approvedDeptRel->dept_name : 'NOT APPROVED YET';
                },
                'filter' => ArrayHelper::map(\frontend\modules\DepartmentSelection\models\DepartmentSelectEntry::find()->groupBy('approved_dept_relId')->all(),'approved_dept_relId',function($model){ 
                    return $model->approved_dept_relId? $model->approvedDeptRel->dept_name : null; 
                }),
            ],
            
            //'approved_at',
            //'approved_by',

            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date_format(date_create($model->created_at),"d/M/Y");
                }
            ],

            [
                'attribute' => 'approved_at',
                'value' => function($model){
                    return is_null($model->approved_at)?NULL:date_format(date_create($model->approved_at),"d/M/Y");
                }
            ],
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->settingRel->isScheduleActive() ? 'Active' : 'Not Active';
                }
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => function($model){ return $model->settingRel->isScheduleActive(); }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
