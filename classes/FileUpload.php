<?php

class FileUpload
{
    private $uploadDir; // absolute filesystem path to uploads base
    private $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    private $allowedExtensions = ['jpg', 'jpeg', 'png'];
    private $maxFileSize = 5 * 1024 * 1024; // 5MB

    public function __construct($uploadDir = 'uploads')
    {
        // Resolve to project root (one level up from classes/)
        $baseDir = dirname(__DIR__);
        $normalized = rtrim($uploadDir, "\\/");

        // If not absolute, resolve to project root
        $this->uploadDir = $this->isAbsolutePath($normalized)
            ? $normalized
            : $baseDir . DIRECTORY_SEPARATOR . $normalized;

        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function uploadFile($file, $subDir = '')
    {
        if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
            return ['success' => false, 'message' => 'No file uploaded'];
        }

        // Validate file type
        if (!in_array($file['type'], $this->allowedTypes)) {
            return ['success' => false, 'message' => 'Only JPG and PNG files are allowed'];
        }

        // Validate file size
        if ($file['size'] > $this->maxFileSize) {
            return ['success' => false, 'message' => 'File size must be less than 5MB'];
        }

        // Ensure subdirectory exists
        $targetDir = $this->uploadDir;
        if (!empty($subDir)) {
            $safeSub = trim($subDir, "\\/");
            $targetDir = $targetDir . DIRECTORY_SEPARATOR . $safeSub;
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }
        }

        // Generate unique filename
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions)) {
            return ['success' => false, 'message' => 'Invalid file extension'];
        }
        $filename = preg_replace('/[^a-zA-Z0-9_\.\-]/', '_', uniqid('', true) . '_' . time() . '.' . $extension);
        $targetPath = rtrim($targetDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $filename, 'path' => $targetPath];
        }

        return ['success' => false, 'message' => 'Failed to upload file'];
    }

    private function isAbsolutePath($path)
    {
        // Windows drive path (e.g., C:\) or UNC (\\\\server\\share) or Unix (/root)
        return (
            preg_match('/^[a-zA-Z]:\\\\/', $path) === 1 ||
            substr($path, 0, 2) === '\\' ||
            strpos($path, '/') === 0
        );
    }
}
