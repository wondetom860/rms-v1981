<?php

use yii\helpers\Html;
use yii\grid\GridView;
use backend\models\Student;
use common\models\User;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\AttendancedetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Presently availabe students list';
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendancedetail-index">

    <h1><?php //echo Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Create Attendancedetail', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
          //  'ad_attendance_relId',
           // 'ad_student_relId',
            [
            'attribute'=>'ad_student_relId',
            'label'=>'Student Name',
            'value'=>function($model){
                $Name=User::find()->innerjoin('student','user.id=student.std_user_relId')->where(['student.id'=>$model->ad_student_relId])->one();
                return $Name->first_name.' '.$Name->middle_name.' '.$Name->last_name;
            }
            ],
            //'ad_status',
            //'ad_remark:ntext',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
