<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ThesisTitleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Thesis Titles');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="thesis-title-index table-responsive">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Enter Thesis Title'), ['/upload-thesis-title'], ['class' => 'btn btn-success btn-xs']) ?>
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
                'attribute' => 'tt_srd2_relId',
                'value' => function ($model) {
                    return $model->ttSrd2Rel->srd2SemregRel->getAcadYearHeader();
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy(['semreg_acadYear_relId', 'semreg_semester_relId'])->all(), 'semreg_acadYear_relId', function ($model) {
                    return $model->getAcadYearHeader();
                })
            ],
            [
                'attribute' => 'std_column',
                'value' => function ($model) {
                    return "{$model->ttStdRel->std_regId} : " . $model->ttStdRel->getFullName();
                },
            ],
            [
                'attribute' => 'tt_title',
                'format' => 'raw',
                'value' => function ($model) {
                    if (file_exists('uploads/thesisTitles/' . $model->tt_title)) {
                        return substr(file_get_contents('uploads/thesisTitles/' . $model->tt_title), 0, 59) . '...';
                    } else {
                        return "<span style='color:red; font-style: italic;'>File not found.</span>";
                    }
                }
            ],
            [
                'attribute' => 'tt_approved',
                'value' => function ($model) {
                    return $model->tt_approved ? 'Approved' : 'Not Yet';
                }
            ],
            //'tt_approved_by',
            //'tt_approved_at',
            //'tt_hod_approved',
            //'tt_hod_approved_at',
            //'tt_hod_approved_by',
            //'tt_graded',

            [
                'class' => 'yii\grid\ActionColumn'
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>