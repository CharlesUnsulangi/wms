


# Workflow Aplikasi HRD

## 1. Gambaran Umum
Aplikasi HRD terdiri dari dua aplikasi utama:
- **hrdmguest**: Untuk pelamar, berisi fitur pengisian data, konfirmasi interview, self check-in (QR), dan pelengkapan data diri.
- **hrdmadmin**: Untuk user/operator/admin HRD, berisi fitur manajemen seluruh proses HRD (rekrutmen, kontrak, payroll, absensi, assessment, berita acara, monitoring).

## 2. Struktur Repository GitHub
- `hrdmadmin`: aplikasi admin HRD
- `hrdmguest`: aplikasi guest/pelamar
> Akses kode dan kolaborasi langsung di GitHub melalui link repository.

## 3. Modul Utama & Alur Bisnis

0. **Manajemen User**
    - Pengelolaan akun, peran (role), hak akses, audit trail, dan histori aktivitas user/operator/admin.
1. **Manajemen Pelamar**
    - 1. **Memasukkan Data Pelamar Baru**: User HRD menginput data pelamar ke sistem, baik dengan mengisi manual atau menyalin dari portal/email eksternal.
    - 2. **Mengirim Pesan WhatsApp ke Pelamar**: Setelah data pelamar masuk, sistem dapat mengirim pesan WA otomatis ke nomor pelamar untuk instruksi kelengkapan data dan konfirmasi interview.
    - 3. **Mengundang Interview (Penjadwalan)**: User HRD menentukan tanggal interview dan mengundang pelamar sesuai jadwal yang dipilih. Link konfirmasi interview dikirim via WA.
    - 4. **Memeriksa Skedul Interview & Konfirmasi**: HRD dapat melihat daftar jadwal interview, status konfirmasi dari pelamar, dan melakukan monitoring siapa saja yang sudah/akan hadir.
    - 5. **(Opsional) Proses Lanjutan**: Setelah interview, HRD dapat melanjutkan proses ke register karyawan atau menandai status pelamar sesuai hasil interview.
2. **Manajemen Interview**
    - Terdapat dashboard interview yang menampilkan daftar pelamar beserta status konfirmasi kehadiran interview.
    - HRD dapat langsung melihat siapa saja pelamar yang sudah konfirmasi hadir, belum konfirmasi, atau membatalkan.
    - Dashboard ini otomatis membentuk jadwal interview berdasarkan data konfirmasi, sehingga HRD dapat mengatur urutan, waktu, dan interviewer yang bertugas.
    - Terdapat statistik per user HRD: berapa pelamar yang berhasil diinput oleh masing-masing user (user leaderboard), sehingga dapat memantau kontribusi tiap user dalam proses rekrutmen.
    - Penjadwalan interview, konfirmasi online/offline, check-in QR, multi-user real-time notes, hasil interview sebagai dasar keputusan.
3. **Manajemen Register Karyawan**
    - Usulan kandidat, syarat kelengkapan data, tanda tangan kontrak, approval berjenjang (admin, finance, manager), perubahan status ke karyawan.
4. **Manajemen PKWT/PKWTT**
    - Pencatatan kontrak kerja karyawan & mitra (driver/kenek/outsourcing), masa berlaku, dokumen pendukung, reminder perpanjangan.
5. **Manajemen Payroll**
    - Pengelolaan komponen gaji (upah pokok, tunjangan, bonus, komisi), olah data absensi, hasil payroll berupa tabel pembayaran, proses approval & pembayaran, slip gaji, pencatatan status & bukti pembayaran (notifikasi/slip/berita acara).
6. **Manajemen Absensi & Cuti**
    - Absensi harian, pengajuan cuti, verifikasi & pengelolaan data absensi/cuti oleh HRD.
7. **Manajemen Assessment**
    - Assessment terdiri dari beberapa bagian:
        - Self assessment (diisi oleh karyawan sendiri)
        - Dinilai oleh atasan langsung
        - Dinilai oleh board director
        - Dinilai oleh HRD
        - Dinilai oleh bawahan (jika ada bawahan)
    - Penilaian kinerja dilakukan secara periodik dan terstruktur dari berbagai perspektif (multi-rater), hasil digunakan untuk pengembangan karir & evaluasi.
8. **Manajemen Berita Acara**
    - Daftar semua informasi penting (pelanggaran, anomali, penghargaan, dsb), table master jenis berita acara untuk klasifikasi, dokumentasi & akses sesuai kebutuhan.

## 4. Diagram Alur Sederhana
Pelamar → [Seleksi Administrasi] → Interview → [Diterima] → Register Karyawan → PKWT/PKWTT → Absensi/Cuti → Payroll → Assessment → Berita Acara

## 5. Catatan Teknis & Standar
- Laravel 11, Laravel Breeze (Livewire + Alpine), Pest/PHPUnit 11.
- Standar keamanan, code style, workflow Git, dependency management, dan protokol interaksi AI sudah dijabarkan detail di bawah bagian workflow.

### Contoh Konfigurasi Database (SQL Server)

Tambahkan konfigurasi berikut ke file `.env` (jangan commit password ke repository!):

```
DB_CONNECTION=sqlsrv
DB_HOST=66.96.240.131,26402
DB_DATABASE=RCM_DEV_HGS_SB
DB_USERNAME=sa
DB_PASSWORD=pfind@sqlserver
```

> **Keamanan:**
> Jangan pernah commit file .env yang berisi password ke repository GitHub. Gunakan .env.example tanpa password untuk contoh.

### Contoh Struktur Tabel Interview (tr_hr_pelamar_interview)

