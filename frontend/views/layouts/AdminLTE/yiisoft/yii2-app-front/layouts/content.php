<?php
use yii\widgets\Breadcrumbs;
use dmstr\widgets\Alert;
use kartik\popover\PopoverX;
use common\components\CommonControllerFunctions as CCF;
use yii\helpers\Url;

?>
<div class="content-wrapper">
    <section class="content-header" style="margin-bottom: 1px;">
        <div class="row">
            <div class="col-md-4">
                <div class="panel-default panel-heading">
                    <b> <span style="font-size: 13px;">
                        <?php

                            $ac = \Yii::$app->AcademicCalendar->getActivePrograms(1);
                            // var_dump($ac);die();

                            if (is_array($ac)) {
                                echo "<p class='bg-".$ac['type']."'>
                                    Academic Calendar : <u><i>{$ac['message']}</i></u>
                                </p>";
                            }else{
                                echo "{$ac}";
                            }
                        ?>
                    </span></b>
                </div> 
            </div>
            <div class="col-md-8">
                <?=
                    Breadcrumbs::widget(
                        [
                            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                        ]
                    ) ?>
            </div>
        </div>
    </section>

    <section class="content" style="margin-top: 1px;">
        <?= Alert::widget() ?>
        <?= $this->render('session_message') ?>
        <?= $content ?>
    </section>
</div>

<footer class="main-footer">

    <?php
        PopoverX::begin([

        ]);
        PopoverX::end();
    ?>
    <div class="pull-right hidden-xs">
        <div class="container-fluid" style="text-align: center; font-size: 8pt;">
            <img src="/uploads/photo/devs/img1.jpg" alt="image" id="ppww" class="img img-circle profilePopover" style="cursor: pointer;" width="30px" height="35px"><strong>Wondemu Toma </strong>
            <div id="profW" class="container-fluid" style="display: none; font-size: 9pt;">
                <img src="/uploads/photo/devs/img1.jpg" alt="image" id="ppww" class="img img-circle profilePopover" style="cursor: pointer;" width="30px" height="35px">
                <strong>Wondemu Toma </strong><hr>
                <strong>Email : </strong><i>wondetom@gmail.com</i><hr>
                <strong>Phone : </strong><i>+251 900 545 345</i><hr>
                <div id="profW" class="container-fluid" style="display: none; font-size: 9pt;">
                    
                </div>
            </div>
        </div>
        <div class="text-warning pull-right">
            <b title="Updated on <?= isset(signature['last_update_date']) ? signature['last_update_date'] : date('r') ?>">V -</b> <?= isset(signature['version']) ? signature['version'] : '1.1' ?>
            <span style="font-size: 7pt"><?= Yii::$app->session->hasFlash('_ALTEDATE') ? "  [ <b>ALTE-DATE : </b>".Yii::$app->session->getFlash('ALTEDATE').' ]  ' : '' ?></span>
        </div>
    </div>
    <div class="pull-right hidden-xs">
    </div>

    <strong>Copyright &copy; <?= date('Y') ?> <a target="_blank" href="<?= Url::to(Yii::$app->params['client_url']) ?>"><?= Yii::$app->params['client'] ?></a>.</strong> All rights reserved.
</footer>

<?php

$this->registerJs(
    "
    $('#ppww').popover({
        html:true,
        placement:'top',
        animate:true,
        continer:'body',
        content:$('#profW').html(),
        trigger:'hover',
    });

    $('#pphh').popover({
        html:true,
        placement:'top',
        continer:'body',
        animate:true,
        content:$('#profH').html(),
        trigger:'hover',
    });
    ",
    $this::POS_READY)

