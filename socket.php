<?php
// Creating a socket
if(!($sock = socket_create(AF_INET,SOCK_STREAM,0)))
{	// Error handling
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);    
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
echo "Socket created \n----------------------\n";

// Get the IP address for the target host from internet 
$address = gethostbyname('www.google.com');
//$address = '127.0.0.1';

// Connect to a Server
if(!socket_connect($sock,$address,80))
{   // Error handling
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);     
    die("Could not connect: [$errorcode] $errormsg \n");
} 
echo "Connection established \n----------------------\n";

//Send the message to the server
//The message is actually a http command to fetch the mainpage of a website
$message = "GET / HTTP/1.1\r\n\r\n";
if(!socket_send($sock,$message,strlen($message),0))
{   // Error handling
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Could not send data: [$errorcode] $errormsg \n");
} 
echo "Message send successfully \n----------------------\n";

//Now receive reply from server
if(socket_recv($sock,$buf,2045,MSG_WAITALL) === FALSE)
{   // Error handling
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);     
    die("Could not receive data: [$errorcode] $errormsg \n");
} 
echo $buf."\n----------------------\n";

// Close socket
socket_close($sock);

?>