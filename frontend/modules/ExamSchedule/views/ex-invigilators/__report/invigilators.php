<?php

use backend\models\Instructor;
use yii\bootstrap\Html;

/**
 * @var \frontend\modules\ExamSchedule\models\ExExamRoomSlot $model
 */

$invigilators = $model->exInvigilators;
/**
 * @var yii\web\View $this
 */
$count = 0;
?>
<div class="row">
    <div class="col-md-12">
        <h4 style="text-align: center;">
            Invigilators Assigned for this Exam Room Slot
            <?php if (isset($addNew)): ?>
                <button class="btn btn-xs btn-info pull-right" onclick="addNewInvigilator(<?= $model->id ?>)">Add New</button>
            <?php endif ?>
        </h4>
        <?php if ($invigilators): ?>
            <table class="table table-sm table-condensed table-hover">
                <tr style="background-color: aquamarine;">
                    <th>#</th>
                    <th>Full Name</th>
                    <th>Phone</th>
                    <th>Dept.</th>
                    <th>Type</th>
                    <th>Emp. Type</th>
                    <th>Room</th>
                    <th>Option</th>
                </tr>
                <?php foreach ($invigilators as $staff): $inst = Instructor::findOne(['inst_user_relId' => $staff]) ?>
                    <tr>
                        <td><?= ++$count ?></td>
                        <td><?= $inst->getFullName() ?></td>
                        <td><?= $inst->instUserRel->phone_mobile ?></td>
                        <td><?= $inst->instDepRel->dept_code ?></td>
                        <td><?= $inst->instUserRel->user_type ?></td>
                        <td><?= $inst->employmentTypeRel ? $inst->employmentTypeRel->et_name : 'Uknown' ?></td>
                        <td><?= $model->getRoomHeader() ?></td>
                        <td><?= Yii::$app->user->can('Admin') ?
                                Html::a(
                                    '<i class="fa fa-trash"></i>',
                                    ['#'],
                                    [
                                        'class' => 'btn btn-xs btn-danger',
                                        'title' => 'Change Invigilator',
                                        'onclick' => "removeInvigilator({$model->id},{$inst->inst_user_relId});return false;"
                                    ]
                                ) : '' ?></td>
                    </tr>
                <?php endforeach ?>
            </table>
        <?php endif ?>
    </div>
</div>


<script>
    window.removeInvigilator = (exroomslotid, satffid) => {
        if (confirm('You want to change this invigilator?')) {
            $.post('/ExamSchedule/ex-invigilators/remove-invigilator', {
                exroomslotid: exroomslotid,
                satffid: satffid,
            }).then((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                }
            });
        }
    }
    window.addNewInvigilator = (roomSlotId) => {
        $('#modal').find('.modal-header').html('Add invigilator(s) to this exam room slot');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/ExamSchedule/ex-invigilators/create2', {
            roomSlotId: roomSlotId,
        }, function(data) {
            $('#modalContent').html(data);
        });
    }
</script>