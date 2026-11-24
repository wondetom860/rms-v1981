<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\DepartmentHeadSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Department Heads');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="department-head-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('decirs', 'Add New Department Head'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'dh_inst_relId',
                'value'=>function($model){
                    $model->cleanCorrect();
                    return $model->dhInstRel->getFullName();
                    // $id = \backend\models\Instructor::findOne(['id'=>$model->dh_inst_relId])->inst_user_relId;
                    // $inst = \backend\models\User::findOne(['id'=>$id]);
                    // return $inst->first_name. ' '.$inst->middle_name;
                },
            ],
            [
                'attribute'=>'dh_dept_relId',
                'value'=>'dhDeptRel.dept_name',
                'filter' => ArrayHelper::map(\backend\models\DepartmentHead::find()->groupBy('dh_dept_relId')->all(),'dh_dept_relId',function($model){ return $model->dhDeptRel->dept_name; })
            ],
            [
                'attribute' => 'dh_assig_date',
                'value' => function($model){
                    return date_format(date_create($model->dh_assig_date),'d M, Y');
                }
            ],
            [
                'attribute' => 'dh_terminated_date',
                'value' => function($model){
                    return is_null($model->dh_terminated_date )? $model->dh_terminated_date : date_format(date_create($model->dh_terminated_date),'d M, Y');
                }
            ],
            [
                'attribute'=>'dh_status',
                'value'=>function($model){
                    return $model->dh_status == 1?'ACTIVE':'TERMINATED';
                },
                'filter' => ArrayHelper::map(\backend\models\DepartmentHead::find()->groupBy('dh_status')->all(),'dh_status',function($model){
                    return $model->dh_status ? 'ACTIVE':'TERMINATED';
                }),
            ],
            [
                'attribute'=>'created_by',
                'value'=>'createdBy.username',
            ],
            // 'created_at',
            // 'updated_by',
            // 'updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
