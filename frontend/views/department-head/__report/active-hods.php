<?php 

$depts = \backend\models\DepartmentHead::find()
    ->innerJoin('department','department.id=department_head.dh_dept_relId')
    ->where(['dh_status' => 1])
    ->orderBy(['dept_name' => 2])
    ->all();

if ($depts && count($depts) > 0) {
    echo "<table class='table table-striped table-bordered table-condensed table-hover'> ";
    echo "<caption>Academic Departments in ". \Yii::$app->params['client'] .", ".\Yii::$app->params['college'].".</caption>";
    echo "<thead style='background-color:#1FFc66;margin-top:2px;'>
            <th>#</th>
            <th>Department Name</th>
            <th>Head of Department</th>
            <th>Dept. Phone (Ext.)</th>
        </thead><tbody>";
    $count = 0;
    foreach ($depts as $key) {
        if (!$key->dhInstRel) {
            continue;
        }
        if (\Yii::$app->user->can('DepartmentHead')) {
            $instdept = \backend\models\Instructor::find()->where(['inst_user_relId'=>\Yii::$app->user->identity->id])->one()->inst_dep_relId;
            if ($key->dh_dept_relId == $instdept) {
               echo "<tr style='background-color:#DCE;margin-top:2px;'>
                    <td>".++$count."</td>
                    <td>{$key->dhDeptRel->dept_name}</td>
                    <td>{$key->dhInstRel->instUserRel->getFullName()}</td>
                    <td>{$key->dhDeptRel->dept_phone}</td>
                </tr>";
                continue;
            }
        }
        // $ss = $key->admission_status == 1?'Active':'Not Active';
        echo "<tr style='background-color:#ACE;margin-top:2px;'>
            <td>".++$count."</td>
            <td>{$key->dhDeptRel->dept_name}</td>
            <td>{$key->dhInstRel->instUserRel->getFullName()}</td>
            <td>{$key->dhDeptRel->dept_phone}</td>
        </tr>";
    }
    echo "</tbody></table>";
}else{
    echo "<p class='bg-danger'> No Department Available.</p>";
}

?>