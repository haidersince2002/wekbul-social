# Step-by-Step Render Deployment

This guide provides a visual walkthrough of deploying ConnectHub to Render.com.

## 1. Create a Render Account

- Go to [render.com](https://render.com)
- Sign up for a free account
- Verify your email address

## 2. Connect Your Git Repository

```
[Render Dashboard] → [New +] → [Blueprint]
```

- Connect to your GitHub/GitLab account
- Select your repository with the ConnectHub code
- Ensure your repository contains the `render.yaml` file

## 3. Apply the Blueprint

- Click "Apply Blueprint"
- Render will scan your repository and detect the `render.yaml` file
- It will create both the Web Service and Database automatically

## 4. Configure Database Connection

```
[Database Service] → [Info] → Copy "Internal Connection URL"
```

- From your database service, copy the connection details
- Parse this URL to get the host, database name, username, and password
- Format: `mysql://{username}:{password}@{host}/{database}`

## 5. Set Environment Variables

```
[Web Service] → [Environment] → [Add Environment Variables]
```

Add these variables:

- `DB_HOST`: Value from connection URL
- `DB_NAME`: Value from connection URL
- `DB_USER`: Value from connection URL
- `DB_PASS`: Value from connection URL
- `BASE_URL`: Your app's URL (e.g., `https://connecthub.onrender.com`)

## 6. Deploy Your Application

```
[Web Service] → [Manual Deploy] → [Deploy Latest Commit]
```

- Wait for the build process to complete
- Monitor the logs for any errors
- The deployment includes running `db-init.php` to set up your database

## 7. Verify Your Deployment

- Click on the generated URL to open your application
- Try to sign up for a new account
- Test creating posts with images
- Verify that all features work as expected

## 8. Common Issues and Solutions

### Database Connection Failed

- Double-check environment variables against database connection URL
- Check build logs for database initialization errors

### Missing Upload Directories

- Verify the build log shows "Creating required directories"
- If missing, manually create them in the Render dashboard shell

### Image Upload Issues

- Verify the directory permissions are set correctly
- Check that `UPLOAD_DIR_PROFILES` and `UPLOAD_DIR_POSTS` are set correctly

## 9. Production Considerations

- Set `DEBUG=false` for production
- Delete or restrict access to `deployment-check.php`
- Consider upgrading your Render plan for better performance
- For high-traffic sites, consider using a CDN for images

## 10. Updating Your Application

- Push changes to your Git repository
- Render will automatically rebuild and deploy
- Check the logs to ensure the deployment succeeds
