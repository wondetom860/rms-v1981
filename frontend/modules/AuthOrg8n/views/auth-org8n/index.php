<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel common\models\AuthOrg8nSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Authenticated Orgainzation Information');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="auth-org8n-index" style="font-size: 9pt;">

    <h3 class="bg-warning"><?= Html::encode($this->title) ?>&nbsp;<small style="font-style: italic;">This is configuration file and any change bieng made should have to be known.</small></h3>

    <p class="hidden">
        <?= Html::a(Yii::t('decris', 'Auth Org8n'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'name:ntext',
            'abrvn',
            // 'logo',
            'website',
            'email:email',
            'phone1',
            // 'phone2',
            'active_from',
            'active_to',
            // 'lic_entry',
            // 'updated_by',
            // 'updated_at',
            'created_at',
            // 'created_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
