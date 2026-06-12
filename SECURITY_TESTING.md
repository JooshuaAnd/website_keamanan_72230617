# Security Testing Documentation - OWASP ASVS Level 1

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@gmail.com | Admin123 |
| Dosen | dosen@lms.com | Dosen123 |
| Peserta | peserta@lms.com | Peserta123 |

---

## Test 1: Brute Force Protection (V2)

**Input:** Login password salah berkali-kali  
**Steps:**
1. Buka halaman login
2. Masukkan email: admin@gmail.com
3. Masukkan password salah sebanyak 5 kali berturut-turut
4. Pada percobaan ke-6, sistem akan menampilkan: "Account locked. Try again in 15 minute(s)."

**Expected Result:** Account diblokir sementara  
**Implementation Location:** `fastapi_auth/app/api/api_v1/endpoints/login.py:38-47`

---

## Test 2: Access Control (V4)

### Test 2a: Peserta akses halaman admin

**Steps:**
1. Login sebagai peserta (peserta@lms.com / Peserta123)
2. Akses URL: http://localhost:8000/admin/dashboard

**Expected Result:** 403 Forbidden  
**Implementation Location:** `app/Http/Middleware/CheckRole.php`

### Test 2b: Peserta akses fitur dosen

**Steps:**
1. Login sebagai peserta
2. Akses URL: http://localhost:8000/dosen/dashboard

**Expected Result:** 403 Forbidden

### Test 2c: Dosen akses pengelolaan user admin

**Steps:**
1. Login sebagai dosen
2. Akses URL: http://localhost:8000/admin/participants

**Expected Result:** 403 Forbidden

---

## Test 3: XSS Protection (V5)

### Test 3a: XSS pada pencarian

**Steps:**
1. Login sebagai admin
2. Buka halaman Data Peserta
3. Masukkan input: `<script>alert('XSS')</script>`
4. Klik Search

**Expected Result:** "Input tidak valid"  
**Implementation Location:** `fastapi_auth/app/api/api_v1/endpoints/lms.py:21-24`

### Test 3b: XSS pada form

**Steps:**
1. Login sebagai dosen
2. Buka halaman Upload Materi
3. Masukkan judul: `<script>alert('XSS')</script>`
4. Submit form

**Expected Result:** "Input tidak valid" atau script tag di-escape

### Test 3c: Output Sanitization

**Steps:**
1. Periksa semua template Blade menggunakan `{{ }}` (auto-escape)
2. Tidak ada penggunaan `{!! !!}` yang tidak perlu

**Expected Result:** All output is HTML-escaped by Blade engine

---

## Test 4: SQL Injection Protection (V5)

**Steps:**
1. Login sebagai admin
2. Buka halaman Data Peserta
3. Masukkan input: `' OR 1=1 --`
4. Klik Search

**Expected Result:** "Data tidak ditemukan" (karena prepared statement tidak terpengaruh)  
**Implementation Location:** 
- XSS/SQLi detection: `fastapi_auth/app/api/api_v1/endpoints/lms.py:21-27`
- Prepared statement: SQLAlchemy ORM digunakan di seluruh endpoint

---

## Test 5: Session Management (V3)

**Steps:**
1. Login sebagai user manapun
2. Buka Developer Tools -> Application -> Cookies
3. Catat cookie session
4. Klik Logout
5. Gunakan kembali cookie session yang sudah dicatat (via edit cookie atau curl)

**Expected Result:** Unauthorized / redirect ke halaman login  
**Implementation Location:** `app/Http/Controllers/FastAPIAuthController.php:115-119`

### Session Timeout

Session akan timeout setelah 120 menit (konfigurasi di `.env`: `SESSION_LIFETIME=120`)

### Session Regeneration

Session ID di-regen setiap kali login berhasil.
**Location:** `app/Http/Controllers/FastAPIAuthController.php:33-34`

---

## Test 6: Security Headers (V14)

**Steps:**
1. Buka aplikasi
2. Buka Developer Tools -> Network
3. Klik request pertama (document)
4. Periksa Response Headers

**Expected Headers:**
```
X-Frame-Options: DENY
X-Content-Type-Options: nosniff
Content-Security-Policy: ...
X-XSS-Protection: 1; mode=block
Referrer-Policy: strict-origin-when-cross-origin
```

**Implementation Location:** `app/Http/Middleware/SecureHeaders.php`

---

## OWASP ASVS Level 1 Checklist Summary

| # | Area | Implementasi | Status |
|---|------|-------------|--------|
| V2 | Authentication Verification | bcrypt hashing, brute force protection (5 attempts lockout), password validation (min 8, letters+numbers) | Done |
| V3 | Session Management | Session regeneration on login, session invalidation on logout, 120 min timeout | Done |
| V4 | Access Control | RBAC (Admin/Dosen/Peserta), middleware role checking, 403 Forbidden | Done |
| V5 | Validation & Sanitization | XSS input rejection, SQLi protection via prepared statements, output HTML escaping | Done |
| V14 | Secure Configuration | Security headers (X-Frame-Options, CSP, HSTS), custom error pages, debug mode disabled | Done |

## Password Security

- **Hash Algorithm:** bcrypt (via passlib)
- **Minimum Length:** 8 characters
- **Character Requirements:** At least 1 letter + at least 1 number
- **Storage:** Never stored in plaintext, always hashed

**Implementation Location:** `fastapi_auth/app/core/security.py:7-13`
