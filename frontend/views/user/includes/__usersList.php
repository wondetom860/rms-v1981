<?php

$empId = isset($empId) && !empty($empId) ? intval($empId) : NULL;

$users = \backend\models\User::find()->where(['id' => $empId])->all();
// ->innerJoin('person','person.id = user.user_person_relId');
// if ($empId) {
// 	$users = $users->where(['user_person_relId' => $empId]);
// }
// $users = $users->orderBy('first_name')->all();

if ($users) {
	echo "<table class='table table-condensed table-bordered table-striped' style='word-wrap: break-word;'>";

	echo "<caption style='color:#000; font-weight:bold;background-color:#F6FDFD;'><center>
			<h3>Users with Login credentials</h3>
		</center></caption>";
	echo "<thead style='background-color:#8DF;'>
			<th>#</th>
			<th>Full Name</th>
			<th>User Name</th>
			<th>Account Status</th>
			<th>Role</th>
			<th>Activation Option</th>
		</thead><tbody>";
	$count = 0;
	foreach ($users as $user) {
		$fullName = $user->getFullName();
		// if (!is_null($user->user_person_relId)) {
		// 	$person = $user->userPersonRel;
		// 	$fullName = $person->;
		// }
		$pwdchange = "No Chnaged Yet!";
		$accountStatus  = $user->activated == $user::ACTIVE_ACCOUNT ? 'Activated' : 'Not Activated';

		$btn = $user->activated == $user::ACTIVE_ACCOUNT ? "<button class='btn btn-warning btn-xs' onclick='alterAccountStatus({$user->id});return false;'>De-activate</button>" : "<button class='btn btn-success btn-xs' onclick='alterAccountStatus({$user->id});return false;'>Activate</button>";

		// if (!is_null($user->password_changed)) {
		// 	$user1 = $user->passwordChangedBy->username;
		// 	$user1 = $user1 == $user->username ? 'Self' : $user1;
		// 	$dat = date_format(date_create($user->password_changed_at), 'd M, Y');
		// 	$pwdchange = "Last Password change was made on {$dat} by {$user1}.";
		// }

		$rle = $user->getUserRole2($user->id);

		echo "<tr>
				<td>" . ++$count . "</td>
				<td>{$fullName}</td>
				<td>{$user->username}</td>
				<td>{$accountStatus}</td>
				<td>{$rle}</td>
				<td>{$btn}</td>
			</tr>";
	}
	echo "</tbody></table>";
}

?>

<script type="text/javascript">
	window.alterAccountStatus = (userId) => {
		if (confirm('Are you sure to change status?')) {
			$.post('/user/alter-user-account-activation-status', {
				userId: userId
			}).done((response) => {
				alert(response);
			});
		}
	}
</script>