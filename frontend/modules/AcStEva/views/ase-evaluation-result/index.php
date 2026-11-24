<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;

/** @var yii\web\View  $this
 * @var $searchModel frontend\modules\AcStEva\models\AseEvaluationResultSearch
 * @var $dataProvider yii\data\ActiveDataProvider 
 */

$this->title = Yii::t('rms', 'Evaluation Results');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-evaluation-result-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            // [
            //     'attribute' => 'Academic Year',
            //     'value' => function($model){
            //         return $model->controlSettingRel->deptPlanRel->getEvaluationheader();
            //     },
            //     'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcAcademicYear::find()->all(), 'id', function ($model) {
            //         return $model->getAcHeader();
            //     })
            // ],
            [
                'attribute' => 'control_setting_relId',
                'value' => function ($model) {
                    return $model->controlSettingRel->deptPlanRel->getEvaluationheader();
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcAcademicYear::find()->orderBy(['school_year' => SORT_DESC,'semester' => SORT_DESC])->all(), 'id', function ($model) {
                    return $model->getAcHeader();
                })
            ],
            [
                'attribute' => 'criteria_bundle_relId',
                'value' => function ($model) {
                    return $model->criteriaBundleRel->code;
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcStEva\models\AseCriteriaBundle::find()->all(), 'id', 'code')
            ],
            // 'completed_staffs',
            // [
            //     'attribute' => 'dept_plan_relId',
            //     'format' => 'raw',
            //     'value' => function ($model) {
            //         return $model->deptPlanRel->getScheduleInterval();
            //     }
            // ],
            [
                'attribute' => 'Staff Name',
                // 'format' => 'raw',
                'value' => function ($model) {
                    return $model->controlSettingRel->candidateStaffRel->getFullName();
                }
            ],
            [
                'attribute' => 'average_total',
                'value' => function($model){
                    return sprintf('%0.2f',$model->average_total);
                }
            ],
            [
                'attribute' => 'average_percentage',
                'value' => function($model){
                    $model->computePercentage();
                    // Ensure the average_percentage is computed before displaying
                    return sprintf('%0.2f',$model->average_percentage);
                }
            ],
           
            //'status',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>