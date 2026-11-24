<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration2 Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Semester Registration2 Detail'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'srd2_semreg_relId',
            'srd2_coff_relId',
            'srd2_course_relId',
            'srd2_grade_relId',
            // 'srd2_coadd_flag',
            // 'srd2_corptd_flag',
            // 'srd2_codrpd_relId',
            // 'srd2_corProject',
            // 'srd2_enrollApproved',
            // 'srd2_enrollApproved_by',
            // 'srd2_enrolApprovrd_at',
            // 'srd2_group_relId',
            // 'srd2_updated_by',
            // 'srd2_updated_at',
            // 'srd2_created_at',
            // 'srd2_created_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
