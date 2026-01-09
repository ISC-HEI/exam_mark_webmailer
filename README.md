<div align="center">

# Exam Mark Mailer

![Language](https://img.shields.io/badge/language-php-red)
![Last Commit](https://img.shields.io/github/last-commit/ISC-HEI/exam_mark_webmailer)
![License](https://img.shields.io/badge/License-Apache_2.0-blue)
![Laravel](https://img.shields.io/badge/Made_with-Laravel-red?logo=laravel)
![Numbers of issues](https://img.shields.io/github/issues/ISC-HEI/exam_mark_webmailer)
![Release version](https://img.shields.io/github/v/release/ISC-HEI/exam_mark_webmailer)
![Release date](https://img.shields.io/github/release-date/ISC-HEI/exam_mark_webmailer)

**A simple, user-friendly web interface for sending exam marks to students via email.** It allows teachers to enter marks manually or import them from CSV, and send personalized emails to each student.

[Contribute](https://github.com/ISC-HEI/exam_mark_webmailer/pulls) • [Report Bug](https://github.com/ISC-HEI/exam_mark_webmailer/issues) • [Request Feature](https://github.com/ISC-HEI/exam_mark_webmailer/issues)

</div>

---

![Main Interface](/img/students_page.png)

## Table Of Contents
- [Features](#features)
- [Installation](#installation)
    - [Docker (Recommended)](#option-a-docker-recommended)
    - [Local Setup](#option-b-local-setup)
- [Configuration](#configuration)
- [Usage](#usage)
    - [Importing Data](#importing-data-csv)
    - [Variables & Shortcuts](#available-variables)
- [Screenshots](#screenshots)
- [License](#license)

## Features
* **Dynamic Management:** Add or remove students dynamically.
* **Bulk Import:** Load students and marks via CSV file.
* **Templating:** Customize emails with dynamic variables (e.g., `[STUDENT_MARK]`) and Markdown support.
* **Shortcuts:** Keyboard shortcuts for power users for fast editing.
* **Statistics:** View exam stats (Median, Average, Success Rate) and export as PDF.
* **Search:** Smart search bar activates automatically when there are >5 students.
* **Dark Mode:** Fully supported dark theme.

## Installation

### Option A: Docker (Recommended)
1.  **Clone the repository**
    ```bash
    git clone https://github.com/ISC-HEI/exam_mark_webmailer.git
    cd exam_mark_webmailer
    ```
2.  **Environment Setup**
    Copy the example environment file and configure it:
    ```bash
    cp .env.example .env
    ```
    *See [Configuration](#configuration) below for mail settings.*

3.  **Build and Run**
    ```bash
    docker compose up -d --build
    docker compose exec app php artisan key:generate # Optional
    ```
    The website will be available at [http://127.0.0.1:8000](http://127.0.0.1:8000).

### Option B: Local Setup
**Prerequisites:** PHP 8.1+, Composer, Node.js.

1.  **Install Dependencies**
    ```bash
    composer install
    npm install && npm run dev
    ```
2.  **Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate # Optional
    ```
3.  **Run**
    ```bash
    php artisan serve
    ```

## Configuration
You need to configure your mail settings in the `.env` file to enable sending emails.

**Example (Gmail SMTP):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD="your-app-password"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```
> **Note:** For Gmail, you must use an **App Password** if 2FA is enabled.

## Usage

### Steps to send marks
1.  Fill in the **Course Name**, **Exam Name**, and your **Email**.
2.  Add student details manually or [Import from CSV](#importing-data-csv).
3.  Customize the email message.
    > Tip: Type **[** in the message box to see available variables.
4.  Click **Send Emails**.

### Importing Data (CSV)
To import students, your CSV file must use **semicolons (;)** as delimiters.  
**Format:** `name;email;mark`

```csv
John Doe;john@example.com;5.5
Jane Smith;jane@example.com;4.0
```

### Available Variables
| Variable | Description |
| :--- | :--- |
| `[STUDENT_NAME]` | The name of the student |
| `[STUDENT_MARK]` | The mark of the student |
| `[COURSE_NAME]` | The name of the course |
| `[EXAM_NAME]` | The name of the exam |
| `[CLASS_AVERAGE]` | The class average |
| `[MEDIAN]` | The median of the exam |
| `[SUCCESS_RATE]` | The success rate of the exam |
| `[MY_MAIL]` | Your email (teacher) |

### Available Shortcuts
| Shortcut | Action |
| :---: | :--- |
| `ALT` + `ENTER` | Send the marks |
| `ALT` + `A` | Add a student |
| `ALT` + `M` | Focus the message area |
| `ALT` + `R` | Reset the message |
| `ALT` + `S` | Toggle between tabs |
| `ALT` + `I` | Toggle incognito mode |
| `ALT` + `T` | Toggle theme (Dark/Light) |
| `ALT` + `P` | Export statistics as PDF |
| `ALT` + `F` | Toggle full screen (Student edition) |
| `ALT` + `V` | Show message preview |

## Screenshots

**Statistics Page**
![Statistics page](/img/statistics_page.png)

**Send Confirmation**
![Send confirmation](/img/send_confirmation.png)

**Dark Mode**
![Dark mode](/img/dark_mode.png)

## License
This project is licensed under the Apache 2.0 License - see the [LICENSE](LICENSE) file for details.