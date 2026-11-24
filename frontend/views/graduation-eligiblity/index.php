<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\GraduationEligiblitySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Graduation Eligiblity Control');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="graduation-eligiblity-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Yii::$app->user->can('Admin') ? Html::a(Yii::t('rms', 'Create Graduation Eligiblity'), ['create'], ['class' => 'btn btn-success']):'' ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'ge_adm_relId',
                'value'=>'geAdmRel.admission_name',
            ],
            [
                'attribute'=>'ge_prog_relId',
                'value'=>'geProgRel.program_name',
            ],
            
            'ge_num_of_F',
            'ge_min_crdthr',
            [
                'attribute'=>'ge_min_CGPA',
                'value'=>function($model){
                    return sprintf("%0.2f",$model->ge_min_CGPA);
                }
            ],
            [
                'attribute'=>'ge_min_CGPA_major',
                'value'=>function($model){

                    return $model->ge_min_CGPA_major == 0?' - ':sprintf("%0.2f",$model->ge_min_CGPA_major);
                }
            ],
            // 'ge_created_at',
            [
                'attribute'=>'ge_created_by',
                'value'=>'geCreatedBy.first_name',
            ],
            // 'ge_updated_at',
            // 'ge_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
