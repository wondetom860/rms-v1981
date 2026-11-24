<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\AttendancedetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Attendancedetails';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendancedetail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Create Attendancedetail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ad_attendance_relId',
            'ad_student_relId',
            'ad_status',
            'ad_remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
