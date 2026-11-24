<?php
/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
use meysampg\intldate\IntlDateTrait;
use kartik\popover\PopoverX;
use yii\bootstrap\Modal;
use \common\components\CommonControllerFunctions as CCF;

$this->title = Yii::$app->name . ' | Home Page';

$this->params['breadcrumbs'][] = $this->title;
?>
<?php
// PopoverX::begin([
//     'header' => 'Hello world',
//     'footer' => Html::button('View', ['class'=>'btn btn-primary']),
//     'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
//     'toggleButton' => ['class'=>'btn btn-primary btn-circle pull-right', 'style'=>'border-radius: 50%;'],
// ]);

// echo '<p class="text-justify">' .
//     'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.' . 
//     '</p>';

// PopoverX::end();
?>
<div class="site-dashboard">
    <div class="row" id="guestHomePage">
        <div class="col-md-8">
            <div class="alert alert-success text-justify">
                <?php $ethiopian_date = new Andegna\DateTime(); ?>
                <p>System Date :
                    <?= date("l, F d, Y") . ' [ ' . $ethiopian_date->format(Andegna\Constants::DATE_ETHIOPIAN_WONDE) . '] ' ?>
                </p>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <span style="scroll-behavior: smooth;">
                        <?= Yii::$app->params['client'] . ' ' . Yii::$app->params['college'] . ' | ' . Yii::$app->params['project'] ?>
                    </span>
                </div>
                <div class="panel-body"  style="min-height: 110px; overflow: auto;">
                    <p>News Page - Dashboard</p>
                    <?php
                    // date_format(,'D M, Y')
                    // echo "Date: ". date('D M, Y', 1581430884);
                    ?>
                    <!-- <button class="btn btn-info btn-md pull-left" onclick="carousel(-1)" style="position: absolute; left: 30px; bottom:20px; opacity: 0.6;">
                        &#10094;
                    </button>
                    <button class="btn btn-info btn-md pull-right" onclick="carousel(+1)" style="position: absolute; right: 30px; bottom:20px; opacity: 0.6;">
                        &#10095;
                    </button>
                   <img class="imgm" src="/uploads/recent_news_img/1.JPG" width="100%" height="100%" alt="DEC-Logo"> -->
                </div>
            </div><br>
        </div>
        <div class="col-md-4">

        </div>
        <div class="col-md-4">
            <div class="alert alert-success text-justify">
                <p class="bg-default">
                    <?php
                    $ccf = CCF::currentTime();
                    if ($ccf) {
                        // 
                        $sched = is_string($ccf) ? $ccf : $ccf['schedule'];
                        echo "ACY: <u><i>" . Yii::$app->params['C']['Y'].'-'.Yii::$app->params['C']['S'] . "</i></u>";
                        echo "<small class='pull-right badge label-info'>" . $sched . "</small>";
                    }
                    ?>
                </p>
            </div>
        </div>
        <?php
        if (Yii::$app->user->isGuest) {
        ?>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"> <?= Yii::t('rms', 'New Admission Form') ?> </div>
                <div class="panel-body" style="max-height: 250px; overflow-y: auto;">
                    <div class="container-fluid" id="applications">
                        <!-- <div class="col-md-6">
                                <button class="btn btn-success btn-block" onclick="applicationForm(1)">UG Program</button>
                            </div> -->
                        <!-- <a class="btn btn-xs btn-primary" href="#" onclick="loadAdmissionForm();return false">2.Admission Form</a> <br> -->

                        <?php
                            $scheduleEntry = Yii::$app->Admission->ScheduleOpen();
                            if ($scheduleEntry && count($scheduleEntry) > 0) { //check if schedule for admission is open
                                foreach ($scheduleEntry as $keySched) {
                                    $activeProgram = $keySched->activeProgramRel;
                                    echo "
                                        <button class='btn btn-xs btn-link' onclick='applicationForm1({$activeProgram->id},{$keySched->id})'>{$activeProgram->getClassLevelHeader()}</button>
                                        <br><small class='bg-warning highlighted pull-right'>Active: <i>{$keySched->getScheduleInterval()}</i></small>
                                    <hr>";
                                    // echo "
                                    //     <button class='btn btn-xs btn-link' onclick='applicationForm1({$activeProgram->program_relId},$keySched->id)'>{$activeProgram->getClassLevelHeader()}</button>
                                    //     <br><small class='bg-warning highlighted pull-right'>Active From <i>{$keySched->getScheduleInterval()}</i></small>
                                    // <hr>";
                                }
                            } else {
                                echo "Schedule NOT open for admission";
                            }

                            ?>
                    </div>

                    <hr>
                    <div class="container-fluid" id="applications">
                        <!-- <div class="col-md-6">
                                <button class="btn btn-success btn-block" onclick="applicationForm1(1)">UG(V.2) Program</button>
                            </div> -->
                        <!-- <div class="col-md-6">
                                <button class="btn btn-success btn-block" onclick="applicationForm1(2)">PG(V.2) (Masters)</button>
                            </div> -->
                    </div>
                </div>
            </div>
        </div>
        <?php
        }
        if (Yii::$app->user->can('Student')) {
        ?>
        <div class="col-md-4">
            <?php
                $std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
                if ($std && count([$std]) > 0) {
                    $gset = CCF::currentStudentGroup($std);
                    if ($gset) {
                        $gd = strtoupper($gset->gsProgRel->program_name) == 'MASTERS' ? "PG Advisor" : "Semester Advisor";
                        echo "<div class='panel panel-success'>
                                <div class='panel-heading'>
                                    {$gd} For This Semester:
                                </div>";
                        $model = new \backend\models\SemesterAdvisor();
                        $aInst = $model->getSemesterAdvisor($gset);
                        if ($aInst && count($aInst) > 0) {
                            foreach ($aInst as $keyInst) {
                                $userId = \backend\models\Instructor::findOne($keyInst->sa_inst_relId)->inst_user_relId;
                                $user = \common\models\User1::findOne($userId);
                                echo "

                                        <div class='panel-body justify-content'>
                                            <center>
                                                <img src='/uploads/" . $user->photo_num . "' width='100' height='140' />
                                                <p class='bg-info'>{$user->first_name} {$user->middle_name}</p>
                                            </center>
                                        </div>";
                            }
                        } else {
                            echo "<p class='bg-warning' style='text-align: center;'><u><i>Not Known</i></u></p>";
                        }
                        echo "</div>";
                    }
                } else {
                }
                ?>
        </div>
        <?php
        }
        if(Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')){
        ?>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"> <?= Yii::t('rms', 'User Activity') ?> </div>
                <div class="panel-body"  style="min-height: 110px; overflow: auto;">
                    <div class="container-fluid table-responsive" style="overflow:auto; max-height: 400px;"
                        id="usersActivitiesContainer">
                        <h5>User activities scroller</h5>
                    </div>
                </div>
            </div>
        </div>

        <?php
        }
        ?>
        <div class="col-md-12">
            <div class="panel panel-default" style="">
                <div class="panel-heading"> <?= Yii::t('rms', 'Programs') ?> </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive"
                        style=" max-height: 400px; overflow:auto; margin-right: -16px;" id="programsContainer">
                        <?= $this->render('../../modules/AcademicCalendar/views/ac-program-schedule/__report/active-programs') ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading"> <?= Yii::t('rms', 'Academic Departments') ?> </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive" style="overflow:auto;" id="departmentsContainer">
                        <?= $this->render('/department-head/__report/active-hods') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script></script>