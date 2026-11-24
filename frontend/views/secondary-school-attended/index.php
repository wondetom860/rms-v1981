<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SecondarySchoolAttendedSeasrch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Secondary School Attendeds');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="secondary-school-attended-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Secondary School Attended'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ssa_ei_relId',
            'ssa_school_name:ntext',
            'ssa_town:ntext',
            'ssa_year',
            // 'ssa_gradeLevels:ntext',
            // 'ssa_created_by',
            // 'ssa_created_at',
            // 'ssa_updated_by',
            // 'ssa_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
