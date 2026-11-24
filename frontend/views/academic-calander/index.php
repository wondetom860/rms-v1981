<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use DateTime;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\AcademicCalanderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Academic Calanders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="academic-calander-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'New Academic Calander'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <div class="container-fluid table-responsive" style="font-size: 9pt;">
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'acad_acadYear_relId',
                'value'=>'acadAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_acadYear_relId')->all(),'acad_acadYear_relId',function($model){ return $model->acadAcadYearRel->sch_year_desc; })
            ],
            [
                'attribute'=>'acad_sem_relId',
                'value'=>function($model){
                    return  $model->acad_sem_relId ? $model->acadSemRel->semester_name : null;
                } ,
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_sem_relId')->all(),'acad_sem_relId',function($model){ return $model->acad_sem_relId ? $model->acadSemRel->semester_name : null; })
            ],
            [
                'attribute'=>'acad_action_relId',
                'value'=>'acadActionRel.aca_name',
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->groupBy('acad_action_relId')->all(),'acad_action_relId',function($model){ $model->acadActionRel->aca_name; })
            ],
            [
                'attribute'=>'acad_adm_relId',
                'value'=>function($model){
                    return $model->acad_adm_relId ? $model->acadAdmRel->admission_name : 'For All Adm.';
                },
            ],
            [
                'attribute'=>'acad_prog_relId',
                'value'=> function($model){
                    return $model->acad_prog_relId ? $model->acadProgRel->program_name : 'For All Prog.';
                } ,
            ],
            [
                'attribute'=>'acad_clslvl_relId',
                'format'=>'raw',
                'value'=>function($model){
                    return $model->acad_clslvl_relId ? $model->acadClslvlRel->getClslvlDisp() : 'For all class Level';
                },
            ],
            

            [
                'attribute'=>'acad_starttime',
                'value'=>function($model){
                    return date_format(date_create($model->acad_starttime),'M d, Y @ h:i A');
                } ,
            ],

            [
                'attribute'=>'acad_finishtime',
                'value'=>function($model){
                    // $stop_date = new DateTime(date_create($model->acad_finishtime)); 
                    // $stop_date->modify('-1 day');
                    return date_format(date_create($model->acad_finishtime),'M d, Y @ h:i A');
                } ,
            ],
            
            [
                'attribute'=>'acad_status',
                'value'=>function($model){
                    return $model->getStatus()[1];
                },
                'filter' => ArrayHelper::map(\backend\models\AcademicCalander::find()->all(),'acad_status',function($model){ return $model->getStatus(1); })
            ],
            [
                'attribute'=>'acad_created_by',
                'value'=>'acadCreatedBy.username',
            ],
            // 'acad_created_at',
            // 'acad_updated_at',
            // 'acad_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div></div>
