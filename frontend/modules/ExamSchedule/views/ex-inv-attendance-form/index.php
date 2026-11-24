<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\ExamSchedule\models\ExInvAttendanceFormSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Ex Inv Attendance Forms');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ex-inv-attendance-form-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Ex Inv Attendance Form'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'date_time_slot_relId:datetime',
            'exam_slot_relId',
            'inviglator_relId',
            'assigned_as:ntext',
            //'status',
            //'approved',
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
