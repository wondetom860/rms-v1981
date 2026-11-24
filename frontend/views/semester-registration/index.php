<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Semester Registration'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?> <?= GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],

               'id',
               'sr_academicYear_relId',
               'sr_semester_relId',
               'sr_student_relId',
               'sr_courses',
               // 'sr_status',
               // 'sr_setOn',
       
               ['class' => 'yii\grid\ActionColumn'],
           ],
       ]); ?>
    <?php Pjax::end(); ?>
</div>