﻿# Customer-Product-Supplier KPI Management System

This project consists of two main parts:

- **Laravel Backend** (located in `/kpi` directory)
- **React Frontend** (located in `/fontend` directory)

---

## Getting Started

### 1. Clone the Repository

```bash
git clone https://github.com/mostafijur007/task_g_soft.git
cd task_g_soft
```

---

## Laravel Backend Setup

1. Navigate to the backend directory:

   ```bash
   cd kpi
   ```

2. Install dependencies:

   ```bash
   composer install
   ```

3. Copy the `.env` file and generate the application key:

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Set up your database in the `.env` file:

   ```dotenv
   DB_DATABASE=your_database_name
   DB_USERNAME=your_db_user
   DB_PASSWORD=your_db_password
   ```

5. Run database migrations and seed :

   ```bash
   php artisan app:install
   ```

6. Set folder permissions (Linux/Mac only):

   ```bash
   chmod -R 775 storage bootstrap/cache
   ```

7. Start the Laravel development server:

   ```bash
   php artisan serve
   ```

---

## React Frontend Setup

1. Navigate to the frontend directory:

   ```bash
   cd ../fontend
   ```

2. Install Node.js dependencies:

   ```bash
   npm install
   ```

3. Create a `.env` file to configure your backend API URL:

   ```env
   VITE_API_BASE_URL=http://localhost:8000/api
   ```

4. Start the frontend development server:

   ```bash
   npm run dev
   ```

---

## Development URLs

- **Laravel Backend**: http://localhost:8000  
- **React Frontend**: http://localhost:5173 (or another Vite port)

---

## Tech Stack

- **Backend**: Laravel
- **Frontend**: React + Vite
- **Database**: Postgresql (or compatible)

---