?>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Create the tabs -->
    <ul class="nav nav-tabs nav-justified control-sidebar-tabs">
        <li style="display: none;"><a href="#control-sidebar-home-tab" data-toggle="tab"><i class="fa fa-home"></i></a></li>
        <li style="display: none;"><a href="#control-sidebar-settings-tab" data-toggle="tab"><i class="fa fa-gears"></i></a></li>
        <li><a href="?language-picker-language=am" style="color:#fff;"><i class="fa fa-flag-checkered"></i> አማ</a></li>
        <li><a href="?language-picker-language=en-US" style="color:#fff;"> <i class="fa fa-flag" ></i> EN</a></li>
    </ul>
    <!-- Tab panes -->
    <div class="tab-content">
        <!-- Home tab content -->
        <div class="tab-pane" id="control-sidebar-home-tab">
            <h3 class="control-sidebar-heading">Recent Activity</h3>
            <ul class='control-sidebar-menu'>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-birthday-cake bg-red"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Langdon's Birthday</h4>

                            <p>Will be 23 on April 24th</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-user bg-yellow"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Frodo Updated His Profile</h4>

                            <p>New phone +1(800)555-1234</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-envelope-o bg-light-blue"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Nora Joined Mailing List</h4>

                            <p>nora@example.com</p>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <i class="menu-icon fa fa-file-code-o bg-green"></i>

                        <div class="menu-info">
                            <h4 class="control-sidebar-subheading">Cron Job 254 Executed</h4>

                            <p>Execution time 5 seconds</p>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

            <h3 class="control-sidebar-heading">Tasks Progress</h3>
            <ul class='control-sidebar-menu'>
                <li>
                    <a href='javascript::;'>
                        <h4 class="control-sidebar-subheading">
                            Custom Template Design
                            <span class="label label-danger pull-right">70%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-danger" style="width: 70%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <h4 class="control-sidebar-subheading">
                            Update Resume
                            <span class="label label-success pull-right">95%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-success" style="width: 95%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <h4 class="control-sidebar-subheading">
                            Laravel Integration
                            <span class="label label-waring pull-right">50%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-warning" style="width: 50%"></div>
                        </div>
                    </a>
                </li>
                <li>
                    <a href='javascript::;'>
                        <h4 class="control-sidebar-subheading">
                            Back End Framework
                            <span class="label label-primary pull-right">68%</span>
                        </h4>

                        <div class="progress progress-xxs">
                            <div class="progress-bar progress-bar-primary" style="width: 68%"></div>
                        </div>
                    </a>
                </li>
            </ul>
            <!-- /.control-sidebar-menu -->

        </div>
        <!-- /.tab-pane -->

        <!-- Settings tab content -->
        <div class="tab-pane" id="control-sidebar-settings-tab">
            <form method="post">
                <h3 class="control-sidebar-heading">General Settings</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Report panel usage
                        <input type="checkbox" class="pull-right" checked/>
                    </label>

                    <p>
                        Some information about this general settings option
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Allow mail redirect
                        <input type="checkbox" class="pull-right" checked/>
                    </label>

                    <p>
                        Other sets of options are available
                    </p>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Expose author name in posts
                        <input type="checkbox" class="pull-right" checked/>
                    </label>

                    <p>
                        Allow the user to show his name in blog posts
                    </p>
                </div>
                <!-- /.form-group -->

                <h3 class="control-sidebar-heading">Chat Settings</h3>

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Show me as online
                        <input type="checkbox" class="pull-right" checked/>
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Turn off notifications
                        <input type="checkbox" class="pull-right"/>
                    </label>
                </div>
                <!-- /.form-group -->

                <div class="form-group">
                    <label class="control-sidebar-subheading">
                        Delete chat history
                        <a href="javascript::;" class="text-red pull-right"><i class="fa fa-trash-o"></i></a>
                    </label>
                </div>
                <!-- /.form-group -->
            </form>
        </div>
        <!-- /.tab-pane -->
    </div>
</aside><!-- /.control-sidebar -->
<!-- Add the sidebar's background. This div must be placed
     immediately after the control sidebar -->
<div class='control-sidebar-bg'></div>

<script type="text/javascript">
    window.applicationForm = function(id){
        // window.open('/admit-student/self-admission-form?formId='+id+'&validFor='+id);
        // return false;
        $('#guestHomePage').html('Loading form....');
        $.post('/admit-student/self-admission-form',{id:id}).done(function(data){
            $('#guestHomePage').html(data);
            $('#user1-first_name').focus();
        });
    }

    window.checkAccountStatus = function(){
        $('#modal').find('.modal-header').html('Signin to your account');
        $('#modalContent').css('text-align','left');
        $('#modal').find('.modal-dialog').addClass('modal-md').removeClass('modal-lg');
        $('#modal').modal('show');
        $('#modalContent').load('/user/login');
        $("#loginform-username").focus();
        // $.post('/user/get-account-status',{}).done(function(data){
        //     $('#modalContent').html(data);
        // });
    }

    // window.loadAdmissionForm = () => {
        
    //     applicationForm1()
    //     console.log(otp);
    // }

    window.applicationForm1 = function(id,settingId){
        otp = prompt('Please Enter OTP: ');
        $('#guestHomePage').html('Loading form....');
        $.post('/admit-student/self-admission-form',{id:id,v2:1,settingId : settingId,otp:otp}).done(function(data){
            $('#guestHomePage').html(data);
            $('#user1-first_name').focus();
        });
    }
</script>