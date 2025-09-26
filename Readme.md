# ConnectHub Social Network

A lightweight social network built with PHP, MySQL, jQuery, HTML, and CSS. It includes au## Quick Tips

- If images don't display, verify files are in `uploads/` and that PHP has write permissions to this folder.
- For login issues, check the `users` table data and ensure passwords are created via the signup flow (bcrypt).
- To change the theme colors, adjust CSS variables in `assets/css/style.css` under `:root`.

## Deployment to Render

This project is configured for easy deployment to [Render](https://render.com), a cloud platform for hosting web applications.

### Prerequisites

1. **Render Account**: Sign up for a free Render account at [render.com](https://render.com)
2. **Git Repository**: Push your project to a Git provider (GitHub, GitLab, etc.)

### Deployment Steps

1. **Login to Render Dashboard**:

   - Go to [dashboard.render.com](https://dashboard.render.com) and sign in

2. **Create a New Web Service**:

   - Click on "New +" and select "Web Service"
   - Connect your Git repository and select it
   - Choose the branch you want to deploy (usually `main` or `master`)

3. **Configure Your Web Service**:

   - Name: `connecthub` (or your preferred name)
   - Environment: `PHP`
   - Build Command: Leave as is (already defined in `render.yaml`)
   - Start Command: Leave as is (already defined in `render.yaml`)

4. **Set Environment Variables**:

   - Most variables are already defined in `render.yaml`
   - Manually set these required variables:
     - `DB_HOST`: Your database hostname (provided by Render after database creation)
     - `DB_NAME`: `social_network` (or your preferred name)
     - `DB_USER`: Database username (provided by Render)
     - `DB_PASS`: Database password (provided by Render)
     - `BASE_URL`: Your app's URL (e.g., `https://connecthub.onrender.com`)

5. **Create a MySQL Database**:

   - Click on "New +" and select "MySQL"
   - Name: `connecthub-db` (must match the name in `render.yaml`)
   - Database: `social_network`
   - User: Use the generated username
   - Plan: Choose according to your needs (Free tier available)

6. **Link Database to Web Service**:

   - After creating the database, copy the connection details
   - Go back to your web service settings
   - Update the environment variables with the database connection details

7. **Deploy**:

   - Click "Create Web Service" to start the deployment
   - Render will automatically build and deploy your application
   - Database initialization will happen automatically via `db-init.php`

8. **Verify Deployment**:
   - Once deployment completes, click the generated URL to view your application
   - Create a test account to verify functionality
   - Check that image uploads work correctly

### Troubleshooting Deployment

- **Database Connection Issues**:

  - Verify your environment variables match the database credentials from Render
  - Check the Render logs for specific error messages

- **Missing Images**:

  - Verify the upload directories exist and have proper permissions
  - Check that the `UPLOAD_DIR_PROFILES` and `UPLOAD_DIR_POSTS` environment variables are set correctly

- **Blank Pages or PHP Errors**:

  - Set `DEBUG=true` temporarily to view detailed error messages
  - Check the Render logs for PHP errors

- **Long-term Storage**:
  - For production use, consider implementing cloud storage for uploads (like AWS S3)
  - Render's disk storage is ephemeral and will be reset on redeploys

### Updating Your Deployed Application

1. Push changes to your Git repository
2. Render will automatically detect changes and redeploy
3. Check logs to ensure successful deploymentication, profile management, posts with images, and reactions with a responsive, consistent UI.

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
