<?php

use backend\models\Instructor;
use common\models\User;

// $standbyUsers = ArrayHelper::map(
//     Instructor::find()
//         ->innerJoinWith('instUserRel')
//         ->where([
//             'user.status' => User::STATUS_ACTIVE,
//             'user.activated' => 1
//         ])
//         ->orderBy([
//             'first_name' => SORT_ASC,
//             'middle_name' => SORT_ASC,
//             'last_name' => SORT_ASC
//         ])
//         ->all(),
//     'id',
//     function ($model) {
//         return $model->getFullName();
//     }
// );

$superviserUserIds = $model->supervisors ? json_decode($model->supervisors, true) : [];

$allUsers = Instructor::find()
    ->innerJoinWith('instUserRel')
    ->where([
        'user.status' => User::STATUS_ACTIVE,
        'user.activated' => 1
    ])
    ->andWhere(['not in', 'inst_user_relId', $superviserUserIds])
    ->orderBy([
        'first_name' => SORT_ASC,
        'middle_name' => SORT_ASC,
        'last_name' => SORT_ASC
    ])
    ->all()
?>

<form action="#">
    <div class="row">
        <div class="hidden">
            <input type="hidden" name="id" value="<?= $model->id ?>">
        </div>
        <div class="col-md-10">
            <div class="form-group">
                <label for="standby_user_id">Search Staff as <b>Standby</b></label>
                <select class="form-control" name="staff_user_id" id="staff_user_id" placeholder="Select Staff">
                    <?php
                    foreach ($allUsers as $user) {
                        $user = $user->instUserRel;
                        echo "<option value='{$user->id}' title='{$user->getFullName()}'>{$user->first_name} {$user->middle_name} {$user->last_name}</option>";
                    }
                    // echo Select2::widget([
                    //     'name' => 'standby_user_id',
                    //     'data' => $standbyUsers,
                    //     'options' => ['placeholder' => 'Select Staff'],
                    //     'pluginOptions' => [
                    //         'allowClear' => true,
                    //     ],
                    // ]);
                    ?>
                </select>
                <!-- <input type="text" class="form-control" id="standby_user_id" name="standby_user_id" placeholder="Type Staff Name"> -->
            </div>
        </div>
        <div class="col-md-2"><br>
            <div class="form-group" style="margin-top: 10px;">
                <button class="btn btn-xs btn-primary" onclick="saveForm();return false;">Save</button>
            </div>
        </div>
    </div>
</form>

<script>
    function saveForm() {
        if (confirm('Assign the staff as stabdby?')) {
            var id = $('input[name="id"]').val();
            var standby_user_id = $('#staff_user_id').val();
            $.ajax({
                url: '/ExamSchedule/ex-date-time-schedule/add-standby?id=' + id,
                type: 'POST',
                data: {
                    id: id,
                    standby_user_id: standby_user_id
                },
                success: function(data) {
                    if (data == 1) {
                        // alert('Staff assigned as standby.');
                        // location.reload();
                        ShowAddStandbyForm(id);
                    }
                }
            });
        }
    }
</script>