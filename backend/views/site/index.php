<?php
/** @var yii\web\View $this */

use backend\models\Program;
use backend\models\Admission;
use yii\helpers\Url;

$this->title = Yii::$app->name .' | '.Yii::$app->params['college'];
?>
<div class="site-index">
<div class="row">
    <div class="col-md-8">

        <p class="lead">Registrar Information System</p>
        <span><?php echo ' SEMESTER - '.\Yii::$app->params['status'][1]; ?></span>

    </div>
    <div class="col-md-4">
      <!--  <?php
            echo "<br> Hello <b>". Yii::$app->user->identity->username."</b>, <br>Welcome to DEC-WBRMS. Now you are in Semester: <b>" . Yii::$app->params['C']['S']."</b> of Academic Year: <i>" . Yii::$app->params['C']['Y']."</i><hr>";
        ?> -->
    </div>
    </div>
    <div class="row">
    <div class="col-md-4">
        <div class="panel panel-default">
        <div class="panel-heading">
            Programs available in DEC
        </div>
        <div class="panel-body">
            <?php 

            // $progs = Program::find()->all();
            // echo "<table class='table table-hover table-dark table-sm table-striped'>";
            // foreach ($progs as $prog) {
            //     echo "<tr>
            //         <td>".$prog->id."</td>
            //         <td>".$prog->program_name."</td>
            //     </tr>";
            // }
            // echo "</table>";

            //print_r(Yii::$app->params['currentAcademicStatus']);

            ?>
        </div>
    </div>

    </div>
    <div class="col-md-4">
        <div class="panel panel-success">
            <div class="panel-heading">
                Admissions in DEC
            </div>
            <div class="panel-body">
                <?php 

                // $adms = Admission::find()->all();
                // echo "<table class='table table-hover table-dark table-sm table-striped'>";
                // foreach ($adms as $amd) {
                //     echo "<tr>
                //         <td>".$amd->id."</td>
                //         <td>".$amd->admission_name."</td>
                //     </tr>";
                // }
                // echo "</table>";
                ?>
            </div>
    </div>

    </div>
    <div class="col-md-4">
        <div class="panel panel-warning">
        <div class="panel-heading">
            Academic Schedule Activities
        </div>
        <div class="panel-body">
            <div class="container-fluid" id="activitiesPort">
                
            </div>
        </div>
    </div>

    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
        <div class="panel-heading">
            Academic Semesters
        </div>
        <div class="panel-body">
            <?php 

                // $adms = backend\models\Semester::find()->all();
                // echo "<table class='table table-hover table-dark table-sm table-striped'>";
                // echo "<thead class='tab-dark'><tr><td>#</td><td>Semester</td><td>Semester Status</td></tr></thead>";
                // foreach ($adms as $amd) {
                //     $cl = $amd->semester_name == Yii::$app->params['currentAcademicStatus']['semester']?"ACTIVE":"NOT-ACTIVE";
                //     echo "<tr>
                //         <tbody>
                //             <td>".$amd->id."</td>
                //             <td>".$amd->semester_name."</td>
                //             <td>".$cl."</td>
                //         </tbody>
                //     </tr>";
                // }
                // echo "</table>";
                ?>
        </div>
    </div>

    </div>
    <div class="col-md-4">
        <div class="panel panel-secondary">
            <div class="panel-heading">
                Main Academic Calander Activities in DEC
            </div>
            <div class="panel-body">
               <?php 

                    // $adms = backend\models\AcademicCalanderAction::find()->all();
                    // $acaCount = backend\models\AcademicCalanderAction::find()->count();
                    // echo "<table class='table table-hover table-dark table-sm table-striped'>";
                    //     if ($acaCount > 0) {
                    //         foreach ($adms as $amd) {
                    //         echo "<tr>
                    //             <td>".$amd->id."</td>
                    //             <td>".$amd->aca_name."</td>
                    //         </tr>";
                    //     }
                    // }else{
                    //         echo "<tr><td><i>No record found yet.</i></td></tr>";
                    // }
                    // echo "</table>";
                    ?>
            </div>
    </div>

    </div>
    <div class="col-md-4">
        <div class="panel panel-warning">
        <div class="panel-heading">
            Courses offered in this semester:
        </div>
        <div class="panel-body">
            <?php 

                    // $adms = backend\models\CourseCatalog::find()->Where([
                    //     'cocat_sem_relId'=>backend\models\Semester::findOne([
                    //         'semester_name'=>Yii::$app->params['currentAcademicStatus']['semester']
                    //     ])
                    // ])->all();

                    // $acaCount = backend\models\CourseCatalog::find()->Where([
                    //     'cocat_sem_relId'=>backend\models\Semester::findOne([
                    //         'semester_name'=>Yii::$app->params['currentAcademicStatus']['semester']
                    //     ])
                    // ])->count();


                    // echo "<table class='table table-hover table-dark table-sm table-striped'>";
                    //     if ($acaCount > 0) {
                    //         $c = 0;
                    //         foreach ($adms as $amd) {
                    //             $ct = backend\models\Course::find()->select('course_title, course_code')->Where(['id'=>$amd->coff_course_relId])->all();
                            
                    //         echo "<tr>
                    //             <td>".++$c."</td>
                    //             <td>".$ct[0]->course_code." => ".$ct[0]->course_title."</td>
                    //         </tr>";
                    //         //print_r($ct);
                    //     }
                    // }else{
                    //         echo "<tr><td><i>No Course offered in this semester.</i></td></tr>";
                    // }
                    // echo "</table>";
                    ?>
        </div>
    </div>

    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-warning">
        <div class="panel-heading">
            Whole sepcializations in DEC - Degree Program -(<i>Undergraduate</i>)
        </div>
        <div class="panel-body">
            <?php

            // $specsCount = backend\models\Specialization::find()->Where(['spec_prog_relId'=>
            //     backend\models\Program::findOne([
            //         'program_name'=>'Undergraduate'
            //     ])
            //     ])->count();

            // $specs = backend\models\Specialization::find()->Where(['spec_prog_relId'=>
            //     backend\models\Program::findOne([
            //         'program_name'=>'Undergraduate'
            //     ])
            //     ])->all();

            // echo "<table class='table table-hover table-dark table-sm table-striped'>";
            //             if ($specsCount > 0) {
            //                 $c = 0;
            //                 foreach ($specs as $amd) {
            //                     $dept = backend\models\Department::find()->select('dept_name, dept_code')->Where(['id'=>$amd->spec_dep_relId])->all();
                            
            //                 echo "<tr>
            //                     <td>".++$c."</td>
            //                     <td>".$amd->spec_name." --> <b>".$dept[0]->dept_name." Dept.</b></td>
            //                 </tr>";
            //                 //print_r($ct);
            //             }
            //         }else{
            //                 echo "<tr><td><i>No specialization available.</i></td></tr>";
            //         }
            //         echo "</table>";

            ?>
        </div>
    </div>

    </div>
    <div class="col-md-4">
        <div class="panel panel-warning">
        <div class="panel-heading">
            Whole sepcializations in DEC - Master Program (<i>POST GRADUATE</i>)
        </div>
        <div class="panel-body">
                        <?php

            // $specsCount = backend\models\Specialization::find()->Where(['spec_prog_relId'=>
            //     backend\models\Program::findOne([
            //         'program_name'=>'Postgraduate'
            //     ])
            //     ])->count();

            // $specss = backend\models\Specialization::find()->Where(['spec_prog_relId'=>
            //     backend\models\Program::findOne([
            //         'program_name'=>'Postgraduate'
            //     ])
            //     ])->all();

            // echo "<table class='table table-hover table-dark table-sm table-striped'>";
            //             if ($specsCount > 0) {
            //                 $c = 0;
            //                 foreach ($specss as $amds) {
            //                     //get department name via its id 
            //                     $dept = backend\models\Department::find()->select('dept_name, dept_code')->Where(['id'=>$amds->spec_dep_relId])->all();
                            
            //                 echo "<tr>
            //                     <td>".++$c."</td>
            //                     <td>".$amds->spec_name." --> <b>".$dept[0]->dept_name." Dept.</b></td>
            //                 </tr>";
            //                 //print_r($ct);
            //             }
            //         }else{
            //                 echo "<tr><td><i>No specialization available for this prgram.</i></td></tr>";
            //         }
            //         echo "</table>";

            ?>
        </div>
    </div>

    </div>
    <div class="col-md-4">
        
    </div>
</div>
</div>
</div>

<!-- activitiesPort -->

<?php
$this->registerJs(
    "
    $.post('/activity/active',{},function(data){
        // console.log(data);
        $('#activitiesPort').html(data);
        });
    ",
    $this::POS_READY
);
?>