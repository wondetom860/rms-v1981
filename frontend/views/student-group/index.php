<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\User;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Student Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-group-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Student Group'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'group_name',
            [
                'attribute'=>'group_created_by',
                'value'=>function($model){
                    $usr = User::findOne(['id'=>$model->group_created_by]);
                    return $usr->username;
                },
            ],
            'group_created_at',
            // 'group_updated_by',
            // 'group_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
