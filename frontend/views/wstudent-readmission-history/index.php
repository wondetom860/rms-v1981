<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\WStudentReadmissionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Wstudent Readmission Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-readmission-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Wstudent Readmission History'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'srh_acadYear_relId',
            'srh_sem_relId',
            'srh_swh_relId',
            'srh_std_relId',
            // 'srh_return_back_on',
            // 'srh_HOD_approved',
            // 'srh_HOD_approved_at',
            // 'srh_HOD_approved_by',
            // 'srh_approved',
            // 'srh_approved_at',
            // 'srh_approved_by',
            // 'srh_return_back_letter',
            // 'remark_HOD:ntext',
            // 'remark_registrar:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
