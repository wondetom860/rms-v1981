<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
// use yii\bootstrap\Modal;
/** @var yii\web\View $this 
 * @var $searchModel backend\models\InstructorSearch 
 * @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Instructors');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="instructor-index table-responsive">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>

        <p class="pull-right">
            <?= Html::button(Yii::t('rms', 'Register Instructor Information'), ['value' => Yii::$app->urlManager->createUrl('user/create?role=Instructor'), 'class' => 'btn btn-success btn-xs', 'id' => 'modalBotton']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'header' => 'User Name',
                'attribute' => 'instUserRel.username',
                'value' => function ($model) {
                    if (is_null($model->instUserRel)) {
                        \backend\models\Instructor::findOne($model->id)->delete(); //incase partial info found, remove it.
                        return NULL;
                    }
                    return $model->instUserRel->username;
                },
            ],
            [
                'attribute' => 'inst_name',
                'value' => function ($model) {
                    return $model->instUserRel->getFullName();
                    // return $usr->getFullName;
                },
            ],
            [
                'header' => 'Department',
                'attribute' => 'inst_dep_relId',
                'value' => 'instDepRel.dept_name',
                'filter' => ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
            ],
            [
                'header' => 'Employment',
                'attribute' => 'employment_type_relId',
                'value' => 'employmentTypeRel.et_name',
                'filter' => ArrayHelper::map(\backend\models\EmploymentType::find()->all(), 'id', 'et_name'),
            ],
            [
                'header' => 'Qualification',
                'attribute' => 'inst_acq_relId',
                'value' => 'instAcqRel.acq_name',
                'filter' => ArrayHelper::map(\backend\models\AcademicQualification::find()->all(), 'id', 'acq_name'),
            ],
            [
                'header' => 'Academic Rank',
                'attribute' => 'inst_acr_relId',
                'value' => 'instAcrRel.acr_name',
                'filter' => ArrayHelper::map(\backend\models\AcademicRank::find()->all(), 'id', 'acr_name'),
            ],
            [
                'attribute' => 'inst_active',
                'value' => function ($model) {
                    return ($model->inst_active == 1) ? 'ACTIVE' : 'NOT ACTIVE';
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>


<?php

$this->registerJs(
    "

    $(function(){
        $('#modalBotton').click(function(){
            $('#modal').find('.modal-header').html('Register new user information');
            $('#modalContent').css({'text-align':'left','margin-top':'0px'});
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $('#modalContent').load($(this).attr('value'));
        });
    });
    ",
    $this::POS_READY
);

?>