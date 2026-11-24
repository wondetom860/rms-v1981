<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\CourseAssessment\models\InstanceMarkAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Instance Mark Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instance-mark-assignment-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Instance Mark Assignment'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'ima_catagorySetup_relId',
            'ima_possiblePoint',
            'ima_instanceName',
            'ima_created_by',
            //'ima_created_at',
            //'ima_updated_by',
            //'ima_updated_at',
            //'ima_approved',
            //'ima_approved_at',
            //'ima_approved_by',
            //'ima_assessed',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
