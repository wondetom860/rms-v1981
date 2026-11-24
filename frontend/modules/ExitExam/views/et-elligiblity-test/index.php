<?php

use frontend\modules\ExitExam\models\EtElligiblityTest;
use frontend\modules\ExitExam\models\EtTestDetail;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\ExitExam\models\EtElligiblityTestSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$count = EtTestDetail::find()
    ->where(['grade_accepted' => 0])
    ->andWhere(' mark_obtained IS NOT NULL ')
    ->count();
$this->title = Yii::t('rms', 'Elligiblity Test Trails');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="et-elligiblity-test-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?php
            $activeSettings = \frontend\modules\ExitExam\models\EtSetting::find()->where(['active_status' => 1])->all();
            if (count($activeSettings) > 0 && Yii::$app->user->can('Student')) {
                $setting = $activeSettings[0];
                echo Html::a(Yii::t('rms', 'Register for Exit Exam'), ['#'], ['class' => 'btn btn-primary btn-xs', 'onclick' => "registerForExitExam({$setting->id});return false;"]);
            }
            if (Yii::$app->user->can('Registrar')) {
                echo Html::a(Yii::t('rms', 'Show Un-approved Requests')."({$count})", ['#'], ['class' => 'btn btn-info btn-xs', 'onclick' => 'showUnApprovedRequests();return false;']);
            }
            ?>
            <?php //Html::a(Yii::t('rms', 'Register for Exit Exam'), ['#'], ['class' => 'btn btn-primary btn-xs', 'onclick' => 'registerForExitExam();return false;']) 
            ?>
            <?php //Html::a(Yii::t('rms', 'Request Backlog Clear'), ['#'], ['class' => 'btn btn-warning btn-xs', 'onclick' => 'requestForBacklogClear();return false;']) 
            ?>
        </p>

    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'regId',
                'label' => 'Student ID',
                'value' => function ($model) {
                    $std = $model->semregRel->semregStdRel;
                    return $std->std_regId;
                },
            ],
            [
                'attribute' => 'full_name',
                'value' => function ($model) {
                    $std = $model->semregRel->semregStdRel;
                    return $std->getFullName();
                },
            ],
            [
                'attribute' => 'reason',
                'value' => function ($model) {
                    return $model->_reasons[$model->reason];
                },
                'filter' => ArrayHelper::map((new EtElligiblityTest())->getReasons(), 'id', 'name')
            ],
            [
                'attribute' => 'trails',
                'value' => function ($model) {
                    return $model->trails . "/3";
                },
                // 'filter' => $model->_reasons,
            ],
            [
                'attribute' => 'pass_status',
                'format' => 'raw',
                'value' => function ($model) {
                    $status =  $model->getPassStatus();
                    if (is_string($status)) {
                        return $status;
                    } else {
                        // var_dump($status);die();
                        $bg = ($model->pass_status ==1  && $status[1]) ? 'green' : 'red';//$status=> [pass_status,approval]
                        return "<span class='label label-default' style='background-color:{$bg};'>{$status[0]}</span>";
                    }
                },
                // 'filter' => $model->_pass_status,
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('d-M-Y', $model->created_at);
                },
            ],
            // [
            //     'attribute' => 'created_by',
            //     'value' => function ($model) {
            //         return $model->createdBy->username;
            //     },
            // ],
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<script>
    window.showUnApprovedRequests = function() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['et-elligiblity-test/unapproved-requests']) ?>',
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                $('#modal').find('.modal-header').html('Un-approved Requests');
                $('#modalContent').css({
                    'text-align': 'left',
                    'margin-top': '0px'
                });
                $('#modalContent').html('Loading form...');
                $('#modelContent').addClass('table-responsive');
                $('#modalContent').css('overflow', 'auto');
                $('#modalContent').css('height', '500px');
                $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
                $('#modal').modal('show');
                $('#modalContent').html(data);
            }
        });
    }
    window.registerForExitExam = function(setting_id) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['et-elligiblity-test/create']) ?>',
            type: 'POST',
            data: {
                'EtElligiblityTest[reason]': 1,
                'setting_id': setting_id
            },
            dataType: 'html',
            success: function(data) {
                $('#modal').modal('show')
                    .find('.modal-content')
                    .html(data);
            }
        });
    }
    window.requestForBacklogClear = function() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['et-elligiblity-test/create']) ?>',
            type: 'POST',
            dataType: 'html',
            data: {
                'EtElligiblityTest[reason]': 2
            },
            success: function(data) {
                $('#modal').modal('show')
                    .find('.modal-content')
                    .html(data);
            }
        });
    }
    $(document).on('click', '.modal-footer .btn-primary', function() {
        var form = $(this).closest('.modal-content').find('form');
        $.ajax({
            url: form.attr('action'),
            type: 'POST',
            data: form.serialize(),
            success: function(data) {
                if (data.status == 'success') {
                    $('#modal').modal('hide');
                    $.pjax.reload({
                        container: '#w0'
                    });
                } else {
                    $('#modal').find('.modal-content').html(data);
                }
            }
        });
    });
</script>