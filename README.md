<p align="center">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640" width="50" height="50" fill="#EF233C">
    <path d="M216.3 124C262.5 44 378 44 424.2 124L461.5 188.6L489.2 172.6C497.6 167.7 508.1 168.4 515.8 174.3C523.5 180.2 526.9 190.2 524.4 199.6L500.9 287C497.5 299.8 484.3 307.4 471.5 304L384.1 280.6C374.7 278.1 367.8 270.2 366.5 260.6C365.2 251 369.9 241.5 378.3 236.7L406 220.7L368.7 156.1C347.1 118.8 293.3 118.8 271.7 156.1L266.4 165.2C257.6 180.5 238 185.7 222.7 176.9C207.4 168.1 202.2 148.5 211 133.1L216.3 124zM513.7 343.1C529 334.3 548.6 339.5 557.4 354.8L562.7 363.9C608.9 443.9 551.2 543.9 458.8 543.9L384.2 543.9L384.2 575.9C384.2 585.6 378.4 594.4 369.4 598.1C360.4 601.8 350.1 599.8 343.2 592.9L279.2 528.9C269.8 519.5 269.8 504.3 279.2 495L343.2 431C350.1 424.1 360.4 422.1 369.4 425.8C378.4 429.5 384.2 438.3 384.2 448L384.2 480L458.8 480C501.9 480 528.9 433.3 507.3 396L502 386.9C493.2 371.6 498.4 352 513.7 343.2zM115 299.4L87.3 283.4C78.9 278.5 74.2 269.1 75.5 259.5C76.8 249.9 83.7 242 93.1 239.5L180.5 216C193.3 212.6 206.5 220.2 209.9 233L233.3 320.4C235.8 329.8 232.4 339.7 224.7 345.7C217 351.7 206.5 352.3 198.1 347.4L170.4 331.4L133.1 396C111.5 433.3 138.5 480 181.6 480L192.2 480C209.9 480 224.2 494.3 224.2 512C224.2 529.7 209.9 544 192.2 544L181.6 544C89.3 544 31.6 444 77.8 364L115 299.4z"/>
  </svg>
</p>

<h1 align="center">
  <span style="color: #EF233C;">ReMarket</span>
</h1>

<p align="center">
  A platform where you can <b>buy and sell pre-owned products</b> easily and securely.<br>
  Built using <b>PHP</b>, <b>MySQL</b>, and <b>HTML/CSS</b>.
</p>

---

<h1 align="center">
  <span style="color: #EF233C;">Preview</span>
</h1>

<p align="center">
  <img src="uploads/69103f113e21c_1762672401.jpg" alt="ReMarket Preview" width="600" style="max-width: 100%; border-radius: 10px; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
</p>

---

> ## 🌟 Features

- User authentication (signup/login using sessions)
- Sell products with images
- Browse and buy listed products
- Order management and delivery confirmation
- User profiles and item listings

> ## Project structure

- `assets/css/style.css` — main stylesheet
- `buy_product.php` — product purchase logic
- `config.php` — database configuration and helpers
- `confirm_delivery.php` — delivery confirmation
- `database.sql` — database schema (create/import)
- `home.php` — main/home page
- `index.php` — landing page
- `login.php`, `signup.php`, `logout.php` — auth
- `orders.php` — order listing and management
- `profile.php` — user profile
- `sell_product.php` — product upload form
- `uploads/` — uploaded product images

> ## ⚙️ Installation and setup

1. Clone the repo:

```bash
git clone https://github.com/prawesh12/remarket.git
cd remarket
```

2. Create and import the database

Using `mysql` CLI:

```sql
CREATE DATABASE remarket;
USE remarket;
SOURCE database.sql;
```

Or import `database.sql` via phpMyAdmin.

3. Configure the database connection

Open `config.php` and update the DB constants to match your environment. Example local settings:

```php
// config.php (example local settings)
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'remarket');
```

If you use a remote host (e.g. InfinityFree) keep in mind DNS/hostname propagation may take time — use the hostname provided by your host control panel.

4. Deploy to your web server

Place the project folder into your web server document root:

- XAMPP: `htdocs/remarket`
- WAMP: `www/remarket`
- LAMP: `/var/www/html/remarket`

Start Apache and MySQL, then open:

```
http://localhost/remarket/
```

> ## Requirements

- PHP 7.4+ with mysqli enabled
- MySQL / MariaDB
- Apache or Nginx (or a local PHP server)

> ## Notes

- For development you can use a local MySQL server to avoid remote-host DNS issues (e.g. InfinityFree). Keep production credentials out of version control.
- Uploaded images are stored in `uploads/`. Ensure this folder is writable by the web server.

> ## License

MIT — feel free to use and modify for learning or projects.

---

> ## Contributors

*1). Ravi Kumar Sah
2). Ankit Chaudhary
3). Prawesh Mandal*

---
