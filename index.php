<?php 
$colors = array('#007AFF','#FF7000','#FF7000','#15E25F','#CFC700','#CFC700','#CF1100','#CF00BE','#F00');
$color_pick = array_rand($colors);
?>

<!DOCTYPE html>
<html>
<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style type="text/css">
.chat-wrapper {
	font: bold 11px/normal 'lucida grande', tahoma, verdana, arial, sans-serif;
    background: #00a6bb;
    padding: 20px;
    margin: 20px auto;
    box-shadow: 2px 2px 2px 0px #00000017;
	max-width:700px;
	min-width:500px;
}
#message-box {
    width: 97%;
    display: inline-block;
    height: 300px;
    background: #fff;
    box-shadow: inset 0px 0px 2px #00000017;
    overflow: auto;
    padding: 10px;
}
.user-panel{
    margin-top: 10px;
}
input[type=text]{
    border: none;
    padding: 5px 5px;
    box-shadow: 2px 2px 2px #0000001c;
}
input[type=text]#name{
    width:20%;
}
input[type=text]#message{
    width:60%;
}
button#send-message {
    border: none;
    padding: 5px 15px;
    background: #11e0fb;
    box-shadow: 2px 2px 2px #0000001c;
}
a{

	cursor:pointer;
}
</style>
</head>
<body>

<div class="chat-wrapper">


	<table style="width:100%">
	<tr>
	<td style="width:20%">
	<div style=" height: 300px; background: #fff;width:100%">
	<p id="loggedUser">
	</p>
	</div>
	</td>

	<td style="width:80%">
		<div id="message-box"></div>
			<div class="user-panel" >

				<input type="text" name="name" id="name" placeholder="Your Name" maxlength="15" />
				<input type="text" name="message" id="message" placeholder="Type your message here..." maxlength="100" />
				<button id="send-message">Send</button>
			</div>
	</td>

	</tr>
	</table>
	
	
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<script language="javascript" type="text/javascript">  
	//create a new WebSocket object.
	var msgBox = $('#message-box');
	var wsUri = "ws://localhost:9000/demo/server.php"; 	
	websocket = new WebSocket(wsUri); 
	
	websocket.onopen = function(ev) { // connection is open 
		msgBox.append('<div class="system_msg" style="color:#bbbbbb">Welcome to my "Bro4U Chat box"!</div>'); //notify user
	}
	// Message received from server
	var selectedUser="";
	var singleQoute='"';
	var loggedInUserList=[];
	websocket.onmessage = function(ev) {
		var response 		= JSON.parse(ev.data); //PHP sends Json data
		
		var res_type 		= response.type; //message type
		var user_message 	= response.message; //message text
		var user_name 		= response.name; //user name
		var user_color 		= response.color; //color
		var curDate=response.date;
		var currentUser=$("#name").val();
		if(user_name!=currentUser && user_name && loggedInUserList.indexOf(user_name)<0) 
		{
			
			loggedInUserList.push(user_name)
			var fun="selectUser('"+user_name+"',this)";
			
			
			var data='<h4><a class="loggedInUser" style="color:blue;" onclick="'+fun+'">User:'+user_name+'</a></h4>';
			$("#loggedUser").append(data);
		}
		
	
		if(selectedUser==user_name)
		{
			
		switch(res_type){
			case 'usermsg':
				msgBox.append('<div><span class="user_name" style="color:green">' + user_name + '</span> : <span class="user_message">' + user_message + '</span></div>');
				msgBox.append("<div style='text-align:left;display:block;font-size:8px'>" +  curDate + "</div>");
				break;
			case 'system':
				msgBox.append("<div style='color:#bbbbbb'>" + user_message + "</div>");
				msgBox.append("<div style='text-align:right;display:block;font-size:8px'>" +  curDate + "</div>");
				break;
		}
		msgBox[0].scrollTop = msgBox[0].scrollHeight; //scroll message 
		}

	};
	
	websocket.onerror	= function(ev){ msgBox.append('<div class="system_error">Error Occurred - ' + ev.data + '</div>'); }; 
	websocket.onclose 	= function(ev){ msgBox.append('<div class="system_msg">Connection Closed</div>'); }; 

	//Message send button
	$('#send-message').click(function(){
		send_message();
	});
	
	//User hits enter key 
	$( "#message" ).on( "keydown", function( event ) {
	  if(event.which==13){
		  send_message();
	  }
	});
	
	//select user to chat
	function selectUser(username,index) {
		$(".loggedInUser").css('color','blue');
		$(index).css('color','green');
		selectedUser=username;
		msgBox.html("");
		
	}
	//Send message
	function send_message(){
		var message_input = $('#message'); //user message text
		var d = new Date();
		var curDate=d.getDate()+"-"+(d.getMonth()+1)+"-"+d.getFullYear()+" "+d.getHours()+":"+d.getMinutes()+":"+d.getSeconds();
		var name_input = $('#name'); //user name
		msgBox.append("<div style='text-align:right;display:block;'>" +  message_input.val() + ":<b style='color:blue;'>YOU</b></div>");
		msgBox.append("<div style='text-align:right;display:block;font-size:8px'>" +  curDate + "</div>");
		if(message_input.val() == ""){ //empty name?
			alert("Enter your Name please!");
			return;
		}
		if(message_input.val() == ""){ //emtpy message?
			alert("Enter Some message Please!");
			return;
		}
		



		//prepare json data
		var msg = {
			message: message_input.val(),
			date:curDate,
			name: name_input.val(),
			color : '<?php echo $colors[$color_pick]; ?>'
		};
		//convert and send data to server
		websocket.send(JSON.stringify(msg));	
		message_input.val(''); //reset message input
	}
</script>
</body>
</html>
