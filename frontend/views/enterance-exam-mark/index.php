<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\EnteranceExamMarkSeasrch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Enterance Exam Marks');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="enterance-exam-mark-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Enterance Exam Mark'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'eh_ei_relId',
            'eem_subject',
            'eem_reference_number',
            'eem_year',
            // 'eem_markObtained',
            // 'eem_created_at',
            // 'eem_created_by',
            // 'eem_updated_at',
            // 'eem_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
