<?php
// Membuat socket
if(!($sock = socket_create(AF_INET,SOCK_STREAM,0)))
{	// Penanganan kesalahan jika socket tidak dapat dibuat
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);    
    die("Couldn't create socket: [$errorcode] $errormsg \n");
}
echo "Socket created \n----------------------\n";

// Mendapatkan alamat IP untuk host target dari internet
$address = gethostbyname('www.google.com');
//$address = '127.0.0.1'; // Alternatif, menggunakan alamat lokal

// Menghubungkan ke Server
if(!socket_connect($sock,$address,80))
{   // Penanganan kesalahan jika socket tidak dapat terhubung
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);     
    die("Could not connect: [$errorcode] $errormsg \n");
} 
echo "Connection established \n----------------------\n";

// Mengirim pesan ke server
// Pesan sebenarnya adalah perintah HTTP untuk mengambil halaman utama dari sebuah situs web
$message = "GET / HTTP/1.1\r\n\r\n";
if(!socket_send($sock,$message,strlen($message),0))
{   // Penanganan kesalahan jika data tidak dapat dikirim
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);
    die("Could not send data: [$errorcode] $errormsg \n");
} 
echo "Message send successfully \n----------------------\n";

// Menerima balasan dari server
if(socket_recv($sock,$buf,2045,MSG_WAITALL) === FALSE)
{   // Penanganan kesalahan jika data tidak dapat diterima
	$errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode);     
    die("Could not receive data: [$errorcode] $errormsg \n");
} 
echo $buf."\n----------------------\n";

// Menutup socket
socket_close($sock);

?>