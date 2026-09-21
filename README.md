# Mobile-Based Monitoring System of Children
## Alamada LGU Learning Center

**Authors:** Joseph Joy A. Emperado · Junril V. Adlawan  
**Institution:** Southern Christian College, Midsayap, Cotabato  
**Degree:** Bachelor of Science in Information Technology  

---

## Project Structure

```
/
├── backend/        Laravel 10 REST API (Admin + Staff web platform)
└── android/        Android mobile app (Parents)
```

---

## Backend Setup (Laravel)

### Requirements
- PHP 8.1+
- Composer
- MySQL 8.0+ (via XAMPP or standalone)
- XAMPP (Apache + MySQL)

### Steps

1. **Start XAMPP** — ensure Apache and MySQL are running.

2. **Create the database**  
   Open phpMyAdmin → create a new database named `alamada_cms`.

3. **Install and configure**
   ```bash
   cd backend
   setup.bat          # copies .env, composer install, key:generate, migrate, seed
   ```

4. **Start the server**
   ```bash
   php artisan serve   # runs at http://localhost:8000
   ```

### Demo Accounts (seeded)

| Role   | Email                                  | Password     |
|--------|----------------------------------------|--------------|
| Admin  | admin@alamada-lgu.gov.ph               | Admin@1234   |
| Staff  | maria.santos@alamada-lgu.gov.ph        | Staff@1234   |
| Parent | ana.delacruz@gmail.com                 | Parent@1234  |

---

## Android App Setup

### Requirements
- Android Studio Hedgehog or newer
- Android SDK 24+
- A physical device or emulator (API 24+)

### Steps

1. Open Android Studio → **Open** → select the `android/` folder.
2. In `app/build.gradle`, update `BASE_URL` if testing on a real device  
   (replace `10.0.2.2` with your PC's local IP, e.g. `192.168.1.x`):
   ```groovy
   buildConfigField "String", "BASE_URL", '"http://192.168.1.10:8000/api/"'
   ```
3. Click **Sync Project with Gradle Files**.
4. Run on emulator or device.

> The app is for **parents only**. Admin and staff use the web platform.

---

## API Reference (key endpoints)

| Method | Endpoint                          | Role          | Description                        |
|--------|-----------------------------------|---------------|------------------------------------|
| POST   | /api/login                        | All           | Authenticate, receive token        |
| POST   | /api/logout                       | Auth          | Revoke token                       |
| GET    | /api/parent/children              | Parent        | List own children                  |
| GET    | /api/parent/children/{id}         | Parent        | Child detail                       |
| GET    | /api/parent/attendance            | Parent        | Attendance records (filter by month)|
| GET    | /api/parent/attendance/summary    | Parent        | Monthly summary stats              |
| GET    | /api/parent/enrollments           | Parent        | Enrollment records                 |
| GET    | /api/notifications                | All           | Notifications list                 |
| GET    | /api/notifications/unread-count   | All           | Unread badge count                 |
| POST   | /api/notifications/{id}/read      | All           | Mark one as read                   |
| POST   | /api/notifications/read-all       | All           | Mark all as read                   |
| GET    | /api/children                     | Admin/Staff   | All children (CRUD)                |
| GET    | /api/attendance                   | Admin/Staff   | Attendance records                 |
| POST   | /api/attendance/bulk              | Staff         | Bulk attendance recording          |
| GET    | /api/enrollments                  | Admin/Staff   | Enrollment management              |
| PUT    | /api/admin/enrollments/{id}/approve | Admin       | Approve enrollment                 |
| GET    | /api/admin/reports/enrollment     | Admin         | Enrollment report (age/gender/year)|
| GET    | /api/admin/reports/attendance     | Admin         | Attendance report (by month)       |
| GET    | /api/admin/users                  | Admin         | User management                    |

All authenticated endpoints require: `Authorization: Bearer <token>`

---

## Technology Stack

| Layer        | Technology                          |
|--------------|-------------------------------------|
| Backend      | Laravel 10, PHP 8.1                 |
| Database     | MySQL 8 via XAMPP                   |
| Auth         | Laravel Sanctum (token-based)       |
| API          | RESTful JSON API                    |
| Mobile       | Android (Java), Retrofit 2, Glide   |
| Dev Tools    | XAMPP, Android Studio, Postman      |