| Kolom                | Tipe         | Nullable   | Keterangan                       |
|----------------------|--------------|------------|-----------------------------------|
| tr_hr_pelamar_interview_id | int          | No         | Primary key                      |
| tr_hr_pelamar_id     | varchar(50)  | Yes        | Foreign key ke pelamar           |
| date_interview       | date         | Yes        | Tanggal interview                |
| time_start           | time(7)      | Yes        | Waktu mulai                      |
| time_end             | time(7)      | Yes        | Waktu selesai                    |
| note_operator        | varchar(50)  | Yes        | Catatan operator                 |
| note_spv             | varchar(50)  | Yes        | Catatan supervisor               |
| note_mgr             | varchar(50)  | Yes        | Catatan manager                  |
| note_hrd             | varchar(50)  | Yes        | Catatan HRD                      |
| note_bd              | varchar(50)  | Yes        | Catatan board director           |
| note_gm              | varchar(50)  | Yes        | Catatan general manager          |
| note_dir             | varchar(50)  | Yes        | Catatan direktur                 |
| note_mgt             | varchar(50)  | Yes        | Catatan manajemen                |
| rating_operator      | int          | Yes        | Rating operator                  |
| rating_spv           | int          | Yes        | Rating supervisor                |
| rating_mgr           | int          | Yes        | Rating manager                   |
| rating_gm            | int          | Yes        | Rating general manager           |
| rating_bd            | int          | Yes        | Rating board director            |
| rating_mgt           | int          | Yes        | Rating manajemen                 |
| rating_hrd           | int          | Yes        | Rating HRD                       |
| cek_lanjut           | bit          | Yes        | Flag lanjut proses                |
| cek_tolak            | bit          | Yes        | Flag tolak proses                 |

### Contoh Struktur Tabel Pengalaman Perusahaan (tr_hr_pelamar_pengalaman_perusahaan)

| Kolom                      | Tipe         | Nullable   | Keterangan                                    |
|----------------------------|--------------|------------|------------------------------------------------|
| tr_hr_pelamar_pengalaman_id| int (IDENTITY) | No       | Primary key, auto increment                   |
| tr_hr_pelamar_id           | varchar(50)  | No         | Foreign key ke pelamar                        |
| perusahaan                 | varchar(50)  | No         | Nama perusahaan                               |
| tgl_start                  | date         | No         | Tanggal mulai bekerja                         |
| tgl_end                    | date         | No         | Tanggal selesai bekerja                       |
| hp_hrd                     | varchar(50)  | No         | Nomor HP HRD perusahaan                       |
| nama_hrd                   | varchar(50)  | No         | Nama HRD perusahaan                           |
| hp_atasan                  | varchar(50)  | No         | Nomor HP atasan langsung                      |
| alasan_resign              | text         | No         | Alasan resign dari perusahaan                  |
| jabatan_akhir              | varchar(50)  | Yes        | Jabatan terakhir di perusahaan                 |
| jabatan_awal               | varchar(50)  | Yes        | Jabatan awal di perusahaan                     |
| gaji_awal                  | money        | No         | Gaji awal saat masuk perusahaan                |
| gaji_akhir                 | money        | No         | Gaji akhir saat keluar perusahaan              |
| sukses_rating              | int          | Yes        | Rating keberhasilan (opsional)                 |
| sukses_keterangan          | text         | Yes        | Keterangan keberhasilan (opsional)             |
| sulit_rating               | int          | Yes        | Rating kesulitan (opsional)                    |
| sulit_keterangan           | text         | Yes        | Keterangan kesulitan (opsional)                |
| puas_rating                | int          | Yes        | Rating kepuasan (opsional)                     |
| puas_keterangan            | text         | Yes        | Keterangan kepuasan (opsional)                 |
| masalah_rating             | int          | Yes        | Rating masalah (opsional)                      |
| masalah_keterangan         | text         | Yes        | Keterangan masalah (opsional)                  |
| kesalahan_paling_besar     | text         | Yes        | Catatan kesalahan paling besar (opsional)      |

### Contoh Struktur Tabel Skedul Pelamar (tr_hr_pelamar_skedul)

| Kolom                | Tipe         | Nullable   | Keterangan                                 |
|----------------------|--------------|------------|---------------------------------------------|
| tr_hr_pelamar_id     | varchar(50)  | No         | Primary key, foreign key ke pelamar         |
| skedul_pelamar_time  | datetime     | Yes        | Waktu skedul interview pelamar (opsional)   |
| skedul_confirmed     | datetime     | Yes        | Waktu konfirmasi skedul oleh pelamar (opsional) |

### Contoh Struktur Tabel Sosial Media Pelamar (tr_hr_pelamar_sosmed)

| Kolom                | Tipe            | Nullable   | Keterangan                                      |
|----------------------|-----------------|------------|-------------------------------------------------|
| tr_hr_pelamar_sosmed | int             | No         | Primary key, ID sosmed pelamar                   |
| sosmed_link          | nvarchar(max)   | Yes        | Link ke profil sosial media pelamar (opsional)   |
| tr_hr_pelamar_id     | varchar(50)     | Yes        | Foreign key ke pelamar (opsional)                |
| sosmed_user          | varchar(50)     | Yes        | Username/ID akun sosial media (opsional)         |
| sosmed_type          | varchar(50)     | Yes        | Jenis sosial media (misal: Facebook, IG, dsb)    |
| date_created         | date            | Yes        | Tanggal data dibuat (opsional)                   |
| user_created         | varchar(50)     | Yes        | User yang membuat data (opsional)                |

### Contoh Struktur Tabel Pelamar Driver (tr_hr_pelamar_driver)

| Kolom                   | Tipe         | Nullable   | Keterangan                                      |
|-------------------------|--------------|------------|-------------------------------------------------|
| tr_pelamar_driver_id    | varchar(50)  | No         | Primary key, ID pelamar driver                  |
| nama                    | varchar(50)  | Yes        | Nama pelamar                                    |
| nama_keluarga           | varchar(50)  | Yes        | Nama keluarga                                   |
| email                   | varchar(50)  | Yes        | Email                                           |
| hp                      | varchar(50)  | Yes        | Nomor HP                                        |
| no_sim                  | varchar(50)  | Yes        | Nomor SIM                                       |
| jenis_sim               | text         | Yes        | Jenis SIM (opsional, bisa A/B/C/dll)            |
| tanggal_lahir           | tinyint      | Yes        | Tanggal lahir (opsional, kemungkinan tahun saja) |
| kota_lahir              | varchar(50)  | Yes        | Kota lahir                                      |
| agama                   | varchar(50)  | Yes        | Agama                                           |
| alamat                  | varchar(50)  | Yes        | Alamat                                          |
| pekerjaan_sebelumnya    | varchar(50)  | Yes        | Pekerjaan sebelumnya                            |
| kapan_terakhir_bekerja  | date         | Yes        | Kapan terakhir bekerja (opsional)               |
| alasan_keluar           | text         | Yes        | Alasan keluar dari pekerjaan sebelumnya         |
| tahu_lamaran_dari       | varchar(50)  | Yes        | Sumber info lamaran (teman, iklan, dsb)         |
| kenal_siapa             | varchar(50)  | Yes        | Kenal siapa di perusahaan (opsional)            |

