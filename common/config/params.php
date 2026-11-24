<?php
/*
 * Configuration for common system values
 * Access it through \Yii::$app->params['var']
 */

use backend\models\SchoolYear;

// define('ALTE_LIST', ["YPOC-TNEDUTS", "TROPER-EDARG", "KCEHC-NOITELPMOC-RETSEMES", "XEDNI/YPOC-TNEDUTS", "XEDNI/TROPER-EDARG-RETSEMES-TNEDUTS", "TROPER-EDARG-RETSEMES-TNEDUTS", "XEDNI/SEDARG-DETTIMBUS", "SEDARG-DETTIMBUS"]);
// define('ERR_MESSAGE', 'FILE_DOESN\'T EXIST (404)!');
// define('__MSG', "System is in trouble of finding such file, Any Change made to SYTEM_DIRECTORY? system_directory_file_altered!");
// define('LIC_KEY', 'q6H3QuhXDZlz8VrdFN/qHCFaJndNB81CehdxmmqJSF1cOxsV1t+wkM755pO47CkAfPx3ccQ6+6w3DwcJcpL6eMmNwX4l4ppLLh9nLSBtLMqNKXXmbPu96ZfHtOYbxkjHk14Bbn8xbuSI980kAIOb6jT72p18ZuJWA65a5Dh5b0W+Sj4+/MTj95uRgCKxVxOdCd1XNDHIesWDb2pv1rx3PMH0Lq+CT20xBqR5kOfXGvvQw5cQpEOZ1Xg5DeRYAcZ3SexjqYbBWQ8xWi0M+gs5zMw8QrctPJAoztPvODf6Y/aEeoE4p0XICU1Rx4tvSPDlrEHok40evXhZbyAeEdaYfQbc+XrDTBds8hJByQXNRSsWWB4VUWkAiMTEeodP7DLQ');
// define('_MSG_NOT_ALLOWED', "You Are NOT allowed for this action, Please contact the developers, There is Problem with System CORE configuration!");

return [
    'adminEmail' => 'admin@rmsback.local',
    'supportEmail' => 'support@rmsback.local',
    'user.passwordResetTokenExpire' => 3600,
    'notifications' => [
        'title' => '',
        'count' => 0,
        'link' => '',
    ],
    'status' => [
        0 => 'NOT-ACTIVE',
        1 => 'ACTIVE',
    ],
    'C' => [
        'Y' => getCurrentYear(date('m'), date('Y')),
        'S' => NULL,
        'schedule' => NULL
    ],
];

function getCurrentYear($month, $year)
{
    $currYr = '';
    // slice year to take only the last two digits.
    $sYear = $year % 1000;
    if ($month >= 9) {
        $currYr = $year . '/' . ($year + 1) % 1000;
    } else if ($month < 9) {
        $currYr = ($year - 1) . '/' . $year % 1000;
    }

    return $currYr;
}

function createSchoolYear($year)
{
    $ct = $year;
    $acadYear = SchoolYear::findOne(['sch_year_desc' => $ct]);
    if (empty($acadYear)) {
        $model = new SchoolYear();
        $model->sch_year_desc = $ct;
        $model->sch_year_status = 1;
        $model->sch_created_by = 1;
        $model->sch_updated_by = $model->sch_created_by;
        $model->sch_created_at = date("Y-m-d h:i:s");
        $model->sch_updated_at = $model->sch_created_at;

        if (!$model->save()) {
            throw new yii\base\Exception("Academic Year Initialization ERROR!", 1);
        } else {
            $acadYear = $model;
        }
    }
    return $acadYear;
}
