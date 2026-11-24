<?php

if (isset($model)) {
    $this->title = 'Unapproved Requests';
    // $this->params['breadcrumbs'][] = ['label' => 'Exit Exam', 'url' => ['index']];
    // $this->params['breadcrumbs'][] = $this->title;
?>
    <div class="et-elligiblity-test-index container-fluid">
        <table class="table table-condensed table-bordered table-sm">
            <caption style="text-align: center; color: #000; font-weight: bold; font-size: 1.2em;">
                <h4>
                    Un-apporved Exit Exam Requests
                </h4>
            </caption>
            <thead style="background-color:rgb(117, 148, 207);">
                <tr>
                    <th>Student ID</th>
                    <th>Year of Test</th>
                    <th>Full Name</th>
                    <th>Reason</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($model as $value) {
                    $test = $value->testRel;
                    $std = $test->semregRel->semregStdRel;
                ?>
                    <tr>
                        <td><?= $std->std_regId ?></td>
                        <td><?= $value->acadYearRel->getAcHeader() ?></td>
                        <td><?= $std->getFullName() ?></td>
                        <td><?= $test->_reasons[$test->reason] ?></td>
                        <td><?= $test->_pass_status[$test->pass_status] ?></td>
                        <td><a href="#" onclick="changeApprovalStatus(<?= $value->id ?>); return false" class="btn btn-success btn-xs" title='Change approval status'><?= $value->grade_accepted ? "Cancel" : "Approve" ?></a></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
<?php
} else {
    echo "<p class='bg-warning'>Invalid Request</p>";
}
?>


<script>
    window.changeApprovalStatus = (detailId) => {
        var url = "<?= \yii\helpers\Url::to(['et-test-detail/change-approval-status']) ?>";
        var data = {
            id: detailId
        };
        if (confirm("Are you sure you want to change the approval status?")) {
            $.post(url, data, function(response) {
                if (response.status == 'success') {
                    showUnApprovedRequests(); 
                } else {
                    alert('Failed to change the approval status.');
                }
            }, 'json');
        }  
    }
</script>