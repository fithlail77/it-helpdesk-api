PANDUAN TESTING API DENGAN POSTMAN
Base URL
http://localhost:8000/api

1. LOGIN
Request:

Method: POST
URL: http://localhost:8000/api/login
Body → raw → JSON:
{
    "email": "admin@itdesk.com",
    "password": "password123"
}
Response (200):

{
    "user": { ... },
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOi..."
}
⚡ Copy token dari response → simpan untuk request berikutnya!

2. REGISTER
Request:
Method: POST
URL: http://localhost:8000/api/register
Body → raw → JSON:
{
    "name": "Teknisi Baru",
    "email": "teknisi@itdesk.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "081234567899"
}

3. GET USER (Data Login)
Request:
Method: GET
URL: http://localhost:8000/api/user
Headers:
Authorization: Bearer {token}

4. LOGOUT
Request:
Method: POST
URL: http://localhost:8000/api/logout
Headers:
Authorization: Bearer {token}

5. DASHBOARD - Summary
Request:
Method: GET
URL: http://localhost:8000/api/dashboard/summary
Headers:
Authorization: Bearer {token}

6. DASHBOARD - Weekly
Request:
Method: GET
URL: http://localhost:8000/api/dashboard/weekly
Headers:
Authorization: Bearer {token}

7. DASHBOARD - Team Stats
Request:
Method: GET
URL: http://localhost:8000/api/dashboard/team-stats
Headers:
Authorization: Bearer {token}

8. ACTIVITIES - List Semua
Request:
Method: GET
URL: http://localhost:8000/api/activities
Headers:
Authorization: Bearer {token}
Dengan Filter:

GET /api/activities?status=pending&category=hardware&search=laptop

9. ACTIVITIES - Buat Baru
Request:
Method: POST
URL: http://localhost:8000/api/activities
Headers:
Authorization: Bearer {token}
Body → raw → JSON:
{
    "title": "Printer tidak bisa print",
    "description": "Printer di lantai 2 sudah tidak merespons sejak kemarin",
    "category": "hardware",
    "priority": "medium",
    "reporter_name": "Dina Marketing",
    "reporter_phone": "081234567800",
    "latitude": -6.2088,
    "longitude": 106.8456
}

10. ACTIVITIES - Detail
Request:
Method: GET
URL: http://localhost:8000/api/activities/{id}
Headers:
Authorization**: Bearer {token}`

11. ACTIVITIES - Update
Request:
Method: PUT
URL: http://localhost:8000/api/activities/{id}
Headers:
Authorization: Bearer {token}
Body → raw → JSON:
{
    "status": "in_progress",
    "assigned_to": "user-uuid-disini"
}

12. ACTIVITIES - Upload Foto
Request:
Method: POST
URL: http://localhost:8000/api/activities/{id}/photo
Headers:
Authorization: Bearer {token}
Body → form-data:
Key: photo → Type: File → Value: (pilih gambar)

13. ACTIVITIES - Hapus
Request:
Method: DELETE
URL: http://localhost:8000/api/activities/{id}
Headers:
Authorization: Bearer {token}

14. PROFILE - Lihat
Request:
Method: GET
URL: http://localhost:8000/api/profile
Headers:
Authorization: Bearer {token}

15. PROFILE - Update
Request:
Method: PUT
URL: http://localhost:8000/api/profile
Headers:
Authorization: Bearer {token}
Body → raw → JSON:
{
    "name": "Admin IT Updated",
    "phone": "081111222333"
}

16. PROFILE - Ubah Password
Request:
- Method: PUT
- URL: http://localhost:8000/api/profile/password
- Headers:
- Authorization: Bearer {token}
- Body → raw → JSON:
    {
        "current_password": "password123",
        "password": "newpassword123",
        "password_confirmation": "newpassword123"
    }

Cara Set Authorization di Postman
Untuk semua request yang butuh login:
1. Klik tab Headers
2. Tambah baris baru:
    - Key: Authorization
    - Value: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOi... (paste token dari login)

Cara Import ke Postman (Otomsatis)
Saya buatkan file collection yang bisa di-import langsung:

File collection sudah dibuat di:

"C:\Users\gnasution\Documents\New OpenCode Project\IT_Helpdesk_API.postman_collection.json"

Cara Import ke Postman:
1. Buka Postman
2. Klik Import ( tombol di kiri atas )
3. Klik File → pilih IT_Helpdesk_API.postman_collection.json
4. Klik Import

Cara Pakai:
1. Login dulu → Klik request Login → Klik Send
2. Token otomatis tersimpan ke variable token
3. Sekarang semua request lain sudah otomatis pakai token yang benar
4. Untuk testing Create Activity, ubah activityId di URL dari request Get Activity Detail dengan ID 
   yang didapat dari List Activities

Urutan Testing yang Disarankan:

No	Request	                Method	    Keterangan
1	Login	                POST	    Dapat token
2	Get User	            GET	        Cek data user
3	Dashboard Summary	    GET	        Cek ringkasan
4	Dashboard Weekly	    GET	        Cek grafik
5	Dashboard Team Stats	GET	        Cek statistik tim
6	List Activities	        GET	        Lihat semua tiket
7	Create Activity	        POST	    Buat tiket baru
8	Get Activity Detail	    GET	        Lihat detail tiket
9	Update Activity	        PUT	        Update status tiket
10	Get Profile	            GET	        Lihat profil
11	Update Profile	        PUT	        Edit profil
12	Change Password	        PUT	        Ubah password
13	Logout	                POST	    Keluar