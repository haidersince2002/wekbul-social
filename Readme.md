# Social Network System

A lightweight social network built with PHP, MySQL, jQuery, HTML, and CSS. It includes authentication, profile management, posts with images, and reactions with a responsive, consistent UI.

## Features

- **User Authentication**

  - Secure signup with validation
  - Login with hashed passwords (bcrypt)
  - Session management with security features

- **Profile Management**

  - View and edit profile information
  - Upload and change profile pictures
  - Secure file upload with validation

- **Post Management**

  - Create posts with descriptions and images
  - View all user posts
  - Delete posts
  - Like/Dislike functionality with AJAX

- **Security Features**
  - Password hashing with bcrypt
  - File upload validation
  - SQL injection prevention with prepared statements
  - XSS protection with input sanitization
  - Session regeneration for security

## Setup (Windows/XAMPP)

1. Start Apache and MySQL in XAMPP.

2. Create the database and tables using `database.sql`:

   ```sql
   -- In phpMyAdmin, import database.sql into a database named social_network
   ```

3. Configure database credentials using environment variables (optional) or edit `config/database.php` defaults:

- Set these in Apache environment or a `.htaccess` at project root:
  ```
  SetEnv DB_HOST localhost
  SetEnv DB_NAME social_network
  SetEnv DB_USER root
  SetEnv DB_PASS ""
  ```

4. Ensure writable folders exist:

- `uploads/profiles` and `uploads/posts`

## Folder Structure

```
project/
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── csrf.php
├── classes/
│   ├── User.php
│   ├── Post.php
│   └── FileUpload.php
├── auth/
│   ├── signup.php
│   ├── login.php
│   └── logout.php
├── api/
│   ├── posts.php
│   ├── reactions.php
│   └── profile.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       ├── common.js
│       ├── signup.js
│       ├── login.js
│       ├── profile.js
│       └── home.js
├── uploads/
│   ├── profiles/
│   └── posts/
├── .htaccess
├── profile.php
├── index.php
└── README.md
```

## Technical Highlights

- ✅ Object-Oriented Programming (OOP) approach
- ✅ HTML, CSS, and jQuery frontend
- ✅ MySQL database with proper relationships
- ✅ Client-side and server-side validation
- ✅ AJAX for dynamic updates
- ✅ Secure file uploads
- ✅ Password hashing and security

## Usage

1. **Signup**: Create a new account with profile picture
2. **Login**: Access your account securely
3. **Profile**: View and edit your information
4. **Posts**: Create, view, and manage posts with likes/dislikes

## Security Features

- Passwords hashed with bcrypt
- File type and size validation
- SQL injection prevention
- XSS protection
- Session security
- Input sanitization
- CSRF protection on mutating requests

## Quick Tips

- If images don’t display, verify files are in `uploads/` and that PHP has write permissions to this folder.
- For login issues, check the `users` table data and ensure passwords are created via the signup flow (bcrypt).
- To change the theme colors, adjust CSS variables in `assets/css/style.css` under `:root`.
