@echo off
cd /d G:\Project\asset-management

:: Buat key
openssl genrsa -out certs\key.pem 2048

:: Buat cert
openssl req -new -key certs\key.pem -out certs\csr.pem -subj "//CN=10.42.1.15"

:: Self-sign
openssl x509 -req -days 365 -in certs\csr.pem -signkey certs\key.pem -out certs\cert.pem

echo Done!
pause