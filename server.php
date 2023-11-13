<?php
error_reporting(1);
set_time_limit (0);

$address = "0.0.0.0";
$port = 5000;
$max_clients = 5;

// Creating a socket 
if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{   // Error handling
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Couldn't create socket: [$errorcode] $errormsg \n");
} 
echo "Socket created \n";

// Bind the source address
if(!socket_bind($sock, $address, $port))
{   // Error handling
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Could not bind socket : [$errorcode] $errormsg \n");
} 
echo "Socket bind OK \n";

// Listens for a connection on a socket
if(!socket_listen ($sock, $max_clients))
{   // Error handling
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Could not listen on socket : [$errorcode] $errormsg \n");
}
echo "Socket listen OK \n";

echo "Waiting for incoming connections... \n";

//array of client sockets
$client_socks = array();

//array of sockets to read
$read = array();

//start loop to listen for incoming connections and process existing connections
while (true)
{   //prepare array of readable client sockets
    $read = array();

    //first socket is the master socket
    $read[0] = $sock;

    //now add the existing client sockets
    for ($i = 0; $i < $max_clients; $i++)
    {   if($client_socks[$i] != null)
        {	$read[$i+1] = $client_socks[$i];
        }
    }

    //Runs the select() system call on the given arrays of sockets
    if(socket_select($read, $write, $except, null) === false)
    {	// Error handling
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode); 
        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }

    //if ready contains the master socket, then a new connection has come in
    if (in_array($sock, $read))
    {   for ($i = 0; $i < $max_clients; $i++)
        {   if ($client_socks[$i] == null)
            {   //Accepts a connection on a socket
                $client_socks[$i] = socket_accept($sock);

                //display information about the client who is connected
                if(socket_getpeername($client_socks[$i], $address, $port))
                {	echo "Client $address : $port is now connected to Us. \n";
                }

                //Send Welcome message to client
                $message = "Welcome to php socket server version 1.0 \n";
                $message .= "Enter a message and press enter. I shall reply back \n";
                socket_write($client_socks[$i], $message);
                break;
            }
        }
    }

    //check each client if they send any data
    for ($i = 0; $i < $max_clients; $i++)
    {	if (in_array($client_socks[$i], $read))
        {	//Reads a maximum of length bytes from a socket
            $input = socket_read($client_socks[$i], 1024);

            if ($input == null)
            {	//zero length string meaning disconnected, remove and close the socket
                // remove the socket
                unset($client_socks[$i]);
                // close the socket
                socket_close($client_socks[$i]);
            }

            $n = trim($input); 
            $output = $client_socks[$i]." Said: ... $input"; 
            echo "Sending output to client \n";

            //send response to client
            //socket_write($client_socks[$i], $output);

            // send response to other client
            foreach (array_diff_key($client_socks, array($i => 0)) as $client_sock) {
                socket_write($client_sock, $output);
            }
        }
    }
}

?>