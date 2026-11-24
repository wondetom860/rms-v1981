<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use backend\models\User;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Students');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <!-- <p>
        <?= Html::a(Yii::t('decirs', 'Create Student'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->

    <p>
            <?= Html::button(Yii::t('decirs', 'Register new student'),['value'=>Yii::$app->urlManager->createUrl('user/create?role=student'),'class' => 'btn btn-success','id'=>'modalBotton']) ?>
    </p>

     <?php

     Modal::begin([
        'header'=>'<h4>Create new user account</h4>',
        'id'=>'modal',
        'size'=>'modal-lg',
     ]);
     echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
     Modal::end();

     ?>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'std_regId',
            [
                'attribute'=>'std_user_relId',
                'value'=>function($model){
                    $usr = User::findOne(['id'=>$model->std_user_relId]);
                    return $usr->first_name . ' '. $usr->middle_name;
                },
            ],
            [
                'attribute'=>'std_dept_relId',
                'value'=>'stdDeptRel.dept_name',
            ],
            [
                'attribute'=>'std_admission_relId',
                'value'=>'stdAdmissionRel.admission_name',
            ],
            [
                'attribute'=>'std_prog_relId',
                'value'=>'stdProgRel.program_name',
            ],
            'std_type',
            // 'std_spec_relId',
            // 'std_clslvl_relId',
            // 'std_acadYear_relId',
            // 'std_group_realId',
            // 'std_semester_status',
            // 'std_created_at',
            // 'std_created_by',
            // 'std_updated_at',
            // 'std_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
