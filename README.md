# CareerForge — Modern Job Portal & Career Portfolio Platform

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%20|%208.0%20|%208.1%20|%208.2-777bb4?logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3-7952B3?logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![jQuery](https://img.shields.io/badge/jQuery-3.6-0769AD?logo=jquery&logoColor=white)](https://jquery.com/)
[![FontAwesome](https://img.shields.io/badge/FontAwesome-6.5-528DD7?logo=font-awesome&logoColor=white)](https://fontawesome.com/)

**CareerForge** (formerly Order Tech Careers) is a full-featured, responsive web-based Job Portal and Dynamic Career Portfolio Management system built with **PHP**, **MySQL**, **PDO**, and **Bootstrap 5**. It empowers job seekers to discover exciting job opportunities, build interactive professional portfolios with resumes and certificates, and provides administrators with a robust back-office control panel to manage candidates, applications, content, and system workflows.

---

## Table of Contents

- [Key Features](#key-features)
  - [Job Seeker Portal](#job-seeker-portal)
  - [Administrative Dashboard](#administrative-dashboard)
- [Architecture & Tech Stack](#architecture--tech-stack)
- [Directory Structure](#directory-structure)
- [Getting Started / Local Installation](#getting-started--local-installation)
  - [Prerequisites](#prerequisites)
  - [Step 1: Clone the Repository](#step-1-clone-the-repository)
  - [Step 2: Database Setup](#step-2-database-setup)
  - [Step 3: Database Connection Configuration](#step-3-database-connection-configuration)
  - [Step 4: Launch Application](#step-4-launch-application)
- [Default Login Credentials](#default-login-credentials)
- [Database Schema Reference](#database-schema-reference)
- [Security & Best Practices](#security--best-practices)
- [Contributing](#contributing)
- [License](#license)
- [Author & Acknowledgments](#author--acknowledgments)

---

## Key Features

### Job Seeker Portal
- **Authentication & Security**:
  - Secure account registration and session-based login.
  - Password recovery workflow (`f_password.php`) with token handling.
- **Dynamic Portfolio & Profile Builder**:
  - Multi-step portfolio creator with auto-save capabilities (`autosave_profile.php`).
  - Education history management (Degree, Institution, Passing Year, CGPA/Percentage).
  - Certification uploads with document verification support (`uploads/certificates`).
  - Resume upload and management (`uploads/resumes`).
  - Profile and cover image personalization (`uploads/profiles`, `uploads/covers`).
- **Interactive Job Search & Categories**:
  - Filter and browse job listings by industry categories and subcategories (`get_categories.php`, `get_subcategories.php`).
  - Dynamic skill picker (`get_skills.php`).
  - Detailed job requirement breakdowns.
- **Content & Support Pages**:
  - Informative Company & About Us page.
  - Team directory and customer testimonials.
  - Tech & career advice blog with full article reader (`blog.php`, `blog_details.php`).
  - Contact Us form with support routing.

### Administrative Dashboard (`/admin`)
- **Executive Analytics**:
  - Live overview cards tracking total registered users, portfolios submitted, and active blog posts.
- **Candidate & Portfolio Management**:
  - Full candidate portfolio registry (`portfolio_list.php`).
  - In-depth individual profile inspection (`view_portfolio.php`).
  - Secure document & resume download gateway (`download_file.php`).
  - Portfolio deletion and status updates.
- **User Management**:
  - View all registered job seekers (`user_list.php`).
  - Modify user credentials and status (`edit_user.php`).
  - Soft/hard user deletion with cascading safeguards (`delete_user.php`).
- **Blog & Content Publishing**:
  - Publish rich blog posts with featured images and categorized tags (`add_blog.php`).
  - Full CRUD management of existing blog articles (`blog_list.php`).

---

## Architecture & Tech Stack

| Layer | Technologies |
| :--- | :--- |
| **Backend** | PHP 7.4+ / 8.x (Procedural & Object-Oriented with PDO and MySQLi) |
| **Database** | MySQL 5.7+ / MariaDB 10.4+ |
| **Frontend UI** | HTML5, CSS3, JavaScript (ES6+), Bootstrap 5.3, jQuery 3.6 |
| **Styling & Icons** | FontAwesome 6.5, Owl Carousel 2, Custom CSS Grid/Flexbox |
| **Web Server** | Apache (XAMPP / WampServer / LAMP / Laragon) or Nginx |

---

## Directory Structure

```plaintext
CareerForge/
├── admin/                        # Administrative Control Panel
│   ├── assets/                   # Admin CSS, JS, and vendor plugins
│   ├── images/                   # Admin visual assets
│   ├── uploads/                  # Admin file uploads (blogs, media)
│   ├── add_blog.php              # Create blog posts
│   ├── blog_list.php             # Manage blog posts
│   ├── connection.php            # Admin database connection (MySQLi)
│   ├── dashboard.php             # Main admin dashboard with metrics
│   ├── delete_portfolio.php      # Remove portfolio submission
│   ├── delete_user.php           # Remove user account
│   ├── download_file.php         # Secure resume/certificate downloader
│   ├── edit_user.php             # Update user details
│   ├── header.php / footer.php   # Admin layout templates
│   ├── login.php / logout.php    # Admin authentication
│   ├── portfolio_list.php        # Portfolios registry
│   ├── user_list.php             # User management table
│   └── view_portfolio.php        # Candidate detailed portfolio view
├── assets/                       # Public frontend assets
│   ├── css/                      # Custom stylesheets (style.css, owl.css, fontawesome.css)
│   ├── images/                   # Static site graphics and banners
│   └── js/                       # Interactive JavaScript and carousels
├── uploads/                      # Candidate uploaded documents & media
│   ├── certificates/             # Uploaded course & degree certificates
│   ├── covers/                   # Profile cover banner images
│   ├── portfolios/               # Portfolio project screenshots
│   ├── profiles/                 # User avatar images
│   └── resumes/                  # Attached resumes / CVs (PDF/DOC)
├── vendor/                       # Bundled frontend libraries (Bootstrap, jQuery)
├── about.php / about.html        # About Us page
├── action.php                    # Asynchronous action processor
├── add_portfolio.php             # Multi-step portfolio builder
├── autosave_profile.php          # Real-time profile autosave handler
├── blog.php / blog_details.php   # Career articles & blog system
├── categories.php                # Job categories catalogue
├── connection.php                # Main database connection (PDO)
├── connection.example.php        # Template database connection config
├── contact.php / contact.html    # Inquiries & Contact form
├── create_profile.php            # Initial profile onboarding
├── f_password.php                # Forgot/Reset password workflow
├── get_categories.php            # Dynamic category endpoint
├── get_skills.php                # Skills autocompletion endpoint
├── get_subcategories.php         # Subcategory filter endpoint
├── header.php / footer.php       # Shared frontend layout components
├── index.php                     # Smart entry router (session-aware)
├── index-2.php                   # Authenticated member home dashboard
├── index.html                    # Public visitor landing page
├── job-details.html              # Job posting description template
├── jobs.html                     # Job listings browser
├── job_portal.sql                # Complete MySQL database schema & seed data
├── login.php / logout.php        # Candidate authentication
├── portfolio.css / portfolio.js  # Dedicated portfolio dynamic builder styles & scripts
├── profile.php                   # Candidate profile view
├── register.php                  # Candidate account registration
├── submit_portfolio.php          # Portfolio form submission processor
├── team.php / testimonials.php   # Company team and testimonials
├── terms.php                     # Terms & conditions page
├── .gitignore                    # Git file exclusion rules
├── LICENSE                       # MIT Open Source License
├── CONTRIBUTING.md               # Contribution guidelines
├── CODE_OF_CONDUCT.md            # Contributor covenant
└── README.md                     # Project documentation
```

---

## Getting Started / Local Installation

### Prerequisites
Make sure you have an Apache & MySQL environment installed:
- [XAMPP](https://www.apachefriends.org/) (Recommended for Windows/macOS/Linux)
- [WampServer](https://www.wampserver.com/) or [Laragon](https://laragon.org/)
- PHP 7.4 or higher (PHP 8.0, 8.1, 8.2 supported)
- MySQL 5.7+ or MariaDB 10.4+

---

### Step 1: Clone the Repository
Clone the repository into your local server root:

```bash
# For XAMPP on Windows:
cd C:\xampp\htdocs

# Clone repository
git clone https://github.com/ajayrathod-dev/Careerforge-A-Job-Portal.git CareerForge
cd CareerForge
```

---

### Step 2: Database Setup
1. Start **Apache** and **MySQL** from your XAMPP Control Panel.
2. Open your browser and navigate to **phpMyAdmin**:
   ```
   http://localhost/phpmyadmin/
   ```
3. Create a new database named `job_portal` with collation `utf8mb4_unicode_ci`.
4. Click on the newly created `job_portal` database, go to the **Import** tab.
5. Click **Choose File**, select `job_portal.sql` from the root directory of this project, and click **Import**.

*Alternatively, using MySQL command-line:*
```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS job_portal;"
mysql -u root -p job_portal < job_portal.sql
```

---

### Step 3: Database Connection Configuration
Verify the credentials in `connection.php` and `admin/connection.php`:

#### `connection.php` (Frontend PDO Connection)
```php
$host = 'localhost';
$dbname = 'job_portal';
$username = 'root';    // Default XAMPP username is 'root'
$password = '';        // Default XAMPP password is blank/empty
```

#### `admin/connection.php` (Admin MySQLi Connection)
```php
$DB_SERVER = "localhost";
$DB_NAME = "job_portal";
$username = "root";    // Default XAMPP username is 'root'
$password = "";        // Default XAMPP password is blank/empty
```

> [!TIP]
> If your local MySQL setup has a custom username or password, update both files accordingly or reference `connection.example.php`.

---

### Step 4: Launch Application
Open your web browser and navigate to:
- **Public Website / Portal**: [http://localhost/CareerForge/](http://localhost/CareerForge/)
- **Member Dashboard**: [http://localhost/CareerForge/index-2.php](http://localhost/CareerForge/index-2.php)
- **Admin Control Panel**: [http://localhost/CareerForge/admin/login.php](http://localhost/CareerForge/admin/login.php)

---

## Default Login Credentials

### Administrator Account
- **Admin URL**: `http://localhost/CareerForge/admin/login.php`
- **Username**: `admin`
- **Password**: `1234`

### Candidate / Job Seeker Account
- Create a new account anytime via [http://localhost/CareerForge/register.php](http://localhost/CareerForge/register.php)
- Demo accounts seeded in `job_portal.sql` can also be used directly.

---

## Database Schema Reference

The database consists of structured relational tables defined in `job_portal.sql`:

| Table | Purpose |
| :--- | :--- |
| `admin` | Administrative credentials and system access flags |
| `job_seekers` | Candidate user profiles, emails, passwords, and bios |
| `portfolios` | Main candidate portfolio submissions, titles, and summaries |
| `portfolio_education` | Candidate educational institutions, degrees, and graduation years |
| `portfolio_certificates` | Uploaded certifications, issuance dates, and file paths |
| `blogs` | System articles, guides, thumbnails, and author information |
| `tblcategory` | Job industries and professional category taxonomy |
| `users` | Secondary/system user records |

---

## Security & Best Practices

- **Upload Sanitization**: The upload directories (`uploads/`) are protected via `.gitignore` to prevent committing candidate personal data, while `.gitkeep` files maintain directory structure.
- **SQL Injection Prevention**: Prepared statements with parameter binding are utilized via PHP PDO and MySQLi.
- **Session Authentication**: Pages check `$_SESSION['user_id']` and redirect unauthenticated requests to login.

---

## Contributing

Contributions are welcome! Please follow these steps:
1. Review our [Contributing Guidelines](CONTRIBUTING.md) and [Code of Conduct](CODE_OF_CONDUCT.md).
2. Fork the repository.
3. Create your feature branch (`git checkout -b feature/AmazingFeature`).
4. Commit your changes (`git commit -m 'feat: Add some AmazingFeature'`).
5. Push to the branch (`git push origin feature/AmazingFeature`).
6. Open a Pull Request.

---

## License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## Author & Acknowledgments

- **Developed by**: [Ajay Rathod](https://github.com/ajayrathod-dev)
- **GitHub Repository**: [Careerforge-A-Job-Portal](https://github.com/ajayrathod-dev/Careerforge-A-Job-Portal)
- Built with :heart: for empowering job seekers and developers worldwide.
