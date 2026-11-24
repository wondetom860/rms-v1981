<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PostSecondaryEducationSeasrch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Post Secondary Educations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="post-secondary-education-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Post Secondary Education'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'pse_ei_relId',
            'pse_institute_name:ntext',
            'pse_nationality_relId',
            'pse_from_year',
            // 'pse_to_year',
            // 'pse_credit_earned',
            // 'pse_CGPA_obtained',
            // 'pse_certificate_earned:ntext',
            // 'pse_created_by',
            // 'pse_updated_by',
            // 'pse_created_at',
            // 'pse_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
