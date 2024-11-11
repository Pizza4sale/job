# Job Application Tracker

This project is a Job Application Tracker built using PHP. It allows users to upload, view, and manage documents related to their job applications. The system features document upload functionality with various file type support (PDF, DOCX, JPG, PNG) and allows users to view or download documents from the system.

## Features
- **Document Upload**: Upload job-related documents such as resumes, cover letters, etc.
- **View Documents**: View uploaded documents directly in the browser (PDF, JPG, PNG).
- **Download Documents**: Option to download unsupported file types.
- **Secure User Access**: Users can only access their own documents based on their login session.

## Requirements
- PHP 7.0 or higher
- MySQL or MariaDB
- Apache or Nginx web server
- PHP Extensions: `mysqli`, `fileinfo`

## Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/Pizza4sale/job.git
   ```

2. **Set up the database**:
   - Import the database schema from the `database.sql` file (or similar).
   - Update the database credentials in the `includes/head.php` file.
   
   ```php
   $mysqli = new mysqli('localhost', 'root', '', 'your_database_name');
   if ($mysqli->connect_error) {
       die("Connection failed: " . $mysqli->connect_error);
   }
   ```

3. **Configure file permissions**:
   Ensure that the `uploads/` directory is writable by the web server:
   ```bash
   chmod 755 uploads/
   ```

4. **Set up the environment**:
   - Ensure you have a working web server (Apache or Nginx).
   - Place the project folder in the web server's root directory.
   - Update the `base_url` and other necessary configuration details in the PHP files.

5. **Start the application**:
   - Open your browser and navigate to the project's index page, e.g., `http://localhost/job/index.php`.

## File Structure

```
/job
    /uploads               # Directory for uploaded documents
    /includes
        head.php           # Database connection and common includes
        navbar.php         # Navigation bar
        sidebar.php        # Sidebar for navigation
        script.php         # JavaScript and footer includes
    manage_documents.php   # Page for managing user documents
    view_document.php      # Page for viewing individual documents
    index.php              # Home page of the application
    database.sql           # SQL script for database setup
    README.md              # Project documentation
```

## How to Use

1. **Upload a Document**:
   - Navigate to the `Upload Document` page from the navigation bar.
   - Select a document (PDF, DOCX, JPG, PNG) from your computer and upload it.
   - Once uploaded, the document will be stored in the `uploads/` directory, and its details will be saved in the database.

2. **View a Document**:
   - Click on the document name in the document management page.
   - The document will be displayed in the browser (for supported file types like PDF and images).
   - Unsupported file types can be downloaded by clicking the provided download link.

3. **Document Management**:
   - The document management page will list all uploaded documents. You can upload, view, and manage documents from this page.

## Contributing

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-name`).
3. Make your changes.
4. Commit your changes (`git commit -am 'Add new feature'`).
5. Push to your forked repository (`git push origin feature-name`).
6. Create a new Pull Request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Acknowledgments
- Thanks to all contributors who made this project possible.
- Inspiration from various PHP tutorials on document management systems.




