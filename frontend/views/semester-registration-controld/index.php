<?php

use backend\models\Admission;
use backend\models\ClassLevel;
use backend\models\Program;
use backend\models\Semester;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterRegistrationControldSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Loaad (Defaults)');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-controld-index">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'New Semester Load (default)'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>
    <?php Pjax::begin(); ?> <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
           // 'src_adm_relId',
           // 'src_prog_relId',
           // 'src_sem_relId',
           // 'src_clslvl_relId',
            [
                'attribute'=>'src_adm_relId',
                'value'=>'srcAdmRel.admission_name',
                'filter' => ArrayHelper::map(Admission::find()->all(), 'id', 'admission_name')

            ],
            [
                'attribute'=>'src_prog_relId',
                'value'=>'srcProgRel.program_name',
                'filter' => ArrayHelper::map(Program::find()->all(), 'id', 'program_name')

            ],
            [
                'attribute'=>'src_sem_relId',
                'value'=>function($model){
                    return $model->srcSemRel? $model->srcSemRel->semester_name : 'For All';
                },
                'filter' => ArrayHelper::map(Semester::find()->all(), 'id', 'semester_name')
            ],
            [
                'attribute'=>'src_clslvl_relId',
                'format'=>'raw',
                'value'=> function($model){
                    return $model->srcClslvlRel ? $model->srcClslvlRel->getClslvlDisp() : 'For all';
                } ,
            ],
             'src_max_crdthr',
             'src_min_crdthr',
            // 'src_remark:ntext',
            // 'src_created_by',
            // 'src_created_at',
            // 'src_updated_at',
            // 'src_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>