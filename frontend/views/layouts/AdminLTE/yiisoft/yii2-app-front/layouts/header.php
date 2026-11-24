<?php
use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this \yii\web\View */
/* @var $content string */

$mm = !(Yii::$app->user->isGuest && (Yii::$app->controller->action->id === 'dashboard' || Yii::$app->controller->action->id === 'index' || Yii::$app->controller->action->id === 'create2'));
?>

<header class="main-header">

    <?= Html::a('<span class="logo-mini">RMS</span><span class="logo-lg">' . Yii::$app->name . ' - '. Yii::$app->params['college_accr'] .' </span>', Yii::$app->homeUrl, ['class' => 'logo']) ?>

    <nav class="navbar navbar-static-top" role="navigation">
        <?php if ($mm) { ?>
        <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
            <span class="sr-only">Toggle navigation</span>
        </a>

        <?php } ?>
        <?= "<div class='navbar-text hidden-xs' style='text-align: center; padding: 0px; color:white; text-align:center'>
                <span style='font-size: 9pt; font-weight: bold;'>
                        ". strtoupper(Yii::$app->params['client']) ." , ". strtoupper(Yii::$app->params['college']) ." - ". Yii::$app->params['project'] ."
                    </span>
           </div>" ?>

        <div class="navbar-custom-menu">

            <?php

                if (Yii::$app->user->isGuest) {
                    echo "<ul class='nav navbar-nav'>";
                    echo "<li style='background-color:#6bb; display:none'>
                            <a href='#' onclick='loadSignUpForm();return false;'>Signup</a>
                        </li><li style='background-color:#9ab;'>
                            <a href='#' title='Login' onclick='checkAccountStatus();return false;'><i class='fa fa-user'></i> Login</a>
                        </li>";
                    echo "</ul>";
                }else{
                    $userModels = \common\models\User::findOne(Yii::$app->user->identity->id);
                ?>
                <ul class="nav navbar-nav">

                    <!-- Messages: style can be found in dropdown.less-->
                    <li class="dropdown messages-menu" style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-envelope-o"></i>
                            <span class="label label-success">4</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 4 messages</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- start message -->
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?= $directoryAsset ?>/img/user2-160x160.jpg" class="img-circle"
                                                     alt="User Image"/>
                                            </div>
                                            <h4>
                                                Support Team
                                                <small><i class="fa fa-clock-o"></i> 5 mins</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <!-- end message -->
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                                                     alt="user image"/>
                                            </div>
                                            <h4>
                                                AdminLTE Design Team
                                                <small><i class="fa fa-clock-o"></i> 2 hours</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                                                     alt="user image"/>
                                            </div>
                                            <h4>
                                                Developers
                                                <small><i class="fa fa-clock-o"></i> Today</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?= $directoryAsset ?>/img/user3-128x128.jpg" class="img-circle"
                                                     alt="user image"/>
                                            </div>
                                            <h4>
                                                Sales Department
                                                <small><i class="fa fa-clock-o"></i> Yesterday</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="#">
                                            <div class="pull-left">
                                                <img src="<?= $directoryAsset ?>/img/user4-128x128.jpg" class="img-circle"
                                                     alt="user image"/>
                                            </div>
                                            <h4>
                                                Reviewers
                                                <small><i class="fa fa-clock-o"></i> 2 days</small>
                                            </h4>
                                            <p>Why not buy a new awesome theme?</p>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="footer"><a href="#">See All Messages</a></li>
                        </ul>
                    </li>
                    <li class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-bell-o"></i>
                            <span class="label label-warning" id="notifications_count"></span>
                        </a>
                        <ul class="dropdown-menu" id="notifications_body">
                            
                        </ul>
                    </li>
                    <!-- Tasks: style can be found in dropdown.less -->
                    <li class="dropdown tasks-menu"  style="display: none;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-flag-o"></i>
                            <span class="label label-danger">9</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header">You have 9 tasks</li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Design some buttons
                                                <small class="pull-right">20%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-aqua" style="width: 20%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">20% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Create a nice theme
                                                <small class="pull-right">40%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-green" style="width: 40%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">40% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Some task I need to do
                                                <small class="pull-right">60%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-red" style="width: 60%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">60% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                    <li><!-- Task item -->
                                        <a href="#">
                                            <h3>
                                                Make beautiful transitions
                                                <small class="pull-right">80%</small>
                                            </h3>
                                            <div class="progress xs">
                                                <div class="progress-bar progress-bar-yellow" style="width: 80%"
                                                     role="progressbar" aria-valuenow="20" aria-valuemin="0"
                                                     aria-valuemax="100">
                                                    <span class="sr-only">80% Complete</span>
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <!-- end task item -->
                                </ul>
                            </li>
                            <li class="footer">
                                <a href="#">View all tasks</a>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->

                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="/uploads/<?= $userModels->photo_num ?>" class="user-image" alt="User Image"/>
                            <span class="hidden-xs"><?= Yii::$app->user->identity->first_name ?></span>
                        </a>
                        <ul class="dropdown-menu" style="background-color: #90DF">
                            <!-- User image -->
                            <li class="user-header">
                                <img src="/uploads/<?= $userModels->photo_num ?>" class="img-circle"
                                     alt="User Image"/>

                                <p class="bg-info">
                                    <?= Yii::$app->user->identity->username ?>
                                </p>
                                <span style="font-size: 8pt; font-style: italic; color: darkred;">Last Login: <?= $userModels->_last_login? date("d-M-y @h:i A",$userModels->_last_login) : '-' ?></span>
                            </li>
                            <!-- Menu Body -->
                            <li class="user-body" style="display: none;">
                                <div class="col-xs-6 text-center">
                                    <a href="#">Followers</a>
                                </div>
                                <div class="col-xs-6 text-center">
                                    <a href="#">Sales</a>
                                </div>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-right">
                                    <?= Html::a(
                                        'Sign out',
                                        ['/site/logout'],
                                        ['data-method' => 'post', 'class' => 'btn btn-default btn-flat']
                                    ) ?>
                                </div>
                            </li>
                        </ul>
                    </li>

                    <!-- User Account: style can be found in dropdown.less -->
                    <li>
                        <a href="#" data-toggle="control-sidebar"><i class="fa fa-gears"></i></a>
                    </li>
                </ul>

                <?php
                }

            ?>
        </div>
    </nav>
</header>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading form...</div>";
    Modal::end();
?>

<script type="text/javascript">
    window.loadSignUpForm = () => {
        $('#modal').modal('show');
        $('#modal').find('.modal-header').html('Create account');
        $('#modalContent').load('/user/signup');
        $('#modalContent').css('text-align','left');
        $('#modal').find('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
    }

    window.loadLoginModal = function(){
        $('#modalGeneralHeader').parent().css('background-color','#8D0');
        $('#modalGeneral').modal('show');
        $('#btn_check').hide();
        $('#modal').find('.modal-header').html('Signin to your account');
        $('#modalContent').load('/user/login');
        $('#modalContent').css('text-align','left');
        $('#modal').find('.modal-dialog').removeClass('modal-lg').addClass('modal-md');
    }
    window.loadAccountStatus = function(){
        form = $('#accountStatusForm');
        $.ajax({
            type:'POST',
            url:'/user/get-account-status',
            data: form.serialize(),
            success:function(data){
                $('#accountStatus').html(data);
            },
            error:function(data){
                $('#accountStatus').html(data);
            },
        });
    }
</script>