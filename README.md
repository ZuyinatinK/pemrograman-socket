# Server-Client Communication using PHP Sockets

## Deskripsi

Program ini merupakan contoh sederhana komunikasi server-klien menggunakan soket (socket) dalam bahasa pemrograman PHP.

## Download dan Installasi

1. Download [VirtualBox 6.1](https://www.virtualbox.org/wiki/Download_Old_Builds_6_1) dan lakukan installasi pada windows.
2. Download [ISO Debian](https://cdimage.debian.org/debian-cd/current/amd64/iso-cd/) dan lakukan installasi pada VirtualBox
3. Download [XAMPP dengan PHP 5.6](https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/5.6.40/) dan lakukan installasi pada windows.

## Persyaratan

- Windows sebagai server dengan IP Address `192.168.10.1`
- Dua Debian server sebagai klien dengan IP Address:
  - Debian server: `192.168.10.13`
  - Debian server clone: `192.168.10.5`
- XAMPP dengan PHP versi 5.6

## Konfigurasi XAMPP

Setelah melakukan installasi XAMPP pada windows, langkah selanjutnya yakni melakukan beberapa konfigurasi.

1. XAMPP yang digunakan yakni:
   - XAMPP 5.6.40
   - PHP Version 5.6.40
2. Masuk ke dalam directory `C:\xampp\php\php.ini` pada file php.ini ubah pengaturan pada:
   ```ini
   ;extension=php_sockets.dll
   short_open_tag=Off
   ```
   menjadi:
   ```ini
   ;extension=php_sockets.dll
   short_open_tag=On
   ```
3. Restart Apache di XAMPP Control Panel

## Installasi dan Konfigurasi Debian Server

1. Pastikan telah melakukan installasi VirtualBox 6.1.
2. Atur IP Address untuk Client Windows/Linux Desktop:
   - File -> Host Network Manager
   - Buat/konfigurasi `vboxnet`
   - IP Address Client: `192.168.10.1`
   - Network Mask: `255.255.255.0`
3. Create Virtual Machine Debian Server
   - Pilih file iso Debian Server yang telah di download
   - Settings storage
   - Pastikan ada 2 Adapter di Debian Server:
     - Adapter 1 (NAT)
     - Adapter 2 (Host-only Adapter)
   - NAT digunakan agar Debian Server dapat terkoneksi dengan internet.
   - Host-only Adapter digunakan agar Debian Server dapat di-remote dari Client.
   - Lakukan installasi Debian server
4. Partisi Debian Server harus ada partisi / (ext4) dan swap (disarankan kapasitas <= 1Gb).
5. Login sebagai superuser/root:
   ```ini
   $ su
   Password:
   ```
6. Setting IP Address:
   - Setting static IP Address
   - Adapter 1 -> NAT (DHCP) -> `enp0s3`
   - Adapter 2 -> Host-only Adapter (Static IP) -> `enp0s8`
   - Untuk Debian Server yang pertama
     Ketik perintah:
     ```sh
     # nano /etc/network/interfaces
     ```
     Tambahkan code:
     ```sh
     auto lo
     iface lo inet loopback
     // starting up enp0s3 interface & dhcp
     allow-hotplug enp0s3
     iface enp0s3 inet dhcp
     // starting up enp0s8 interface & static ip
     allow-hotplug enp0s8
     iface enp0s8 inet static
         address 192.168.10.13
         netmask 255.255.255.0
     ```
   - Untuk Debian Server Clone
     Ketik perintah:
     ```sh
     # nano /etc/network/interfaces
     ```
     Tambahkan code:
     ```sh
     auto lo
     iface lo inet loopback
     // starting up enp0s3 interface & dhcp
     allow-hotplug enp0s3
     iface enp0s3 inet dhcp
     // starting up enp0s8 interface & static ip
     allow-hotplug enp0s8
     iface enp0s8 inet static
         address 192.168.10.5
         netmask 255.255.255.0
     ```
7. Jalankan:
   ```sh
   # /sbin/service networking restart
   ```
   Jika tidak berhasil, jalankan:
   ```sh
    # /sbin/ifdown enp0s3
    # /sbin/ifdown enp0s8
    # /sbin/ifup enp0s3
    # /sbin/ifup enp0s8
   ```
   Jika masih tidak berhasil, jalankan:
   ```sh
   # /sbin/reboot
   ```
   Selanjutnya cek IP Address:
   ```sh
   # ip address
   ```
   atau
   ```sh
   # ip a
   ```

## Menjalankan `socket.php`

1. Pastikan terkoneksi internet.
2. Jalankan melalui Command Prompt di Windows:
   ```sh
   C:\> xampp\php\php.exe xampp\htdocs\socket\socket.php
   ```

## Menjalankan `server.php`

1. Jalankan program
   ```sh
   C:\> xampp\php\php.exe xampp\htdocs\socket\server.php
   ```
   Output:
   ```ini
   Socket created
   Socket bind OK
   Socket listen OK
   Waiting for incoming connections...
   ```
2. Dari masing-masing Debian Server (client) dan Debian Server Clone, lakukan telnet ke port server:
   ```sh
   $ telnet 192.168.10.1 5000
   ```
   Output:
   ```ini
   Trying 192.168.10.1...
   Connected to 192.168.10.1.
   Escape character is '^]'.
   Welcome to php socket server version 1.0
   Enter a message and press enter. I shall reply back
   Hello
   Resource id #6 Said: ... Hai
   Are you free this weekend?
   Resource id #6 Said: ... Yes, I am. Why?
   I was thinking we could go hiking.
   ```