### Contoh Struktur Tabel Master Form HRD (ms_hr_from)

| Kolom           | Tipe         | Nullable   | Keterangan                                 |
|-----------------|--------------|------------|---------------------------------------------|
| ms_hr_from_id   | varchar(50)  | No         | Primary key, ID form HRD                   |
| form_hr_desc    | varchar(50)  | Yes        | Deskripsi form HRD (opsional)               |


### Contoh Struktur Tabel Kandidat Karyawan (ms_hr_kandidat)

| Kolom                  | Tipe         | Nullable   | Keterangan                                                      |
|------------------------|--------------|------------|-----------------------------------------------------------------|
| ms_hr_kandidat_emp_id  | varchar(50)  | No         | Primary key, ID kandidat (relasi ke karyawan/pelamar)           |
| ms_status_id           | varchar(50)  | Yes        | Status kandidat (proses, approved, rejected, dsb)               |
| ms_user_id             | varchar(50)  | Yes        | User yang menginput/menangani kandidat                          |
| date_kandidat          | date         | Yes        | Tanggal kandidat diusulkan                                      |
| date_emp               | date         | Yes        | Tanggal kandidat menjadi karyawan (jika lolos)                  |
| date_hrd_approve       | date         | Yes        | Tanggal approval HRD                                            |
| date_finance_approve   | date         | Yes        | Tanggal approval Finance                                        |
| date_bod_approve       | date         | Yes        | Tanggal approval Board of Director                              |
| rating_hrd             | int          | Yes        | Rating/penilaian HRD terhadap kandidat                          |
| rating_finance         | int          | Yes        | Rating/penilaian Finance terhadap kandidat                      |
| rating_bod             | int          | Yes        | Rating/penilaian Board of Director terhadap kandidat            |
| rating_spv             | int          | Yes        | Rating/penilaian Supervisor terhadap kandidat                   |
| date_spv               | date         | Yes        | Tanggal penilaian/approval Supervisor                           |


### Contoh Struktur Tabel WhatsApp Message (tr_hr_wa_msg_id)

| Kolom            | Tipe           | Nullable   | Keterangan                                              |
|------------------|----------------|------------|---------------------------------------------------------|
| tr_hr_wa_msg_id  | int            | No         | Primary key, ID pesan WhatsApp                          |
| email            | varchar(50)    | Yes        | Email penerima (opsional)                               |
| ms_hr_pelamar_id | varchar(50)    | Yes        | ID pelamar terkait (opsional)                           |
| hp               | varchar(50)    | Yes        | Nomor HP penerima (opsional)                            |
| link             | nvarchar(max)  | Yes        | Link yang dikirim (misal: onboarding/interview)         |
| msg              | text           | Yes        | Isi pesan WhatsApp                                      |
| ms_hr_wa_msg_id  | int            | Yes        | Relasi ke master template pesan (opsional)              |
| time_sent        | time(7)        | Yes        | Waktu pesan dikirim (opsional)                          |


### Contoh Struktur Tabel Master WhatsApp Message (ms_hr_wa_msg)

| Kolom            | Tipe        | Nullable   | Keterangan                                 |
|------------------|-------------|------------|--------------------------------------------|
| ms_hr_wa_msg_id  | int         | No         | Primary key, ID master template pesan WA   |
| msg_desc         | text        | Yes        | Deskripsi/isi template pesan WhatsApp      |






### Contoh Struktur Tabel User HRD (ms_hr_user)

| Kolom            | Tipe         | Nullable | Keterangan |
|------------------|--------------|----------|------------|
| ms_hr_user_id    | varchar(50)  | No       | Primary key, ID user HRD |
| password         | varchar(50)  | Yes      | Password user (hash/encrypted) |
| email            | varchar(50)  | Yes      | Email user |
| cek_aktif        | bit          | Yes      | Status aktif (opsional) |
| ms_user_level_id | varchar(50)  | Yes      | ID level user (opsional) |
| cek_inaktif      | bit          | Yes      | Status inaktif (opsional) |
| sync_emp         | bit          | Yes      | Sinkronisasi dengan employee (opsional) |
| sync_exp_date    | date         | Yes      | Tanggal sync expired (opsional) |
| exp_date         | date         | Yes      | Tanggal expired user (opsional) |

| Kolom                          | Tipe         | Nullable | Keterangan |
|--------------------------------|--------------|----------|------------|
| rec_usercreated                | varchar(50)  | No       | User yang membuat data |
| rec_userupdate                 | varchar(50)  | No       | User yang mengupdate data |
| rec_datecreated                | datetime     | No       | Tanggal data dibuat |
| rec_dateupdate                 | datetime     | No       | Tanggal data diupdate |
| rec_status                     | char(1)      | No       | Status record (aktif/nonaktif) |
| rec_comcode                    | varchar(50)  | No       | Kode perusahaan |
| rec_areacode                   | varchar(50)  | No       | Kode area |
| driver_helper_code_h           | varchar(50)  | No       | Kode header fee driver/helper |
| emp_code                       | varchar(50)  | No       | Kode karyawan driver/helper |
| driver_helper_desc             | varchar(50)  | Yes      | Deskripsi fee driver/helper (opsional) |
| driver_helper_dateregistration | datetime     | Yes      | Tanggal registrasi (opsional) |
| driver_helper_transmaincoa     | varchar(50)  | Yes      | Kode COA transaksi utama (opsional) |
| driver_helper_mode             | varchar(50)  | Yes      | Mode fee (opsional) |
| driver_helper_total_value_out  | varchar(50)  | Yes      | Total value out (opsional) |
| driver_helper_totalprice       | money        | Yes      | Total harga/fee (opsional) |

