<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\ExitExam\models\EtSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Elligiblity Test: Setting Parameters');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exit Exam'), 'url' => ['/ExitExam/default/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="et-setting-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create New Setting'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>

    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'setting_header_info',
            'max_trails',
            'pass_mark',
            'active_status',
            //'locked',
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>