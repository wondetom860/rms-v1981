<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ProjectGroupSetupNameSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Project Course Setups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-course-setup-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Project Course Setup'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pcs_acadYear_relId',
            'pcs_sem_relId',
            'pcs_gset_relId',
            'pcs_course_relId',
            // 'pcs_pgName_relId',
            // 'pcs_advisor',
            // 'pcs_group_leader',
            // 'pcs_title:ntext',
            // 'pcs_approved',
            // 'pcs_approved_at',
            // 'pcs_approved_by',
            // 'pcs_created_at',
            // 'pcs_created_by',
            // 'pcs_updated_at',
            // 'pcs_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