| Kolom                        | Tipe         | Nullable | Keterangan |
|------------------------------|--------------|----------|------------|
| rec_comcode                  | varchar(50)  | No       | Kode perusahaan |
| rec_areacode                 | varchar(50)  | No       | Kode area |
| drvhlprfee_code_h            | varchar(50)  | No       | Kode header fee driver/helper |
| drvhlprfee_code_d            | varchar(50)  | No       | Kode detail fee driver/helper |
| drvhlprfee_no                | int          | No       | Nomor urut detail fee |
| drvhlprfee_Value_fee         | money        | Yes      | Nilai fee (opsional) |
| drvhlprfee_emp_code          | varchar(50)  | Yes      | Kode karyawan driver/helper (opsional) |
| drvhlprfee_Value_potongan    | money        | Yes      | Nilai potongan (opsional) |
| drvhlprfee_Total_value       | money        | Yes      | Total nilai setelah potongan (opsional) |
| drvhlprfee_outstanding_pinjaman | money    | Yes      | Outstanding pinjaman (opsional) |
| drvhlprfee_status            | varchar(50)  | Yes      | Status fee (opsional) |
| drvhlprfee_date              | date         | Yes      | Tanggal fee (opsional) |
| drvhlprfee_codepayroll       | varchar(50)  | Yes      | Kode payroll (opsional) |

| Kolom             | Tipe           | Nullable | Keterangan |
|-------------------|----------------|----------|------------|
| rec_usercreated   | varchar(50)    | No       | User yang membuat data |
| rec_userupdate    | varchar(50)    | No       | User yang mengupdate data |
| rec_datecreated   | datetime       | No       | Tanggal data dibuat |
| rec_dateupdate    | datetime       | No       | Tanggal data diupdate |
| rec_status        | char(1)        | No       | Status record (aktif/nonaktif) |
| Drv_Id            | varchar(50)    | No       | Primary key, ID driver |
| Drv_FistName      | varchar(100)   | Yes      | Nama depan driver |
| Drv_LastName      | varchar(100)   | Yes      | Nama belakang driver |
| Drv_Addrase       | varchar(200)   | Yes      | Alamat driver |
| Drv_BPlace        | varchar(100)   | Yes      | Tempat lahir driver |
| Drv_Bdate         | date           | Yes      | Tanggal lahir driver |
| Drv_StartDate     | date           | Yes      | Tanggal mulai kerja |
| Drv_EndDate       | date           | Yes      | Tanggal selesai kerja |
| Drv_Phone         | varchar(20)    | Yes      | Nomor telepon rumah |
| Drv_CellPhone     | varchar(50)    | Yes      | Nomor HP |
| Drv_License       | varchar(50)    | Yes      | Nomor SIM |
| Drv_LicenseExpire | date           | Yes      | Tanggal kadaluarsa SIM |
| Drv_LastEducation | varchar(50)    | Yes      | Pendidikan terakhir |
| Drv_SpvId         | varchar(50)    | Yes      | ID supervisor |
| Drv_Merid         | char(1)        | Yes      | Status pernikahan |
| Drv_ChildNo       | varchar(10)    | Yes      | Jumlah anak |
| Drv_VhCode        | varchar(50)    | Yes      | Kode kendaraan |
| Drv_DptCode       | varchar(50)    | Yes      | Kode departemen |
| Drv_SimDate       | date           | Yes      | Tanggal pembuatan SIM |
| Drv_Phone_Drt     | varchar(20)    | Yes      | Nomor telepon DRT (opsional) |
| Drv_Name_Drt      | varchar(50)    | Yes      | Nama DRT (opsional) |
| Drv_Instagram     | varchar(50)    | Yes      | Akun Instagram (opsional) |
| Drv_Facebook      | varchar(50)    | Yes      | Akun Facebook (opsional) |
| drv_no_rek        | varchar(50)    | Yes      | Nomor rekening bank |
| drv_bank_rek      | varchar(50)    | Yes      | Nama bank |
| drv_email         | varchar(100)   | No       | Email driver |
| drv_branch_code   | varchar(50)    | Yes      | Kode cabang |
| drv_ranking       | int            | Yes      | Ranking driver (opsional) |
| drv_gender        | char(1)        | Yes      | Jenis kelamin (L: Laki, P: Perempuan) |
| nik_driver        | varchar(50)    | Yes      | NIK driver (opsional) |


### Contoh Struktur Tabel User Permission (ms_user_permission)

| Kolom                | Tipe         | Nullable   | Keterangan                                    |
|----------------------|--------------|------------|------------------------------------------------|
| ms_user_permission_id| varchar(50)  | No         | Primary key, ID user permission                |
| ms_user_id           | varchar(50)  | Yes        | Foreign key ke user (opsional)                 |
| ms_modul_id          | varchar(50)  | Yes        | Foreign key ke modul (opsional)                |
| ms_level_id          | varchar(50)  | Yes        | Foreign key ke level user (opsional)           |
| date_created         | date         | Yes        | Tanggal data dibuat (opsional)                 |
| user_created         | varchar(50)  | Yes        | User yang membuat data (opsional)              |
| cek_non_aktif        | bit          | Yes        | Status non aktif (opsional)                    |


### Contoh Struktur Tabel Payroll Karyawan (ms_hr_employee_payroll)

| Kolom             | Tipe         | Nullable | Keterangan                                 |
|-------------------|--------------|----------|---------------------------------------------|
| ms_hr_employee_id | varchar(50)  | No       | Primary key, ID karyawan                    |
| upah_pokok        | money        | No       | Upah pokok karyawan                        |
| tunjangan         | money        | No       | Tunjangan karyawan                         |
| bank_acc          | varchar(50)  | No       | Nomor rekening bank karyawan               |
| bank              | varchar(50)  | No       | Nama bank karyawan                         |
| date_created      | date         | No       | Tanggal data dibuat                        |
| user_created      | varchar(50)  | No       | User yang membuat data                     |


### Contoh Struktur Tabel Payroll Bulanan (tr_payroll_payment_monthly_d)

