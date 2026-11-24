<?php


?>

<div class="container-fluid">
	<div class="panel panel-default">
		<div class="panel-heading">
			<center>
				<img src="/uploads/<?= $model->photo_num ?>" alt="" class="img-circle" alt="Profile Image" height="100" width="80">
			</center>
		</div>
		<div class="panel-body">
			<pre>Full Name : <?= $model->first_name .' ' . $model->middle_name .' ' . $model->last_name ?></pre>
			<pre>Current Role : <?= $model->getUserRole($model->id) ?></pre>
			<pre>User Name : <?= $model->username ?></pre>
			<div class="container-fluid alert-warning">
				<button class="btn btn-danger btn-xs pull right" title="Reset password to default" onclick="reserPassword(<?= $model->id ?>)">Reset</button>
				<button class="btn btn-danger btn-xs pull right" title="Show OTP" onclick="showOtp(<?= $model->id ?>)">OTP</button>
			</div>
			<hr>
			<p style="display: none" id="passwordResetReport" onclick="showLog()"></p>
			<hr>
			<div class="container-fluid" id="showPasswordResetLog" style="display: none;">
				
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	window.showOtp = function(userId){
		$.post('/user/show-otp',{userId:userId}).done(function(data){
			$('#showPasswordResetLog').html(data).show();
		});
	}
	window.showLog = function(){
		dd = "";
		$.post('/user/show-password-reset-log').done(function(data){
			$('#showPasswordResetLog').html(data).show();
		});
	}
	window.reserPassword = function(userId){
		if (confirm('Are You sure to reset password?')) {
			$.post('/user/reset-password-wonde',{userId:userId}).done(function(data){
				$('#passwordResetReport').html(data).show();
			});
		}
	}
</script>