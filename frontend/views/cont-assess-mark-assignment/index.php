<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ContAssessMarkAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Cont Assess Mark Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-mark-assignment-index">

    <h1><?= Html::encode($this->title) ?></h1>
<?php // echo $this->render('_search', ['model' => $searchModel]);  ?>

    <p>
    <?= Html::a(Yii::t('decirs', 'Create Cont Assess Mark Assignment'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin(); ?>    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'pjax' => true,
        'export'=>false,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'ma_catagory_relId',
            [
                'class'=>'kartik\grid\EditableColumn',
                'header'=>'Istance Name',
                'attribute'=>'ma_instance',
            ],
            'ma_possiblePoint',
            'ma_created_at',
            // 'ma_created_by',
            // 'ma_updated_by',
            // 'ma_updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
<?php Pjax::end(); ?></div>