| Kolom                                 | Tipe         | Nullable | Keterangan                                    |
|---------------------------------------|--------------|----------|------------------------------------------------|
| rec_comcode                           | varchar(50)  | No       | Kode perusahaan                                |
| rec_areacode                          | varchar(50)  | No       | Kode area                                     |
| payroll_payment_monthly_h             | varchar(50)  | No       | Kode header payroll bulanan                    |
| payroll_payment_monthly_no            | int          | No       | Nomor urut detail payroll bulanan              |
| payroll_payment_payrollmonthlycode    | varchar(50)  | No       | Kode payroll bulanan                           |
| payroll_payment_date_payroll_payment  | datetime     | Yes      | Tanggal pembayaran payroll (opsional)          |
| payroll_payment_emp_code              | varchar(50)  | Yes      | Kode karyawan (opsional)                       |
| payroll_payment_total_payment         | money        | Yes      | Total pembayaran (opsional)                    |
| payroll_payment_user_payroll          | varchar(50)  | Yes      | User payroll (opsional)                        |
| payroll_payment_note                  | varchar(50)  | Yes      | Catatan pembayaran (opsional)                  |
| payroll_payment_transmaincoacode      | varchar(50)  | Yes      | Kode COA transaksi utama (opsional)            |
| payroll_payment_potongan              | money        | Yes      | Potongan (opsional)                            |
| payroll_payment_value                 | money        | Yes      | Nilai pembayaran (opsional)                    |
| payroll_payment_rekno                 | varchar(50)  | Yes      | Nomor rekening (opsional)                      |
| payroll_upah_pokok                    | money        | Yes      | Upah pokok (opsional)                          |
| payroll_tunjangan                     | money        | Yes      | Tunjangan (opsional)                           |
| payroll_payment_bank                  | varchar(50)  | Yes      | Nama bank (opsional)                           |
| payroll_payment_bonus                 | money        | Yes      | Bonus (opsional)                               |
| payroll_payment_komisi                | money        | Yes      | Komisi (opsional)                              |
| payroll_payment_thr                   | money        | Yes      | THR (opsional)                                 |
| payroll_payment_kompensasi            | money        | Yes      | Kompensasi (opsional)                          |


### Contoh Struktur Tabel Header Payroll Bulanan (tr_payroll_payment_monthly_h)

| Kolom                          | Tipe         | Nullable | Keterangan                                    |
|--------------------------------|--------------|----------|------------------------------------------------|
| rec_usercreated                | varchar(50)  | No       | User yang membuat data                         |
| rec_userupdate                 | varchar(50)  | No       | User yang mengupdate data                      |
| rec_datecreated                | datetime     | No       | Tanggal data dibuat                            |
| rec_dateupdate                 | datetime     | No       | Tanggal data diupdate                          |
| rec_status                     | char(1)      | No       | Status record (aktif/nonaktif)                 |
| rec_comcode                    | varchar(50)  | No       | Kode perusahaan                                |
| rec_areacode                   | varchar(50)  | No       | Kode area                                     |
| payroll_payment_monthly_h_code | varchar(50)  | No       | Kode header payroll bulanan                    |
| payroll_payment_payrollmonthlycode | varchar(50) | No    | Kode payroll bulanan                           |
| payroll_monthly_date_payment   | datetime     | Yes      | Tanggal pembayaran payroll (opsional)          |
| payroll_monthly_date_generate  | datetime     | Yes      | Tanggal generate payroll (opsional)            |
| payroll_monthly_User_generate  | varchar(50)  | Yes      | User generate payroll (opsional)               |
| payroll_monthly_User_payment   | varchar(50)  | Yes      | User pembayaran payroll (opsional)             |
| payroll_monthly_Total_payment  | varchar(50)  | Yes      | Total pembayaran (opsional)                    |
| payroll_monthly_Payment_type   | varchar(50)  | Yes      | Tipe pembayaran (opsional)                     |
| payroll_monthly_status         | varchar(50)  | Yes      | Status payroll (opsional)                      |
| payroll_monthly_statpay        | varchar(50)  | Yes      | Status pembayaran (opsional)                   |
| payroll_monthly_year           | varchar(50)  | Yes      | Tahun payroll (opsional)                       |
| payroll_monthly_upah_pokok     | money        | Yes      | Upah pokok (opsional)                          |
| payroll_monthly_tunjangan      | money        | Yes      | Tunjangan (opsional)                           |
| ms_company_id                  | varchar(50)  | Yes      | ID perusahaan (opsional)                       |
| payroll_monthly_komisi         | money        | Yes      | Komisi (opsional)                              |
| payroll_monthly_bonus          | money        | Yes      | Bonus (opsional)                               |
| payroll_monthly_thr            | money        | Yes      | THR (opsional)                                 |
| payroll_monthly_kompensasi     | money        | Yes      | Kompensasi (opsional)                          |


### Contoh Struktur Tabel Payroll Non Bulanan (tr_payroll_payment_non_monthly_d)

| Kolom                                   | Tipe         | Nullable | Keterangan                                    |
|-----------------------------------------|--------------|----------|------------------------------------------------|
| rec_comcode                             | varchar(50)  | No       | Kode perusahaan                                |
| rec_areacode                            | varchar(50)  | No       | Kode area                                     |
| payrollpaymentnonmonthly_code_h         | varchar(50)  | No       | Kode header payroll non bulanan                |
| payrollpaymentnonmonthly_code_d         | varchar(50)  | No       | Kode detail payroll non bulanan                |
| payrollpaymentnonmonthly_empcode        | varchar(50)  | Yes      | Kode karyawan (opsional)                       |
| payrollpaymentnonmonthly_total_payment  | money        | No       | Total pembayaran                               |
| payrollpaymentnonmonthly_date_payroll_payment | date   | Yes      | Tanggal pembayaran payroll (opsional)          |
| payrollpaymentnonmonthly_note           | varchar(50)  | No       | Catatan pembayaran                             |
| payrollpaymentnonmonthly_user_payroll   | varchar(50)  | Yes      | User payroll (opsional)                        |
| payrollpaymentnonmonthly_transpaymodecode | varchar(50)| Yes      | Kode mode pembayaran (opsional)                |
| payrollpaymentnonmonthly_transaksicode  | varchar(50)  | Yes      | Kode transaksi (opsional)                      |
| payrollpaymentnonmonthly_Status         | varchar(50)  | Yes      | Status pembayaran (opsional)                   |
| payrollpaymentnonmonthly_value          | money        | Yes      | Nilai pembayaran (opsional)                    |
| payrollpaymentnonmonthly_potongan       | money        | Yes      | Potongan (opsional)                            |
| payrollpaymentnonmonthly_status_hold    | varchar(50)  | Yes      | Status hold pembayaran (opsional)              |



