<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\RemovedSrd2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Removed Semester Enrols');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="removed-srd2-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?php // Html::a(Yii::t('rms', 'Create Removed Srd2'), ['create'], ['class' => 'btn btn-success']) 
            ?>
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
                'attribute' => 'std_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->stdRel->getFullName(), ['student/view', 'id' => $model->std_relId]);
                }
            ],
            [
                'attribute' => 'semreg_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->semregRel->getSemregHeader() . " >> " . $model->semregRel->semregClslvlRel->getClslvlDisp();
                }
            ],
            // [
            //     'attribute' => 'coff2det_relId',
            //     'format' => 'raw',
            //     'value' => function ($model) {
            //         return $model->coff2detRel->coff2detCoff2Rel->getCourseOfferingHeader();
            //     }
            // ],
            [
                'attribute' => 'Course Ttitle',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coff2detRel->coff2detCourseRel->course_title;
                }
            ],
            'total_mark',
            [
                'attribute' => 'grade_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->gradeRel ? $model->gradeRel->gs_ltr_grade : '-';
                }
            ],
            [
                'attribute' => 'hod_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $text = $model->hod_approved == 1 ? 'Yes' : 'No';

                    return $text;
                }
            ],
            [
                'attribute' => 'reg_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $text = $model->reg_approved == 1 ? 'Yes' : 'No';

                    return $text;
                }
            ],
            //'hod_approved_at',
            //'hod_approved_by',
            //'reg_approved',
            //'reg_approved_at',
            //'reg_approved_by',
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