<?php

use backend\models\SemesterRegistration2Detail;

if (isset($offerings) && count($offerings) > 0) {
    echo "<table class='table table-condensed table-sm table-bordered table-hover'>";
    echo "<caption style='color: #000; font-weight: bold;'>
            <h4 style='text-align: center;'>Course Offerings for <u>{$req->courseRel->course_title} <i>[{$req->courseRel->getCourseCredits()}]</i></u></h4>
        </caption>";
    echo "<thead>
            <tr style='background-color: #DEF;'>
                <th>#</th>            
                <th>ACY</th>            
                <th>Department</th>            
                <th>Section</th>            
                <th>Instructor</th>
                <th>Option</th>
            </tr>
        </thead><tbody>";
    $count = 0;
    foreach ($offerings as $offeringDetail) {
        $gset = $offeringDetail->coff2detCoff2Rel->coff2GroupRel;
        $acy = $offeringDetail->coff2detCoff2Rel->coff2detAcProgRel->academicYearRel;
        $inst = $offeringDetail->coff2detInstRel ? $offeringDetail->coff2detInstRel->getFullName() : "-";
        $enrolledInThisYear = SemesterRegistration2Detail::find()
            ->innerJoinWith('srd2SemregRel')
            ->andWhere([
                'semreg_acadYear_relId' => $acy->schoolYearRel->id,
                'semreg_semester_relId' => $acy->semesterRel->id,
                'semreg_std_relId' => $req->std_relId,
                'srd2_course_relId' => $req->course_relId,
            ])
            ->all();

        $option = (count($enrolledInThisYear) > 0) ? "Enrolled in this ACY" : "<button class='btn btn-xs btn-primary' title='Enroll course with this section' onclick='selectOffering({$offeringDetail->id},{$req->id})'>Select</button>";
        echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$acy->getAcHeader()}</td>
                <td>{$gset->gsDeptRel->dept_code}</td>
                <td>{$gset->getGsDisp()}</td>
                <td>{$inst}</td>
                <td>{$option}</td>
        </tr>";
    }
    echo "</table>";
} else {
    echo "<p>Offering NOT found for the course.</p>";
}