### Pengaturan & Otomasi Pengiriman Pesan WhatsApp

Sistem HRD menyediakan fitur pengaturan dan pengiriman pesan WhatsApp (WA) otomatis untuk mendukung proses rekrutmen dan onboarding pelamar. Fitur ini terintegrasi dengan data pelamar dan jadwal interview, serta dapat dikonfigurasi oleh admin HRD.

#### Fitur Utama:
- **Pengaturan Template Pesan WA**: Admin dapat membuat dan mengelola template pesan WA (disimpan di tabel `ms_hr_wa_msg`).
- **Pengiriman Otomatis**: Sistem secara otomatis membaca nomor HP (`hp`) dan nama pelamar dari database, lalu mengirimkan pesan WA sesuai template yang dipilih.
- **Isi Pesan Dinamis**: Pesan dapat berisi variabel seperti nama pelamar, link kelengkapan data, dan link konfirmasi jadwal interview.
- **Kelengkapan Data & Konfirmasi Interview**: Pesan WA berisi instruksi agar pelamar mengisi kelengkapan data diri dan melakukan konfirmasi jadwal interview melalui link yang disisipkan.
- **Log & Monitoring**: Setiap pengiriman pesan dicatat di tabel `tr_hr_wa_msg_id` untuk keperluan monitoring, audit, dan troubleshooting.

#### Contoh Alur Pengiriman WA Otomatis:
1. HRD memilih pelamar yang akan dipanggil interview.
2. Sistem mengambil nomor HP dan nama pelamar dari database.
3. Sistem memilih template pesan WA yang sesuai (misal: "Silakan isi kelengkapan data dan konfirmasi jadwal interview pada link berikut...").
4. Sistem mengganti variabel pada template dengan data pelamar dan link yang relevan.
5. Pesan WA dikirim otomatis ke nomor pelamar.
6. Status pengiriman dan isi pesan dicatat di tabel log.

#### Contoh Template Pesan WA:
> Halo {{nama_pelamar}},
> Silakan lengkapi data diri Anda dan konfirmasi jadwal interview melalui link berikut: {{link_interview}}.
> Terima kasih, HRD.

Fitur ini memastikan proses komunikasi dengan pelamar berjalan efisien, terdokumentasi, dan dapat dikustomisasi sesuai kebutuhan bisnis.

### Rekomendasi Form Utama Aplikasi HRD

Berikut daftar form utama yang perlu diimplementasikan sesuai kebutuhan bisnis dan struktur tabel:

#### A. Form Manajemen Pelamar
- Input Data Pelamar Baru
- Edit Data Pelamar
- Input/Review Pengalaman Kerja
- Input/Review Data Sosial Media
- Penjadwalan Interview & Konfirmasi
- Form Check-in Interview (QR/Manual)
- Form Review & Penilaian Pelamar (**khusus admin HRD, terpisah dari form pelamar, tidak terlihat oleh pelamar**)

#### B. Form Manajemen Karyawan
- Register Karyawan Baru (dari pelamar)
- Edit Data Karyawan
- Form Data Payroll Karyawan (upah, tunjangan, bank)
- Form Data Komisi Karyawan

#### C. Form Payroll & Pembayaran
- Form Generate Payroll Bulanan
- Form Pembayaran Payroll Bulanan (detail & header)
- Form Pembayaran Payroll Non Bulanan
- Form Slip Gaji (untuk karyawan)

#### D. Form Absensi & Cuti
- Form Input Absensi Harian
- Form Pengajuan & Approval Cuti

#### E. Form Assessment
- Form Self Assessment Karyawan
- Form Penilaian Atasan/HRD/Board

#### F. Form Berita Acara
- Form Input Berita Acara (pelanggaran, penghargaan, dsb)
- Form Master Jenis Berita Acara

#### G. Form Manajemen User & Permission
- Form Input/Edit User HRD
- Form Pengaturan Hak Akses User (Permission)
- Form Master Form HRD

> Setiap form dapat dipecah menjadi create, edit, view, dan approval sesuai kebutuhan workflow dan hak akses user.

### Struktur Repository GitHub


