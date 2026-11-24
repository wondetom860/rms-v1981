<?php

use frontend\modules\ExitExam\models\EtElligiblityTest;

$last_year = $gsetModel->gsAcadYearRel->sch_year_desc;
$last_year = explode('/', $last_year);
$year_completed1 = $last_year[0] -7;
$male = $modelStudent->stdUserRel->gender == 'MALE' ? true : false;
$gen_en = $male ? 'He' : 'She';
$gen_en2 = $male ? 'His' : 'Her';
$gen_am = [
    0 => $male ? 'ተመዝግቦ' : 'ተመዝዝግባ',
    1 => $male ? 'አጠናቋል' : 'አጠናቃለች',
    2 => $male ? 'ወስዶ' : 'ወስዳ',
    3 => $male ? 'ያስመዘገበ' : 'ያስመዘገበች',
    4 => $male ? 'አላገኘም' : 'አላገኘችም',
    5 => $male ? 'ማጠናቀቁን' : 'ማጠናቀቋን',
    6 => $male ? 'ሲያስመዘግብ' : 'ስታስመዘግብ',
];
$lastSemreg = $modelStudent->getLastSemreg();
$cgpa = '-';
if($lastSemreg){
    $lAcadYear = $lastSemreg->semregAcadYearRel->sch_year_desc;
    $lAcadYear = explode('/', $lAcadYear);
    $year_completed2 = "20".$lAcadYear[1] - 7;
    // $lastSemreg->semreg_CGPA
    $cgpa = sprintf("%.2f", $lastSemreg->semreg_CGPA);
}

$year_completed = $year_completed1 != $year_completed2 ? $year_completed2 : $year_completed2;
$year_completed = "20{$last_year[1]} (" . $year_completed1 . " E.C)";
$year_completed_am = "20{$last_year[1]} (" . $year_completed1 . " ዓ.ም)";

$date_of_exit_exam = "-";
$date_of_exit_exam_am = "-";
$exit_exam_status = "-";
$exit_exam_result = "-";

$adYear = $modelStudent->getAdmissionYear();

$exitExamResult = EtElligiblityTest::getFailedEtRecord($modelStudent->id);
if ($exitExamResult) {
    $etDetail = $exitExamResult->getEtTestDetails()->where(['pass_status' => 0, 'grade_accepted' => 1])->one();
    // var_dump($exitExamResult);die();

    if($etDetail){
        $exit_exam_status = "HAS NOT REGISTERED A PASS MARK";
        $exit_exam_result = sprintf("%.2f", $etDetail->mark_obtained);
        $d = date('Y-m-d', $etDetail->date_submitted);
        $d = explode('-', $d);
        $date_of_exit_exam = $d[0] . " (". $d[0]-8 ." E.C)";
        $date_of_exit_exam_am = $d[0] . " (". $d[0]-8 ." ዓ.ም)";

    }
}else{
    echo "<p class='bg-warning'>Has Not taken National Exit Exam Yet.</p>";
    return;
}
?>

