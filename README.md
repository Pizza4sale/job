# Job Application Tracker

A web-based application that helps users manage their job applications, documents, and personal information. Users can upload and view documents related to their job applications, track their application statuses, and keep detailed records of their job search process.

## Features

- **User Management**: Users can create accounts, update their profiles, and manage their personal information.
- **Job Application Management**: Users can track job applications, including job title, company name, status, salary range, interview details, recruiter information, and more.
- **Document Upload**: Users can upload resumes, cover letters, portfolios, and other related documents. Uploaded documents can be viewed or downloaded.
- **Skills Management**: Users can add, edit, and view their skills along with proficiency levels.
- **Search and Filter**: Ability to search and filter job applications by job title, company, status, and other criteria.

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript (with custom styles)
- **Backend**: PHP
- **Database**: MySQL (MariaDB)
- **Web Server**: Apache (or other suitable server supporting PHP)

## Setup Instructions

### Prerequisites

1. **PHP**: Make sure PHP is installed on your system. The project was developed using PHP 8.2.4.
2. **MySQL / MariaDB**: The database should be running on your system.
3. **Apache Web Server**: Ensure Apache is installed and configured to serve your project files.
4. **phpMyAdmin (optional)**: For easy database management, you can use phpMyAdmin to interact with your database.

### Installation

1. Clone this repository to your local machine:
   ```bash
   git clone https://github.com/your-username/job.git
   ```

2. Set up the database:
   - Import the `job.sql` file from the `sql/` folder into your MySQL/MariaDB database.
   - You can use phpMyAdmin or MySQL Workbench to run the SQL dump and create the required tables.

3. Configure your database connection:
   - Edit the `includes/head.php` file to add your database credentials:
     ```php
     $mysqli = new mysqli("localhost", "root", "password", "job");
     ```

4. Upload the project files to your server's root directory (e.g., `htdocs` for XAMPP, `www` for WAMP).

5. Access the project via your browser:
   ```
   http://localhost/job
   ```

### File Structure

```
/job-application-tracker
|-- /includes
|   |-- head.php          # Database connection and configuration
|   |-- navbar.php        # Navigation bar
|   |-- sidebar.php       # Sidebar
|   |-- script.php        # Includes scripts for the pages
|
|-- /uploads              # Directory to store uploaded documents
|-- /css                  # Stylesheets for the project
|-- /js                   # JavaScript files
|
|-- manage_documents.php  # Page to manage document uploads
|-- view_document.php     # View a specific document
|-- applications.php      # Page for managing job applications
|-- profile.php           # User profile page
|-- index.php             # Main entry point to the application
```

### Database Structure

The database consists of the following tables:

- **`applications`**: Stores job application details such as job title, company name, status, resume, and more.
- **`documents`**: Stores uploaded documents like resumes and cover letters, associated with a user.
- **`users`**: Stores user details including email, password, and profile information.
- **`user_skills`**: Stores skills of users, including skill name and proficiency.

### How to Use

1. **Register/Login**: Users can create an account or log in to the platform.
2. **Manage Applications**: Users can add, update, and track job applications with their respective statuses, dates, and documents.
3. **Upload Documents**: Users can upload documents such as resumes, cover letters, and portfolios. These documents can be viewed or downloaded.
4. **Update Profile**: Users can update their personal information, including contact details, skills, and social media links.
5. **View and Download Documents**: Uploaded documents can be viewed within the browser (PDF, images) or downloaded.

### Future Enhancements

- **Email Notifications**: Send email notifications when a job application status changes or an interview is scheduled.
- **Job Search Integration**: Integrate with external job boards (e.g., LinkedIn, Indeed) to automatically track job applications.
- **User Analytics**: Provide users with insights about their job search, such as the number of applications, interview invitations, etc.

## License

This project is open-source and available under the [MIT License](LICENSE).

---

**Author**: https://www.facebook.com/Pizza4sale/  
**Contact**: 09195431910  

