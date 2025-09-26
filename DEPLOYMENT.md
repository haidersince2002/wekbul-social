# ConnectHub Deployment Guide

This guide provides step-by-step instructions for deploying the ConnectHub social network application to Render.com.

## Prerequisites

- A [Render.com](https://render.com) account
- Your project code in a Git repository (GitHub, GitLab, etc.)

## Deployment Steps

### 1. Prepare Your Repository

1. Make sure your code is pushed to a Git repository.
2. Verify you have these key files in your repository:
   - `render.yaml` (deployment configuration)
   - `db-init.php` (database initialization script)
   - `config/local.sample.php` (environment variable template)

### 2. Connect Your Repository to Render

1. Log in to your [Render Dashboard](https://dashboard.render.com)
2. Click **New** and select **Blueprint**
3. Connect your Git account if you haven't already
4. Select your repository
5. Click **Apply Blueprint**

### 3. Configure Your Web Service

Render will detect the `render.yaml` file and create resources automatically.
You'll need to configure these critical environment variables:

1. In the Render dashboard, click on your new web service
2. Go to **Environment** tab
3. Add/update these key variables:
   - `DB_HOST`: Your database hostname (provided by Render)
   - `DB_NAME`: `social_network`
   - `DB_USER`: Your database username (provided by Render)
   - `DB_PASS`: Your database password (provided by Render)
   - `BASE_URL`: Your app URL (e.g., `https://connecthub.onrender.com`)

### 4. Link Database to Web Service

1. Go to your database service in the Render dashboard
2. Copy the **Internal Database URL** (contains the connection details)
3. Update your web service environment variables with these details

### 5. Deploy Your Application

1. Go back to your web service
2. Click **Manual Deploy** > **Deploy latest commit**
3. Wait for the build process to complete (you can monitor the logs)

### 6. Verify Deployment

1. Once deployment completes, click the generated URL to view your application
2. Open `/deployment-check.php` to verify your environment (only works with DEBUG=true)
3. Create a test account to verify functionality
4. Check that image uploads work correctly

## Troubleshooting

### Database Connection Issues

- Verify the database environment variables match your Render database credentials
- Check Render logs for specific error messages

### Missing Images

- Ensure the upload directories exist and have proper permissions
- Verify that `UPLOAD_DIR_PROFILES` and `UPLOAD_DIR_POSTS` environment variables are set correctly

### Blank Pages or PHP Errors

- Set `DEBUG=true` temporarily to view detailed error messages
- Check the Render logs for PHP errors

## Updating Your Deployed Application

1. Push changes to your Git repository
2. Render will automatically detect changes and redeploy
3. Monitor the logs to ensure successful deployment

## Security Notes

- After successful deployment, set `DEBUG=false` in your environment variables
- Remove or restrict access to `deployment-check.php`
- Regularly update your dependencies for security patches
