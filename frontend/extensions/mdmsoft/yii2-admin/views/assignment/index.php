<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel mdm\admin\models\searchs\Assignment */
/* @var $usernameField string */
/* @var $extraColumns string[] */

$this->title = Yii::t('rbac-admin', 'Assignments');
$this->params['breadcrumbs'][] = $this->title;

$columns = [

];
if (!empty($extraColumns)) {
    $columns = array_merge($columns, $extraColumns);
}
$columns[] = [
    'class' => 'yii\grid\ActionColumn',
    'template' => '{view}'
];
?>
<div class="assignment-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            $usernameField,
            [
                'attribute' => 'fullname',
                'value' => function($model){
                    return $model->first_name." ".$model->middle_name;
                }
            ],[
                'attribute' => 'role',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getUserRoles();
                },
                'filter' => ArrayHelper::map(\common\models\User::getRoleTypes3(),'name','name'),
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}'
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>

</div>