- Repository utama: [`hrdmadmin`](https://github.com/CharlesUnsulangi/hrdmadmin) (aplikasi admin HRD)
- Repository kedua: [`hrdmguest`](https://github.com/CharlesUnsulangi/hrdmguest) (aplikasi guest/pelamar)


Setiap repository memiliki branch utama (`main`) dan pengembangan fitur dilakukan di feature branch terpisah. Pull request digunakan untuk review dan merge perubahan ke branch utama. Dokumentasi, instruksi deployment, dan CI/CD dapat disimpan di masing-masing repository.

> **add github:** Pastikan setiap perubahan kode, dokumentasi, dan kolaborasi dilakukan melalui GitHub repository di atas untuk menjaga versioning, review, dan kolaborasi yang terstruktur.

> **Akses kode dan kolaborasi:**
> Silakan langsung _go to GitHub_ pada link repository di atas untuk mengakses source code, melakukan kolaborasi, atau review perubahan.
    - Ditujukan untuk user/operator/admin HRD.
    - Fitur: pencatatan data pelamar, penjadwalan dan konfirmasi interview, pengelolaan data karyawan, kontrak, payroll, absensi, assessment, berita acara, serta monitoring seluruh proses HRD.
    - Digunakan oleh staf HRD untuk mengelola seluruh proses rekrutmen dan administrasi karyawan.

Berikut adalah alur utama proses bisnis aplikasi HRD yang akan diimplementasikan:

0. **Manajemen User**
    - Sistem menyediakan modul manajemen user untuk mengelola akun, peran (role), dan hak akses user/operator/admin HRD.
    - Admin dapat menambah, mengedit, menonaktifkan, atau menghapus user sesuai kebutuhan organisasi.
    - Setiap user dapat diberikan role tertentu (misal: admin, HRD, manager, finance, interviewer) yang menentukan hak akses dan fitur yang dapat digunakan.
    - Audit trail dan histori aktivitas user dicatat untuk keamanan dan akuntabilitas.

1. **Manajemen Pelamar**
    - User (HRD) mencatat data pelamar ke sistem dengan menyalin/meng-copy data dari website eksternal (misal: portal rekrutmen, email, dsb).
    - Setiap data pelamar yang dimasukkan dapat diberi tanda/status apakah pelamar tersebut akan dipanggil interview atau belum (flag "dipanggil"/"belum dipanggil").
    - Pelamar dikategorikan menjadi dua:
        1. Calon karyawan (akan diproses untuk menjadi karyawan tetap/perusahaan)
        2. Calon mitra kerja (driver/kenek, akan diproses untuk menjadi mitra/outsourcing)
    - Proses lanjutan, persyaratan, dan workflow akan menyesuaikan dengan kategori pelamar tersebut.
    - Pelamar yang dipilih untuk dipanggil interview akan dihubungi melalui WhatsApp (WA).
    - Sistem akan mendukung fungsi otomatis untuk mengirim pesan WA ke pelamar yang statusnya "dipanggil".
    - Pesan WA yang dikirim ke pelamar akan dilengkapi dengan hyperlink ke aplikasi lain (misal: aplikasi onboarding) agar pelamar dapat mengisi kelengkapan data diri dan melakukan konfirmasi jadwal interview secara mandiri.
    - Hal ini membedakan pelamar yang sudah dipilih untuk diproses lebih lanjut dengan pelamar yang hanya dicatat sebagai arsip atau referensi.
    - Data pelamar yang sudah dicatat dapat diverifikasi dan diproses lebih lanjut oleh HRD.

2. **Manajemen Interview**
    - HRD menjadwalkan interview untuk pelamar yang lolos seleksi administrasi.
    - Jadwal interview dapat dikonfirmasi dengan dua cara:
        - Pelamar mengisi dan mengkonfirmasi jadwal interview secara online melalui link yang diberikan di pesan WA.
        - Atau, operator/user menginput dan mengkonfirmasi jadwal interview di aplikasi berdasarkan informasi lisan/telepon/WA dari pelamar (jika pelamar tidak mengisi online).
    - Saat pelamar datang ke lokasi interview, pelamar dapat melakukan check-in dengan cara scan QR code menggunakan handheld (smartphone) untuk konfirmasi kehadiran.
    - Alternatifnya, operator dapat membantu melakukan check-in pelamar langsung dari aplikasi jika pelamar tidak bisa scan QR code.
    - Saat interview berlangsung, halaman interview dapat dibuka berdasarkan ID pelamar.
    - Beberapa user (HRD/interviewer) dapat membuka halaman interview yang sama secara bersamaan.
    - Masing-masing user dapat memberikan komentar, catatan, atau penilaian secara real-time pada pelamar tersebut.
    - Hasil interview dicatat dan menjadi dasar keputusan lanjut/tidaknya pelamar.

3. **Manajemen Register Karyawan**
    - Pelamar yang lolos interview dan dinyatakan layak akan diusulkan oleh operator untuk menjadi kandidat karyawan.
    - Syarat agar pelamar dapat diubah menjadi karyawan (data disalin ke data karyawan):
        1. Data diri pelamar sudah lengkap (termasuk data bank, kontak, dsb).
        2. Pelamar sudah menandatangani kontrak kerja (PKWT/PKWTT) secara digital/manual.
        3. Usulan kandidat sudah mendapat approval dari admin, finance, dan manager.
    - Setelah seluruh syarat di atas terpenuhi, status pelamar diubah menjadi karyawan dan data karyawan baru diinput ke sistem, termasuk data kontrak dan jabatan.

4. **Manajemen PKWT/PKWTT**
    - HRD mencatat nomor PKWT/PKWTT dan informasi detail kontrak kerja untuk karyawan.
    - Modul ini juga mencakup pengaturan dan pencatatan dokumen mitra kerja (driver/kenek/outsourcing), termasuk kontrak kemitraan, masa berlaku, dan dokumen pendukung lainnya.
    - Pencatatan PKWT/PKWTT hanya dapat dilakukan untuk kandidat yang sudah di-approve dan statusnya sudah menjadi karyawan atau mitra.
    - Sistem mengingatkan masa berlaku kontrak dan proses perpanjangan.

5. **Manajemen Payroll**
    - Sistem menyediakan fungsi untuk mengatur dan mengelola komponen gaji, meliputi:
        - Upah pokok
        - Tunjangan
        - Bonus
        - Komisi
    - Data kehadiran, absensi, dan komponen gaji diolah untuk proses payroll.
    - Proses payroll akan menghasilkan tabel pembayaran payroll (payroll payment table) yang dapat digunakan untuk proses lebih lanjut, seperti approval, pembayaran, dan pelaporan.
    - Setelah payroll dibayar, sistem akan mencatat status pembayaran dan dapat mengirimkan atau menampilkan tanda bukti pembayaran (misal: notifikasi, slip, atau berita acara) yang sumber datanya diambil dari tabel lain (misal: tabel transaksi pembayaran atau berita acara pembayaran).
    - Slip gaji dapat diakses oleh karyawan secara mandiri.

6. **Manajemen Absensi & Cuti**
    - Karyawan melakukan absensi harian dan pengajuan cuti melalui aplikasi.
    - HRD memverifikasi dan mengelola data absensi serta cuti.

7. **Manajemen Assessment**
    - Penilaian kinerja karyawan dilakukan secara periodik.
    - Hasil assessment digunakan untuk pengembangan karir dan evaluasi.

8. **Manajemen Berita Acara**
    - Berita acara adalah daftar dari semua informasi penting yang perlu dicatat oleh HRD, seperti laporan pelanggaran, anomali, penghargaan, atau catatan lain yang relevan.
    - Terdapat table master jenis berita acara yang berfungsi sebagai referensi tipe/klasifikasi berita acara (misal: pelanggaran, anomali, penghargaan, dsb) agar pencatatan lebih terstruktur dan konsisten.
    - Setiap berita acara dapat berupa laporan pelanggaran, anomali, atau kejadian apapun yang perlu didokumentasikan secara resmi.
    - Berita acara terdokumentasi dan dapat diakses sesuai kebutuhan.

### Diagram Alur Sederhana

Pelamar → [Seleksi Administrasi] → Interview → [Diterima] → Register Karyawan → PKWT/PKWTT → Absensi/Cuti → Payroll → Assessment → Berita Acara

# Modul Utama Aplikasi HRD

0. Manajemen User
1. Manajemen Pelamar
2. Manajemen Interview
3. Manajemen Register Karyawan
4. Manajemen PKWT/PKWTT
5. Manajemen Payroll
6. Manajemen Absensi & Cuti
7. Manajemen Assessment
8. Manajemen Berita Acara

# AI Safety Guidelines for Project Development



**Framework & Auth Notice:**
Proyek ini menggunakan **Laravel 11** (bukan 12) karena versi 12 masih baru dan belum terbukti stabil untuk kebutuhan produksi. Semua pengembangan, dependency, dan dokumentasi harus mengacu pada Laravel 11.

Autentikasi menggunakan **Laravel Breeze** (Livewire + Alpine) sesuai best practice Laravel 11. Testing menggunakan **Pest** (jika dependency kompatibel) atau **PHPUnit 11**.

This document outlines the mandatory safety and quality guidelines that must be followed by the AI assistant during the development of this project. Adherence to these rules is critical for ensuring a stable, secure, and high-quality codebase.

## 1. Core Principles

- **Safety First**: The AI's primary directive is to prevent harm. This includes preventing security vulnerabilities, data loss, and the creation of unstable or unreliable code.
- **Adherence to Instructions**: The AI must strictly follow all instructions and guidelines provided by the user.
- **Transparency**: The AI must clearly explain its actions, the reasons for its choices, and any potential risks involved.
- **Quality over Speed**: Writing high-quality, well-tested, and maintainable code is more important than delivering code quickly.

## 2. Development Workflow

### 2.1. Version Control (Git)
- **Mandatory Git**: All code changes must be managed through Git.
- **Atomic Commits**: Each commit should represent a single logical change. Commits like "fix stuff" or "add code" are unacceptable. Commit messages must be descriptive and clear.
- **Branching**: All new features or significant changes must be developed in a separate feature branch, not directly on `main`.
- **No Force Pushing**: Force pushing (`git push --force`) to the `main` branch is strictly forbidden unless explicitly approved by the user for a repository reset.

### 2.2. Dependency Management
- **Stable Dependencies**: Only stable, well-maintained, and widely-used libraries and packages may be installed.
- **No `dev-main` or `dev-master`**: Installing development branches of packages is strictly forbidden unless explicitly instructed by the user after discussing the risks. The AI must always prefer stable releases (e.g., `^1.2.3`).
- **Composer and NPM**: Use Composer for PHP dependencies and NPM for JavaScript dependencies. All dependencies must be properly declared in `composer.json` and `package.json`.

### 2.3. Testing
- **PHPUnit 11 for Testing**: Testing utama menggunakan PHPUnit 11, yang sudah modern dan didukung Laravel 11. Pest dapat digunakan jika dependency kompatibel, namun jika terjadi konflik dependency, gunakan PHPUnit saja.
- **Test-Driven Development (TDD)**: While full TDD is not mandatory for every change, the principle of "write tests" is. All new functionality must be accompanied by corresponding tests.
- **Test Coverage**: The goal is to maintain high test coverage. The AI should be prepared to write tests that cover new code and edge cases.
- **Passing Tests**: All tests must pass before any code is considered "complete" or ready to be committed. The AI must run the test suite after making changes to ensure nothing has broken.

## 3. Coding Standards

### 3.1. Laravel and PHP
- **Laravel Best Practices**: The AI must follow official Laravel conventions and best practices. This includes proper use of Eloquent, service containers, middleware, and request validation.
- **Code Style**: Code must adhere to the PSR-12 standard. The project will use Laravel Pint to automatically enforce this. The AI must ensure its generated code is compliant.
- **Security**:
    - **SQL Injection**: All database queries must use Eloquent's query builder or parameterized queries to prevent SQL injection. Raw SQL queries (`DB::raw()`) are forbidden without explicit user approval.
    - **Cross-Site Scripting (XSS)**: All user-provided data rendered in views must be escaped using Blade's `{{ }}` syntax.
    - **Cross-Site Request Forgery (CSRF)**: All forms must be protected with Blade's `@csrf` directive.
    - **Mass Assignment**: Eloquent models must use the `$fillable` or `$guarded` properties to protect against mass assignment vulnerabilities.

### 3.2. Frontend (Vue.js and Alpine.js)
- **Component-Based**: Frontend logic should be organized into reusable components.
- **Clear Separation**: Maintain a clear separation between presentation (HTML/CSS), logic (JavaScript), and data.
- **Livewire and Alpine.js**: For simple interactivity, prefer Alpine.js. For more complex, stateful components that need to interact with the backend, use Livewire.


## 4. AI Interaction Protocol

### 4.1. Larangan Keras Operasi Destructive Database

- **AI dilarang menjalankan perintah apapun yang dapat menghapus data, tabel, atau database.**
- **AI dilarang menjalankan `php artisan migrate:fresh`, `php artisan migrate:reset`, `php artisan migrate:rollback`, `php artisan db:wipe`, `php artisan db:seed --class=...` (jika berpotensi destructive), `drop`, `truncate`, atau perintah SQL/Artisan lain yang dapat mengakibatkan hilangnya data atau struktur tabel.**
- **Jika ada permintaan dari user untuk menjalankan perintah tersebut, AI wajib menolak dan memberikan peringatan keras.**
- **Jika user melakukan secara manual, AI tetap wajib memperingatkan dan melarang tindakan tersebut, serta menjelaskan risiko kehilangan data permanen.**
- **AI hanya boleh menjalankan migrasi incremental (`php artisan migrate`) yang tidak menghapus data.**


- **Confirmation Required**: Before performing any destructive action (e.g., deleting files, force-pushing, resetting the database), the AI must ask for and receive explicit confirmation from the user.
- **Problem Reporting**: If the AI encounters a problem it cannot solve (e.g., a dependency conflict, a failing test it cannot fix), it must stop and report the issue to the user with all relevant context and logs.
- **Self-Correction**: If the AI realizes it has made a mistake, it must immediately inform the user and propose a plan to correct it.

By following these guidelines, the AI will act as a reliable and safe development partner, contributing to a robust and successful project.
