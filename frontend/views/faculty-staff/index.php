<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\FacultyStaffSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Faculty Staff');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faculty-staff-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Faculty Staff'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'fas_faculty_relId',
                'value'=>function($model){
                    return $model->fasFacultyRel->fac_name;
                }
            ],
            'fas_roleName',
            [
                'attribute'=>'fas_user_relId',
                'value'=>function($model){
                    return $model->fasUserRel->first_name .' '. $model->fasUserRel->middle_name;
                }
            ],
            [
                'attribute'=>'fas_assigned_at',
                'value'=>function($model){
                    return date_format(date_create($model->fas_assigned_at),'D m , Y');
                }
            ],
            // 'fas_assigned_by',
            [
                'attribute'=>'fas_status',
                'value'=>function($model){
                    return $model->fas_status == 1?'ACTIVE' : 'NOT ACTIVE';
                }
            ],
            // 'fas_updated_at',
            // 'fas_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
