<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentGroupHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Student Group Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-group-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Student Group History'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'sgh_stud_relId',
                'value'=>function($model){
                    $stu = \backend\models\Student::findOne(['id'=>$model->sgh_stud_relId]);
                    $user = \backend\models\User::findOne(['id'=>$stu->std_user_relId]);
                    return $user->first_name.' '.$user->last_name;
                }
            ],
            [
                'attribute'=>'sgh_acadYear_relId',
                'value'=>'sghAcadYearRel.sch_year_desc'
            ],
            [
                'attribute'=>'sgh_classlevel',
                'value'=>'sghClasslevel.clslvl_name',
            ],
            [
                'attribute'=>'sgh_semester_relId',
                'value'=>'sghSemesterRel.semester_name',
            ],
            [
                'attribute'=>'sgh_group_relId',
                'value'=>'sghGroupRel.group_name',
            ],
            [
                'attribute'=>'sgh_created_by',
                'value'=>'sghCreatedBy.username',
            ],
            // x
            // 'sgh_created_at',
            // 'sgh_updated_by',
            // 'sgh_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
