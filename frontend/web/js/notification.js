
$("#modalBotton").click(function(){
	$("#modal").modal("show")
	.find("#modalContent")
	.load($(this).attr('value'));
});

// $('#modal').on('hide.bs.modal',function(){
//    window.location.href = window.location;
// });

// var t = setTimeInterval(refreshNotification(),1000);
refreshNotification();
t = setInterval(refreshNotification, 1000000); // Change image every 6 seconds
// Auto Load any available notification for current user.

function refreshNotification(){
  $.post('/notification/get-notification').done(function(data){
  	$('#notifications_body').html(data);
    // $('.alerts-location').html(data);
    // http://rmsfront.local/NotificationController/get-notification
  });
}