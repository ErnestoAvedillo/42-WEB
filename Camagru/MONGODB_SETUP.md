# MongoDB Setup Instructions

## 1. Build and start the containers
```bash
cd /home/ernesto/Desktop/WEB/Camagru
docker-compose down
docker-compose build
docker-compose up -d
```

## 2. Wait for MongoDB to be ready
```bash
docker-compose logs mongodb
```

## 3. Access the application
- **Web Application**: http://localhost:8080
- **Mongo Express (DB Admin)**: http://localhost:8081
- **MongoDB Direct**: localhost:27017

## 4. Test the functionality
1. Go to http://localhost:8080/register.php
2. Create a new user account
3. Go to http://localhost:8080/upload.php
4. Upload a file (image, PDF, Word doc, etc.)
5. Check Mongo Express at http://localhost:8081 to see the stored data

## MongoDB Collections Structure

### Users Collection
```json
{
  "_id": ObjectId("..."),
  "username": "johndoe",
  "email": "john@example.com", 
  "password": "hashed_password",
  "created_at": ISODate("..."),
  "is_active": true,
  "profile_picture": null,
  "files": [ObjectId("..."), ObjectId("...")]
}
```

### Files Collection
```json
{
  "_id": ObjectId("..."),
  "filename": "document.pdf",
  "file_type": "application/pdf",
  "file_size": 1024576,
  "file_data": "base64_encoded_file_content",
  "user_id": "507f1f77bcf86cd799439011",
  "upload_date": ISODate("...")
}
```

## Advantages of MongoDB for File Storage

1. **Flexible Schema**: Easy to add new file metadata fields
2. **Binary Data**: Direct storage of file content as base64
3. **Scalability**: Better horizontal scaling for large files
4. **Document Structure**: Natural fit for storing files with metadata
5. **GridFS Ready**: Can be upgraded to GridFS for large files later
6. **No Joins**: Files and metadata stored together
7. **JSON-like Structure**: Easy to work with in web applications

## File Types Supported
- Images: JPG, JPEG, PNG, GIF
- Documents: PDF, DOC, DOCX, TXT
- Maximum file size: 5MB (configurable)

## Security Features
- Password hashing using PHP's password_hash()
- File type validation
- File size limits
- Input sanitization
- SQL injection prevention (not applicable but good practice)
