# Huroof – Backend (Laravel)

This repository contains the backend of **Huroof**, a university reading platform project developed in the third year.  
The backend is built with **Laravel** and provides secure APIs for the mobile application (Flutter) and the admin dashboard (React).

---

## 🚀 Features

-   **Authentication & Security**

    -   Secure login and registration
    -   Password reset & change password flow
    -   Role-based access control

-   **Role & Permission Management**

    -   Super Admin: create/update admins and assign/revoke permissions
    -   Admin: manage content based on granted permissions (CRUD actions)

-   **Reader Management**

    -   View general reader information (profile, stats, activity)
    -   Handle complaints submitted by readers
    -   Accept/deny book suggestions from readers

-   **Content Management**

    -   CRUD operations for:
        -   Books
        -   Authors
        -   Categories & Size Categories
        -   Countries
        -   Reading Challenges
        -   Badges

-   **Integrations**

    -   **Firebase Cloud Messaging (FCM):** send notifications to readers
    -   **Cloudinary:** store images and book files securely in the cloud

-   **API for Mobile & Web**
    -   Serves the Flutter mobile app
    -   Serves the React admin dashboard

---

## 🛠️ Tech Stack

-   **Laravel** (PHP Framework)
-   **MySQL** (Database)
-   **Cloudinary** (Cloud storage for images & files)
-   **Firebase Cloud Messaging** (Push notifications)

---

## 📂 Project Structure

app/ # Application core code
config/ # Configuration files
database/ # Migrations & seeders
routes/ # API routes

---

## ⚙️ Installation & Setup

1. Clone the repository:

    ```bash
    git clone https://github.com/your-username/huroof-backend.git
    cd huroof-backend

    ```

2. Install dependencies:

    ```bash
    composer install

    ```

3. Copy the .env.example file and configure your environment:
    ```bash
    cp .env.example .env
    ```

-   **Update the following in .env:**
    -   **Database connection**
    -   **Cloudinary credentials**
    -   **Firebase configuration**

4. Generate application key:

    ```bash
    php artisan key:generate

    ```

5. Run migrations:

    ```bash
    php artisan migrate

    ```

6. Start the server:
    ```bash
    php artisan serve
    ```

## 📡 API Documentation

📌 Full API documentation is available in the Postman collection:

```bash
👉 https://www.postman.com/laravel-backend-team/workspace/reading-app-laravel


```
