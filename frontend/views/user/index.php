<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use mdm\admin\components\Helper;

/** @var yii\web\View $this */
/** @var  backend\models\UserSearch $searchModel*/
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = Yii::t('rms', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index table-responsive">

    <h4><?= Html::encode($this->title) ?></h4>
    <?php //echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <p class="pull-right">
        <?php if (!Yii::$app->user->can('UniversityRegistrar')): ?>
            <?= Html::a(Yii::t('rms', 'Login History'), ['#'], ['class' => 'btn btn-info btn-xs', 'onclick' => "showLoginSummary();return false;"]) ?>
            <?= Html::a(Yii::t('rms', 'Register New User'), ['create2'], ['class' => 'btn btn-success btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'View Students Account'), ['/student'], ['class' => 'btn btn-warning btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'View Instructors Account'), ['/instructor'], ['class' => 'btn btn-danger btn-xs']) ?>
        <?php endif ?>
    </p>
    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'username',
            'first_name',
            'middle_name',
            'last_name',
            'gender',
            // 'auth_key',
            'user_type',
            // 'phone_mobile',
            // 'password_hash',
            // 'password_reset_token',
            [
                'attribute' => 'role',
                'format' => 'raw',
                'value' => function ($model) {
                    // getUserRole($userId)
                    $role = Yii::$app->authManager->getRolesByUser($model->id);
                    return $role && count($role) > 0 ? "<i>" . json_encode(array_keys($role)) . "</i>" : 'Not Known';
                }
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    $st =  $model->status == $model::STATUS_ACTIVE ? 'ACTIVE' : ($model->status == $model::STATUS_DELETED ? 'Deleted' : ($model->status == $model::STATUS_UNAPPROVED ? 'Not Approved' : 'Uknown Status'));
                    return $model->activated == 1 ? "<span class='text-success'>{$st}</span>" : "<span class='text-danger'>{$st}</span>";
                },
                'filter' => ArrayHelper::map(\backend\models\User::find()->groupBy('status')->all(), 'status', function ($model) {
                    return $model->status == $model::STATUS_ACTIVE ? 'ACTIVE' : ($model->status == $model::STATUS_DELETED ? 'Deleted' : ($model->status == $model::STATUS_UNAPPROVED ? 'Not Approved' : 'Uknown Status'));
                })
            ],
            [
                'attribute' => 'nationality',
                'value' => function ($model) {
                    $nationality =  \backend\models\Nationality::findOne(['id' => $model->nationality]);
                    return $nationality ? $nationality->name : '';
                }
            ],

            [
                'attribute' => 'OTP',
                'format' => 'raw',
                'value' => function ($model) {
                    $bg = $model->getStudents() && $model->getStudents()
                        ->andFilterWhere(['std_semester_status' => "NOT APPROVED"])
                        ->count() > 0 ? "bg-warning" : "bg-default";
                    return "<span class='{$bg}'>{$model->OTP}</span>";
                }
            ],
            // 'photo_num',
            // 'created_at',
            // 'updated_at',
            // 'updated_by',
            // 'created_by',
            [
                'attribute' => 'Link',
                'format' => 'raw',
                'visible' => Yii::$app->user->can('Admin'),
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Link Account'),
                        [''],
                        [
                            'class' => 'btn btn-warning btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Link Account',
                            'value' => $model->id,
                            'onclick' => 'linkAccount(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => Helper::filterActionColumn(['view', 'activate', 'delete']),
                'buttons' => [
                    'activate' => function ($url, $model) {
                        if ($model->status == 2) {
                            return 'dd';
                        }
                        $options = [
                            'title' => Yii::t('rbac-admin', 'Activate'),
                            'aria-label' => Yii::t('rbac-admin', 'Activate'),
                            'data-confirm' => Yii::t('rbac-admin', 'Are you sure you want to activate this user?'),
                            'data-method' => 'post',
                            'data-pjax' => '0',
                        ];
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', $url, $options);
                    }
                ]
            ],
        ],
    ]); ?>

    <script type="text/javascript">
        window.linkAccount = (userid) => {
            window.location.href = '/rms-user-link/create?id=' + userid;
        }
        const showLoginSummary = () => {
            $('#modal').find('.modal-header').html('Loading...');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading form data...');
            $('#modal').find('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/user/show-login-summary', {}).done(function(data) {
                $('#modal').find('.modal-header').html('Users Login History');
                $('#modalContent').html(data);
            });
        }
    </script>
    <?php Pjax::end(); ?>
</div>