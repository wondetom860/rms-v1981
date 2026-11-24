<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ClearanceAllowedStudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Clearance Allowed Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="clearance-allowed-student-index">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Allow Clearance Form'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'cas_full_name',
            'cas_regId',
            'cas_acadYear',
            'cas_semester',
            'cas_admission',
            'cas_program',
            'cas_department',
            'cas_clslvl',
            'cas_reason',
            'cas_spec',
            //'created_at',
            //'created_by',
            //'updated_by',
            //'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
