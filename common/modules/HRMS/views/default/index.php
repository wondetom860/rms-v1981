<?php 

$this->title = Yii::t('rms', 'Employees\' Personal Infomarmtion on HRMS');
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="HRMS-default-index container-fluid table-responsive" style="background-color: #fff; font-size: 9pt;">
    <h3><?= $this->title ?></h3>
    <table class="table table-condensed table-sm table-hover table-bordered" cellpadding="0" cellspacing="1">
        <caption style="font-weight: normal; color: #000;">
            <center>
                <h5><?= Yii::t('rms','Employees signed up in RMS') ?></h5>
                <small class="bg-info">These are list of employees who have personal information detail on HRMS. To keep consistency of records of employees, please check if there is any update on their profile</small>
            </center>
        </caption>
        <thead>
            <tr style="background-color: #efe; text-align: center;">
                <th colspan="6">Personal Information on HRMS<br>
                    <small><?= Yii::$app->params['client'].' '.Yii::$app->params['college'] ?></small></th>  
                <th colspan="6" style="background-color: #edd;">Personal Information on <?= Yii::$app->name ?><br>
                    <small><?= Yii::$app->params['client'].' '.Yii::$app->params['college'] ?></small>
                </th>  
                <th rowspan="2" style="background-color: #fcc;">Option</th>
            </tr>
            <tr>
                <th><?= Yii::t('rms','ID NO') ?></th>
                <th><?= Yii::t('rms','Full Name') ?></th>
                <th><?= Yii::t('rms','Gender') ?></th>
                <th><?= Yii::t('rms','User Type') ?></th>
                <th><?= Yii::t('rms','Military Rank') ?></th>
                <th><?= Yii::t('rms','Academic Rank') ?></th>

                <th><?= Yii::t('rms','ID NO') ?></th>
                <th><?= Yii::t('rms','Full Name') ?></th>
                <th><?= Yii::t('rms','Gender') ?></th>
                <th><?= Yii::t('rms','User Type') ?></th>
                <th><?= Yii::t('rms','Military Rank') ?></th>
                <th><?= Yii::t('rms','Academic Rank') ?></th>
            </tr>
        </thead>
        
        <tbody>
            <?php 

            $allRmsLinkedUsers = \common\modules\HRMS\models\RmsUserLink::find()->all();

            if ($allRmsLinkedUsers && count($allRmsLinkedUsers) > 0) {
                foreach ($allRmsLinkedUsers as $keyRmsLinkedUser) {

                    $title = "";
                    $bg = 'default';
                    $btnOption = "<button class='btn btn-xs btn-warning' title='Update employee personal information to the latest HRMS records' onclick='updateEMployeeRecord({$keyRmsLinkedUser->id});return false;'>Update</button>";
                    // info on HRMS
                    $ID = $keyRmsLinkedUser->personRel->MIL_ID_NO ? $keyRmsLinkedUser->personRel->MIL_ID_NO : $keyRmsLinkedUser->personRel->ID_NO;

                    if (is_null($keyRmsLinkedUser->hrms_data)) {

                        $keyRmsLinkedUser->hrms_data = $keyRmsLinkedUser->personRel->data;
                        // $keyRmsLinkedUser->updated_by = Yii::$app->user->identity->id;
                        $keyRmsLinkedUser->updated_at = date("Y-m-d h:i:s");
                        $keyRmsLinkedUser->data = $keyRmsLinkedUser->getData();
                        $keyRmsLinkedUser->update();

                        $title = "There is update on account for this employee on HRMS";
                        $bg = "info";

                    }else if ($keyRmsLinkedUser->hrms_data != $keyRmsLinkedUser->personRel->data) {
                        $title = "There is update on account for this employee on HRMS";
                        $bg = "info";
                    }

                    echo "<tr class='bg-{$bg}' title='{$title}'>
                            <td>{$ID}</td>
                            <td>{$keyRmsLinkedUser->personRel->getFullName()}</td>
                            <td>{$keyRmsLinkedUser->personRel->gender}</td>
                            <td>{$keyRmsLinkedUser->personRel->profession_type}</td>
                            <td>{$keyRmsLinkedUser->personRel->getMilitaryRank()}</td>
                            <td>{$keyRmsLinkedUser->personRel->getAcademicRank()}</td>";

                    // user info on rms
                    $keyRmsAll = $keyRmsLinkedUser->rmsAll();//this is user INSTANCE of DECRIS-user model
                    echo "
                            <td style='background-color: #fee;'>-</td>
                            <td style='background-color: #fee;'>{$keyRmsLinkedUser->getRMSFullName()}</td>
                            <td style='background-color: #fee;'>{$keyRmsAll->gender}</td>
                            <td style='background-color: #fee;'>{$keyRmsAll->user_type}</td>
                            <td style='background-color: #fee;'>-</td>
                            <td style='background-color: #fee;'>-</td>

                            <td style='background-color: #fcc;'>{$btnOption}</td>
                        </tr>";
                }
            }


            ?>
        </tbody>
    </table>

    <script type="text/javascript">
        window.updateEMployeeRecord = (rmsLinkEntryId) => {
            if (confirm('Are you sure to import employee\'s information')) {
                $.post('/HRMS/default/get-update',{rmsLinkEntryId:rmsLinkEntryId}).done((response)=>{ if (response == 1) { location.reload(); }else{ alert(response); } });
            }
        }
    </script>
</div>