<div class="container temporary-certificate">
    <div class="row">
        <div class="col-md-12" style="border-bottom: 1px solid #000;">
            <center>
                <img src="/logo/etdu.jpeg" alt="Logo" style="width: 90px; height: auto;"><br>
            </center>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6" style="padding: 1px 20px;">
            <h4 style="text-align: center; text-decoration: underline;">
                To Whom It May Concern
            </h4>
            <p style="text-align: justify; font-size: 10pt;  min-height: 280px; overflow-y: hidden;">
                This is to certify that <strong><u><?= $modelStudent->getFullName() ?>(ID No:
                        <?= $modelStudent->std_regId ?>)</u></strong> was admitted to
                <strong><u><?= $modelStudent->stdAdmissionRel->admission_name ?></u></strong>
                <strong><u><?= $modelStudent->stdProgRel->program_name ?></u></strong> in
                <strong><u><?= strtoupper(Yii::$app->params['client']) ?></u></strong> in the
                <strong><u><?= $adYear[0] ?></u></strong> Academic
                Year. <?= ucfirst($gen_en) ?> has completed
                the course requirements for the
                <strong><u><?= $modelStudent->stdProgRel->getDegreeNomenclature() ?></u></strong>
                in <strong><u><?= $modelStudent->stdDeptRel->dept_name ?></u></strong> in
                <strong><u><?= $year_completed ?></u></strong> year with a CGPA of <strong><u><?= $cgpa ?></u></strong>.
                However, <?= $gen_en ?> <strong><u><?= $exit_exam_status ?></u></strong> in the national exit examination which
                was administered from <strong><u><?= $date_of_exit_exam ?></u></strong>. <br>
                This certificate is issued as confirmation of completion of course-works, pending the issuance of
                temporary certificate that fully depends on <?= strtolower($gen_en2) ?> successfull completion of the national exit
                examination.
                <br><br>
                <br><br>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <center>
                        ___________________________<br>
                        Registrar Head
                    </center>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <center>
                        ___________________________<br>
                        University President
                    </center>
                </div>
            </div>
            <br><br>
            </p>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6"  style="padding: 1px 20px;">
            <h4 style="text-align: center; text-decoration: underline;">
                ለሚመለከተው ሁሉ
            </h4>
            <p style="text-align: justify; font-size: 10pt; min-height: 280px; overflow-y: hidden;">
                ይህ የምስክር ወረቀት 
                ተማሪ <strong><u><?= $modelStudent->getFullName_am() ?></u></strong>(መታወቂያ
                ቁጥር:<strong><u><?= $modelStudent->std_regId ?>)</u></strong>
                በ<strong><u><?= $modelStudent->stdAdmissionRel->admission_name ?></u></strong>
                <strong><u><?= $modelStudent->stdProgRel->program_name ?></u></strong>
                በ<strong><u><?= strtoupper(Yii::$app->params['client_am']) ?></u></strong>
                በ<strong><u><?= $adYear[1] ?></u></strong> የትምህርት ዘመን <?= $gen_am[0] ?>
                <strong><u><?= $year_completed_am ?></u></strong> በ
                <strong><u><?= $modelStudent->stdDeptRel->dept_name ?></u></strong>
                የ<strong><u><?= $modelStudent->stdProgRel->getDegreeNomenclature_am() ?></u></strong> የኮርስ መስፈርቶችን በCGPA
                <strong><u><?= $cgpa ?></u></strong> <?= $gen_am[1] ?>። ይሁን እንጂ የተሰጠውን የብሄራዊ መውጫ ፈተና <?= $gen_am[2] ?> ከመቶ
                <strong><u><?= $exit_exam_result ?></u></strong> <?= $gen_am[3] ?> በመሆኑ የማለፊያ ዉጤቱን
                <strong><u><?= $gen_am[4] ?></u></strong>። <br>

                ይህ የምስክር ወረቀት በመስኩ የሚሰጡትን ኮርሶች <?= $gen_am[5] ?> ለማረጋገጥ የተሰጠ ሲሆን ጊዚያዊ የድግሪ የሚሰጠው የመዉጫ ፈተናውን በድጋሚ <?= $gen_am[2] ?> የማለፊያ
                ነጥብ <?= $gen_am[6] ?> ብቻ ይሆናል።
                <br><br>
                <br><br>
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <center>
                        ___________________________<br>
                        ረጅስትራር ኃላፊ
                    </center>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6">
                    <center>
                        ___________________________<br>
                        ዩኒቨርሲቲ ፕሬዝዳንት
                    </center>
                </div>
            </div>
            <br><br>
            </p>
        </div>
    </div>
</div>

<style>
div.temporary-certificate {
    width: 100%;
    height: 100%;
    padding: 20px 40px;
    background-color: #f5f5f5;
    font-family: Arial, sans-serif;
    border: 5px dashed #000;
}
</style>