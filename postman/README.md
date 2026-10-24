# PlaceLog API Collection

Kumpulan request Postman untuk menguji REST API pada aplikasi PlaceLog.

## Isi Collection

### Authentication
- Register User
- Login User
- Get Authenticated User
- Logout User

### Places
- Get All Places
- Create Place
- Get Place by ID
- Update Place
- Delete Place
- Create Place - Validation Error

## Konfigurasi

1. Import file `PlaceLog API.postman_collection.json` ke Postman.
2. Buat environment bernama `PlaceLog Local`.
3. Tambahkan variable berikut:

| Variable | Initial Value |
|---|---|
| `base_url` | `http://127.0.0.1:8000/api` |
| `bearer_token` |  |

4. Jalankan backend Laravel secara lokal.
5. Jalankan request **Login User** untuk mendapatkan token autentikasi.
6. Isi variable `bearer_token` dengan token hasil login jika diperlukan oleh request yang dilindungi autentikasi.

## Pengujian

Collection ini digunakan untuk menguji:
- Registrasi dan autentikasi pengguna.
- Operasi CRUD data tempat.
- Validasi input yang menghasilkan HTTP 422.


