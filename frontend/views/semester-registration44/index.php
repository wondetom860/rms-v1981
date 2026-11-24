<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistrationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registrations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Semester Registration'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'sr_student_relId',
                'value'=>'srStudentRel.firstname',
            ],
            [
                'attribute'=>'sr_co_relId',
                'value'=>'srCoRel.course_title',
            ],
            [
                'attribute'=>'Semester',
                'value'=> 'I',
            ],
            'sr_ma_relId',
            'sr_markObtained',
            // 'sr_setBy',
            // 'sr_setAt',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
