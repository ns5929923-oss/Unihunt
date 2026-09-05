# 🎓 UniHunt – University Search & Comparison Platform

UniHunt is a web-based educational platform designed to help students discover, search, and compare universities and colleges, with a primary focus on institutions in Gujarat. It provides students with structured university information, comparison tools, career guidance, reviews, and educational blogs to support better higher-education decisions.

---

## 📌 Features

### 🔍 University Search

* Search universities by name, course, stream, location, and type.
* Filter universities based on available criteria.
* View detailed university information.
* Browse universities in Gujarat.

### ⚖️ University Comparison

* Select universities for comparison.
* Compare important details side-by-side.
* Compare courses, fees, location, facilities, and placement information.
* Easy-to-read comparison table.

### 👨‍💼 Admin Panel

* Secure admin login.
* Add new universities.
* Update university information.
* Delete university records.
* Manage university data.

### 📝 Blog Section

* Browse educational articles and admission-related information.
* Admin can add and manage blog posts.
* Provide useful information related to higher education and career decisions.

### 🎨 UI/UX

* Clean and user-friendly interface.
* Responsive design for desktop, tablet, and mobile.
* Simple navigation.
* Card-based university presentation.
* Easy-to-use search and comparison interface.

---

## 🎯 Objectives

* Provide a centralized platform for university discovery.
* Help students find universities according to their preferences.
* Simplify university comparison.
* Provide reliable and structured educational information.
* Help students make better higher-education decisions.
* Highlight universities and educational opportunities in Gujarat.

---

## 🛠️ Technology Stack

| Technology | Purpose                   |
| ---------- | ------------------------- |
| HTML5      | Web page structure        |
| CSS3       | Styling and layout        |
| JavaScript | Client-side functionality |
| Bootstrap  | Responsive UI             |
| PHP        | Backend development       |
| MySQL      | Database management       |
| Apache     | Local web server          |
| XAMPP      | Development environment   |

---

## 🏗️ System Modules

```text
UniHunt
│
├── Home
│
├── University Search
│   ├── Search
│   ├── Filters
│   └── University Details
│
├── University Compare
│   └── Compare Table
│
├── Blog
│   ├── Blog Listing
│   └── Blog Details
│
└── Admin Panel
    ├── Admin Login
    ├── Add University
    ├── Edit University
    ├── Delete University
    └── Manage Blogs
```

---

## 🗄️ Database

UniHunt uses **MySQL** for storing and managing application data.

### Main Tables

* `users` – Stores user information.
* `universities` – Stores university details.
* `courses` – Stores course information.
* `streams` – Stores academic streams.
* `reviews` – Stores student reviews.
* `blogs` – Stores blog posts.
* `admin` – Stores administrator authentication details.

---

## 🚀 Installation & Setup

### 1. Install XAMPP

Install XAMPP and start:

* Apache
* MySQL

### 2. Clone the Repository

```bash
git clone https://github.com/your-username/UniHunt.git
```

Move the project folder into:

```text
C:\xampp\htdocs\
```

### 3. Create Database

Open **phpMyAdmin** and create a database:

```text
unihunt
```

Import the provided SQL database file into the newly created database.

### 4. Configure Database Connection

Update the PHP database configuration according to your local MySQL credentials.

Example:

```php
$host = "localhost";
$username = "root";
$password = "";
$database = "unihunt";

$conn = mysqli_connect($host, $username, $password, $database);
```

### 5. Run the Project

Open your browser and visit:

```text
http://localhost/UniHunt/
```

---

## 🔐 Admin Panel

The administrator can manage the platform through the Admin Panel.

### Admin Functions

* Admin Login
* Add University
* Edit University
* Delete University
* Manage University Information
* Add Blog
* Edit Blog
* Delete Blog

> For security, do not publish real admin credentials in the GitHub repository.

---

## 🔄 Basic System Flow

```text
        Student
           │
           ▼
       UniHunt Home
           │
     ┌─────┼──────────┐
     ▼     ▼          ▼
  Search Compare     Blog
     │     │
     ▼     ▼
 University Comparison
  Details     Table
     │
     ▼
 Higher Education Decision


        Admin
          │
          ▼
     Admin Login
          │
          ▼
    Admin Dashboard
       │       │
       ▼       ▼
 Universities  Blogs
       │
       ▼
 Add / Edit / Delete
```

---

## 🧪 Testing

The project includes testing for the following modules:

* University Search
* University Compare
* Admin Login
* Admin Add University
* Admin Edit/Delete University
* UI/UX
* Compare Table
* Blog Management
* Database Operations

### Example Test Case

| Test Case         | Input                    | Expected Result                 |
| ----------------- | ------------------------ | ------------------------------- |
| University Search | Course + Location        | Matching universities displayed |
| Admin Login       | Valid credentials        | Admin dashboard displayed       |
| Add University    | Valid university details | University successfully added   |
| Compare           | Two universities         | Comparison table displayed      |
| Blog              | Valid blog content       | Blog successfully published     |

---

## 🔮 Future Enhancements

* 🤖 AI-based university recommendation system.
* 🧠 Advanced career and stream counselling.
* 🎓 Scholarship finder.
* 📱 Android/iOS mobile application.
* 🗺️ Interactive university map.
* 👥 Student discussion/community section.
* 📊 Advanced university analytics.
* 🔔 Admission and application notifications.
* 💬 AI-powered counselling chatbot.

---

## 👥 Target Users

* Students after Class 12
* Undergraduate students
* Parents
* Career counsellors
* Educational institutions
* University administrators

---

## 🌟 Project USP

The primary USP of UniHunt is its **regional focus on Gujarat combined with university search and comparison functionality**. Instead of requiring students to collect information from multiple sources, UniHunt brings important university information together in one platform.

---

## 📄 Project Documentation

The project documentation covers:

1. Acknowledgement
2. Introduction
3. Organization Profile
4. Existing System
5. Problem Areas of Existing System
6. Need for the New System
7. Proposed System
8. System Flow Diagram
9. UML Diagrams
10. Data Dictionary
11. User Interface Screens
12. Reports
13. Test Plan and Test Cases
14. Future Enhancements
15. References / Bibliography

---

## 📚 References

* PHP Documentation
* MySQL Documentation
* HTML Documentation
* CSS Documentation
* JavaScript Documentation
* Bootstrap Documentation

---

## 👩‍💻 Project

**Project Name:** UniHunt
**Project Type:** Educational Web Application
**Backend:** PHP
**Database:** MySQL
**Frontend:** HTML, CSS, JavaScript, Bootstrap
**Focus:** University Search, Comparison & Higher-Education Guidance

---

## 📜 License

This project is developed for **academic/educational purposes**.
