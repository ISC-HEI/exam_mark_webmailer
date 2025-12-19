<div align="center">

# Exam mark mailer

![Language](https://img.shields.io/badge/language-php-red)
![Last Commit](https://img.shields.io/github/last-commit/ISC-HEI/exam_mark_webmailer)
![License Apache](https://img.shields.io/badge/License-Apache-red)
![Made with expo](https://img.shields.io/badge/made_with-Laravel-white?logo=laravel)
![Numbers of issues](https://img.shields.io/github/issues/ISC-HEI/exam_mark_webmailer)
![Release version](https://img.shields.io/github/v/release/ISC-HEI/exam_mark_webmailer)
![Release date](https://img.shields.io/github/release-date/ISC-HEI/exam_mark_webmailer)

**Exam Mark Mailer** is a simple and user-friendly web interface for sending exam marks to students via email. It allows teachers to enter marks manually or import them from CSV, and send personalized emails to each student.
</div>

## Table Of Contents
- [Features](#features)
- [Installation](#installation)
    - [Configuration](#configuration)
- [Usage](#usage)
    - [Steps to send marks](#steps-to-send-marks)
    - [Available Variables](#available-variables)
    - [Available Shortcuts](#available-shortcuts)
- [License](#license)

## Features
- Add or remove students dynamically.
- Load students and marks from a CSV file.
- Customize the email message with many variables.
- Reset message to default template.
- Send personalized emails to multiple students at once.
- Supports Markdown formatting.
- Many shortcuts for fast editing.
- search bar from 5 students.

## Installation
Clone the git directory
```bash
git clone https://github.com/ISC-HEI/exam_mark_webmailer.git
cd exam_mark_webmailer
```
Copy .env.example to .env and configure your environment variables:
```bash
cp .env.example .env
# php artisan key:generate
```
> You can run `php artisan key:generate` if you want to add some functions. If you just want to use the program as is, a key is provided in the `.env.example` file.

Create the docker image and start
```docker
docker build -t exam-mark-webmailer:1 .
docker run -p 8000:8000 exam-mark-webmailer:1
```

### Configuration
You need to configure your mail settings in the `.env` file to enable sending emails. Example configuration using Gmail SMTP:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=test@gmail.com
MAIL_PASSWORD="aaaa bbbb cccc dddd"
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=test@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```
> Note: For Gmail, you may need to create an App Password or enable “Less secure app access”.

## Usage
You can start the web page
```bash
php artisan serve
# Or via docker
docker run -p 8000:8000 exam-mark-webmailer:1
```

### Steps to send marks:

1. Fill in the Course Name and Exam Name.
2. Add student details manually or load from a CSV file.
3. Customize the email message using the available variables
> Tip : You can type **[** to get the variables 
4. Click Send Emails.

*Optional: use Reset Message to restore the default template.*

### Available Variables
Here is a list of available variables
|   **Variable**  |     **Description**     |
|:---------------:|:-----------------------:|
| [STUDENT_NAME]  | The name of the student |
| [STUDENT_MARK]  | The mark of the student |
| [COURSE_NAME]   | The name of the course  |
| [EXAM_NAME]     | The name of the exam    |
| [CLASS_AVERAGE] | The class average       |
| [MY_EMAIL]      | Your email (teacher)    |

### Available Shortcuts
Here is a list of available shortcuts

| **Shortcut** |       **Action**       |
|:------------:|:----------------------:|
| ALT + ENTER  | Send the marks         |
| ALT + A      | Add a student          |
| ALT + M      | Focus the message area |
| ALT + R      | Reset the message      |

## License
The current License is Apache version 2.0, you can see it in the [LICENSE](LICENSE) file.