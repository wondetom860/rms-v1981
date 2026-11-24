<?php

/**
 * @var $this yii\web\View
 * @var $studentName string
 * @var $academicYear string
 * @var $semester string
 */
?>
<div class="grade-statement-email">

    <h3>Student Semester Grade Report</h3>
    <div style="border: 1px dashed black; padding: 15px;">
        Dear Sir/Madam, <br><br>
        Please find attached the semester grade report of the student <b><?= $studentName ?></b> for the academic year <b><?= $academicYear ?></b> and semester <b><?= $semester ?></b>. <br><br>
        Kindly review the attached grade report and take necessary actions. <br><br>
        Thank you for your attention to this matter. <br><br>
        Best regards, <br>
        <b><?= "ETDU Regstrar and Alumni Directorate" ?></b><br>
        <b><?= "univ.reg.alumin@etdu.edu.et" ?></b><br>
    </div>
</div>