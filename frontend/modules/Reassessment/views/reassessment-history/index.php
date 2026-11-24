<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\Reassessment\models\ReassessmentHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Reassessment Requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="reassessment-history-index">

    <h3><?= Html::encode($this->title) ?>

        <p>
            <?= Yii::$app->user->can('Student') ? Html::a(Yii::t('rms', 'New Reassessment History'), ['create'], ['class' => 'btn btn-success btn-xs pull-right']) : '' ?>
        </p>
    </h3>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'ras_acadYear_relId',
                'value' => function ($model) {
                    return $model->rasAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\ReassessmentHistory::find()->groupBy('ras_acadYear_relId')->all(), 'ras_acadYear_relId', function ($model) {
                    return $model->rasAcadYearRel->sch_year_desc;
                }),
            ],
            [
                'attribute' => 'ras_sem_relId',
                'value' => function ($model) {
                    return $model->rasSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\ReassessmentHistory::find()->groupBy('ras_sem_relId')->all(), 'ras_sem_relId', function ($model) {
                    return $model->rasSemRel->semester_name;
                }),
            ],
            [
                'attribute' => 'ras_std_relId',
                'value' => function ($model) {
                    return $model->rasStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'course_title',
                'value' => function ($model) {
                    return $model->rasCourseRel->course_title;
                }
            ],
            //'ras_status',
            //'ras_registrarApprove_status',
            //'ras_registrarApprove_by',
            //'ras_registrarApprove_at',
            //'ras_approved_by',

            [
                'attribute' => 'ras_prevMark',
                'value' => function ($model) {
                    return sprintf("%0.3f", $model->ras_prevMark);
                }
            ],
            [
                'attribute' => 'ras_bef_grade_relId',
                'value' => function ($model) {
                    return $model->ras_bef_grade_relId ? $model->rasBefGradeRel->gs_ltr_grade : NULL;
                }
            ],

            //'ras_app_date',
            //'ras_created_at',
            //'ras_updated_at',
            //'ras_updated_by',

            [
                'attribute' => 'ras_afterMark',
                'value' => function ($model) {
                    return $model->ras_afterMark ? sprintf("%0.3f", $model->ras_afterMark) : NULL;
                }
            ],
            [
                'attribute' => 'ras_aft_grade_relId',
                'value' => function ($model) {
                    return $model->ras_aft_grade_relId ? $model->rasAftGradeRel->gs_ltr_grade : NULL;
                }
            ],
            //'ras_contAssCatag_relId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>