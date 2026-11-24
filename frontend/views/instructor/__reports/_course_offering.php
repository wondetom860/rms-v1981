<?php

/** @var \backend\models\Instructor $model
 *
 */
if (isset($model)) {
    $offerings = $model->getCourseOffering2Details()
        ->joinWith('coff2detCoff2Rel')
        ->orderBy([
            'coff2_acadYear_relId' => SORT_DESC,
            'coff2_sem_relId' => SORT_DESC,
        ])
        ->all();

    if ($offerings) {
        echo "<div style='margin-top: 10px; max-height: 300px; overflow-y: auto;'>
                <h4>Course Offering</h4>";
        echo "<table class='table table-bordered table-sm table-striped table-hover' style='width: 100%; font-size: 8pt;'>";
        echo "<tr style='background-color:rgba(76, 127, 149, 0.69);'>
                <th>ACY</th>
                <th>Course</th>
                <th>Credits</th>
                <th>Section</th>
                <th>Enrols</th>
                <th>Evaluation</th>
                <th>Remark</th>
            </tr>";
        foreach ($offerings as $offering) {
            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
            $course = $offering->coff2detCourseRel;
            $enrolls = $offering->getEnrollsCount();
            $evRepBtns = $offering->getEvaluationResultV2();
            $ta = $offering->coff2det_inst_relId == $model->id ? 0 : 1;
            $btn = "<button class='btn btn-xs btn-primary' onclick='showEvaluation({$offering->id},{$ta});return false;' title='Show evaluation result from old version'>E1</button>";
            $btn2 = "<button class='btn btn-xs btn-info' onclick='sendAsmail({$offering->id},{$ta});return false;' title='Send result by email'>send</button>";
            echo "<tr>
                <td>" . $gset->getAcadYearHeader2() . "</td>
                <td>" . $course->course_title . "</td>
                <td>" . $course->getCourseCredits() . "</td>
                <td>" . $gset->getGsDisp() . "({$gset->gsGroupRel->group_name})</td>
                <td style='text-align: center;'><a href='#' onclick='showEnrollments({$offering->id});return false;'>{$enrolls}</a></td>
                <td>{$btn}{$evRepBtns}{$btn2}</td>
                <td></td>
            </tr>";
        }
        echo "</table></div>";
    } else {
        echo "No course offering found";
    }
} else {
    echo "Invalid request";
}

?>

<script>
    function sendAsmail(offeringdetailid, ta) {
        $.post('/instructor/send-evaluation-report-email', {
            offeringdetailid: offeringdetailid,
            ta: ta
        }).done((resp) => {
            resp ? alert(resp) : (window.location.href = window.location);
        });
    }

    function printEvaluationResultV2(fucId) {
        myWindow = window.open(
            '/faculty-course-assignment/generate-evaluation-result-v2?facuid=' + fucId + '&ta=0&__loyout_print=1',
            'title=Generate Evaluation Report V2',
            'height=900,width=800');
    }

    function showEvaluation(offeringId, ta) {
        myWindow = window.open(
            '/generate-semester-evaluation-report/get-report?coffDetId=' + offeringId + '&ta=' + ta + '&__loyout_print=1',
            'title=Generate Evaluation Report',
            'height=900,width=800');
        // $('#modal').find('.modal-header').html('Course Offering Evaluation');
        // $('#modalContent').css({
        //     'text-align': 'left',
        //     'margin-top': '0px'
        // });
        // $('#modalContent').html('Loading data...');
        // $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');

        // $.ajax({
        //     url: '/generate-semester-evaluation-report/get-report',
        //     type: 'GET',
        //     data: {
        //         coffDetId: offeringId,
        //         ta: 0,
        //         __layout_print: 0
        //     },
        //     success: function(response) {
        //         $('#modal').modal('show');
        //         $('#modalContent').html(response);
        //     },
        //     error: function(xhr, status, error) {
        //         // alert('Error: ' + error);
        //         console.log(xhr, status, error);
        //     }
        // });
    }

    function showEnrollments(offeringId) {
        $('#modal').find('.modal-header').html('Course Offering Enrollments');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');

        $.ajax({
            url: '/course-offering2-detail/get-enrollments',
            type: 'GET',
            data: {
                coff2detId: offeringId
            },
            success: function(response) {
                $('#modal').modal('show');
                $('#modalContent').html(response);
            },
            error: function(xhr, status, error) {
                alert('Error: ' + error);
            }
        });
    }
</script>