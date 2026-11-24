<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WSemesterEvaluationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Wsemester Evaluations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wsemester-evaluation-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decris', 'Create Wsemester Evaluation'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'se_eet_relId',
            'se_reviewee_relId',
            'se_total_point_given',
            'se_date_started',
            // 'se_acadYear_relId',
            // 'se_sem_relId',
            // 'se_coff2det_relId',
            // 'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
