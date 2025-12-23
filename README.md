<div align="center">

# ⚓ Battleship (Bataille Navale)

![PHP](https://img.shields.io/badge/Language-PHP_8-purple?style=for-the-badge&logo=php&logoColor=white)
![SQL](https://img.shields.io/badge/Database-MySQL-orange?style=for-the-badge&logo=mysql&logoColor=white)
![School](https://img.shields.io/badge/School-Coda-blue?style=for-the-badge)

<p>
  <strong>A naval strategy game developed with PHP and SQL.</strong><br>
  Manage your fleet, fire at the enemy, and climb the leaderboard!
</p>

</div>

---

## 📋 About The Project

This project was developed as part of the **Coda** curriculum. The goal was to create a functional browser-based game without using frameworks, relying solely on **native PHP** and a **MySQL database**.

The challenge was to manage the game state (whose turn it is, where ships are located, hits and misses) strictly through server-side logic and database queries.

---

## ⚙️ Key Features

Here is what the project includes:

* **🔐 User Authentication:** Secure registration and login system (sessions).
* **🎮 Game Loop:** Turn-based mechanics managed via PHP logic.
* **💾 Data Persistence:** Every move is saved in the database.
* **📊 Leaderboard:** A ranking system displaying the best players based on their scores.
* **🎨 Responsive Design:** A clean interface to play on different screen sizes.

---

## 🛠️ Tech Stack

| Technology | Usage |
| :--- | :--- |
| **PHP 8** | Backend logic, session management, and routing. |
| **MySQL** | Database to store users, games, and shots. |
| **HTML5 / CSS3** | Structure and styling of the game board. |
| **Git** | Version control. |

---

## 🗄️ Database Structure

The project relies on a relational database. The main logic revolves around these entities:

1.  **Users:** Stores username and encrypted passwords.
2.  **Games:** Tracks the status of a match (Active, Finished, Winner).
3.  **Ships:** Specific coordinates of the player's fleet.
4.  **Shots/Moves:** Logs every attack (X, Y coordinates) and the result (Hit/Miss).

---

## 🚀 How to Run Locally

To test this project on your machine, you need a local server like **XAMPP**, **WAMP**, or **MAMP**.

### 1. Clone the repository
Open your terminal and run:
```bash
git clone [https://github.com/kenzotrindade/Bataille_Navale_Coda.git](https://github.com/kenzotrindade/Bataille_Navale_Coda.git)

## 2️⃣ Move Files

Move the project folder into your local server root directory:

- **XAMPP:** `htdocs/`
- **WAMP:** `www/`
- **MAMP:** `htdocs/`

---

## 3️⃣ Setup Database

1. Open **phpMyAdmin**  

http://localhost/phpmyadmin


2. Create a new database named:

"bataille_navale"


3. Import the `.sql` file located at the root of the project folder.

---

## 4️⃣ Configure Database Connection

Open the PHP file responsible for the database connection  
(e.g. `connect.php`, `db.php`, or `config.php`) and ensure the credentials match your local setup:

```php
<?php
$host = "localhost";
$dbname = "bataille_navale";
$username = "root";
$password = ""; // XAMPP/WAMP | use "root" for MAMP

5️⃣ Play the Game 🎮

Open your browser and go to:

http://localhost/Bataille_Navale_Coda
