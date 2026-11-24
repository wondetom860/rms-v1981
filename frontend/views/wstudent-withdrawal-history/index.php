<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\WStudentWithdrawalHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Wstudent Withdrawal Histories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-withdrawal-history-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Wstudent Withdrawal History'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'swh_acadYear_relId',
            'swh_sem_relId',
            'swh_std_relId',
            'swh_reason_relId',
            // 'swh_course_removed',
            // 'swh_approved',
            // 'swh_approved_by',
            // 'swh_approved_at',
            // 'swh_created_at',
            // 'swh_created_by',
            // 'swh_updated_at',
            // 'swh_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
