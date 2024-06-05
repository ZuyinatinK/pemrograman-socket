<?php
// Mengaktifkan pelaporan kesalahan dan menetapkan batas waktu eksekusi skrip ke nol (tak terbatas).
error_reporting(1);
set_time_limit (0);

// Mengatur alamat IP dan port untuk server
$address = "0.0.0.0";
$port = 5000;
$max_clients = 5;

// Membuat socket 
if(!($sock = socket_create(AF_INET, SOCK_STREAM, 0)))
{   // Penanganan kesalahan jika socket tidak dapat dibuat
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Couldn't create socket: [$errorcode] $errormsg \n");
} 
echo "Socket created \n";

// Bind alamat sumber ke socket
if(!socket_bind($sock, $address, $port))
{   // Penanganan kesalahan jika socket tidak dapat di-bind
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Could not bind socket : [$errorcode] $errormsg \n");
} 
echo "Socket bind OK \n";

// Menunggu koneksi pada socket
if(!socket_listen ($sock, $max_clients))
{  // Penanganan kesalahan jika socket tidak dapat mendengarkan
    $errorcode = socket_last_error();
    $errormsg = socket_strerror($errorcode); 
    die("Could not listen on socket : [$errorcode] $errormsg \n");
}
echo "Socket listen OK \n";

echo "Waiting for incoming connections... \n";

// Array untuk menyimpan socket klien
$client_socks = array();

// Array untuk menyimpan socket yang dapat dibaca
$read = array();

// Memulai loop untuk mendengarkan koneksi masuk dan memproses koneksi yang ada
while (true)
{   // Mempersiapkan array socket yang dapat dibaca
    $read = array();

    // Socket pertama adalah socket utama
    $read[0] = $sock;

    // Menambahkan socket klien yang ada
    for ($i = 0; $i < $max_clients; $i++)
    {   if($client_socks[$i] != null)
        {	$read[$i+1] = $client_socks[$i];
        }
    }

    // Memanggil fungsi select() untuk array socket yang diberikan
    if(socket_select($read, $write, $except, null) === false)
    {	// Penanganan kesalahan jika select() gagal
        $errorcode = socket_last_error();
        $errormsg = socket_strerror($errorcode); 
        die("Could not listen on socket : [$errorcode] $errormsg \n");
    }

    // Jika socket utama siap, artinya ada koneksi baru yang masuk
    if (in_array($sock, $read))
    {   for ($i = 0; $i < $max_clients; $i++)
        {   if ($client_socks[$i] == null)
            {   // Menerima koneksi pada socket
                $client_socks[$i] = socket_accept($sock);

                // Menampilkan informasi tentang klien yang terhubung
                if(socket_getpeername($client_socks[$i], $address, $port))
                {	echo "Client $address : $port is now connected to Us. \n";
                }

                // Mengirim pesan selamat datang ke klien
                $message = "Welcome to php socket server version 1.0 \n";
                $message .= "Enter a message and press enter. I shall reply back \n";
                socket_write($client_socks[$i], $message);
                break;
            }
        }
    }

    // Mengirim pesan selamat datang ke klien
    for ($i = 0; $i < $max_clients; $i++)
    {	if (in_array($client_socks[$i], $read))
        {	// Membaca data dari socket klien
            $input = socket_read($client_socks[$i], 1024);

            if ($input == null)
            {	// Jika input kosong, berarti klien terputus, hapus dan tutup socket
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

            // Mengirim respons ke klien lain
            foreach (array_diff_key($client_socks, array($i => 0)) as $client_sock) {
                socket_write($client_sock, $output);
            }
        }
    }
}

?>