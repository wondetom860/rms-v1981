<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\Student1Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Student1s');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student1-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decirs', 'Create Student1'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'std_user_relId',
            'std_regId',
            'std_type',
            'std_dept_relId',
            // 'std_spec_relId',
            // 'std_clslvl_relId',
            // 'std_prog_relId',
            // 'std_schlrshpType_relId',
            // 'std_admission_relId',
            // 'std_acadYear_relId',
            // 'std_group_realId',
            // 'std_semester_status',
            // 'std_marital_status',
            // 'std_emergency_contact_relId',
            // 'st_educ_info_relId',
            // 'std_created_at',
            // 'std_created_by',
            // 'std_updated_at',
            // 'std_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
