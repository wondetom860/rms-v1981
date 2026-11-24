<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\HonorListControlSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Honor List Controls');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="honor-list-control-index">

    <h3><?= Html::encode($this->title) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Register New Honor List Control'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    </h3>
    <?php Pjax::begin(); ?> <?= GridView::widget([
           'dataProvider' => $dataProvider,
           'filterModel' => $searchModel,
           'columns' => [
               ['class' => 'yii\grid\SerialColumn'],
               [
                   'attribute' => 'hl_acadYear_relId',
                   'value' => function ($model) {
                              return $model->hlAcadYearRel->sch_year_desc;
                          }
               ],
               [
                   'attribute' => 'hl_sem_relId',
                   'value' => function ($model) {
                              return $model->hlSemRel->semester_name;
                          }
               ],
               'hl_maxSGPA',
               'hl_minSGPA',
               //'hl_created_by',
               [
                   'attribute' => 'hl_created_by',
                   'value' => function ($model) {
                              return $model->hlCreatedBy->username;
                          }
               ],
               'hl_created_at',
               // 'hl_updated_at',
               // 'hl_updated_by',
       
               ['class' => 'yii\grid\ActionColumn'],
           ],
       ]); ?>
    <?php Pjax::end(); ?>
</div>