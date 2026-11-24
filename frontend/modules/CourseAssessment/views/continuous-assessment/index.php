<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\CourseAssessment\models\ContinuousAssessmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Continuous Assessments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="continuous-assessment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Continuous Assessment'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ca_std_relid',
            'ca_semreg_relId',
            'ca_markAssignment_relId',
            'ca_catagorySetup_relId',
            //'ca_markObtained',
            //'ca_coff_relId',
            //'ca_created_by',
            //'ca_created_at',
            //'ca_updated_by',
            //'ca_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
