<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Wsemester Evaluation Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wsemester-evaluation-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decris', 'Create Wsemester Evaluation Detail'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'sed_se_relId',
            'sed_ep_relId',
            'sed_point_given',
            'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
