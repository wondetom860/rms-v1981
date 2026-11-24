<?php

if (isset($report)) {
    echo "<table class='table table-condensed table-sm table-hover table-bordered'>";
    echo "<thead>
        <tr style='background-color: #FED;'>
            <th>#</th>
            <th>User Name</th>
            <th>Login Count</th>
        </tr>
    </thead><tbody";
    $count = 0;
    foreach ($report as $key => $value) {
        $det = $value['user_person_relId'] ? "<a href='/user/view?id={$value['user_person_relId']}' target='_blank'>{$value['username']}</a>" : $value['username'];
        echo "<tr>
                <td>" . ++$count . "</td>
                <td>$det</td>
                <td>{$value['login_count']}</td>
        </tr>";
    }
    echo "</tbody></table>";
}
