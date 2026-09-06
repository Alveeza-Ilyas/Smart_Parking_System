# Smart Parking System 

A web app for checking live parking availability and booking a bay in seconds. The repository ships
**two parallel implementations** that share one look & feel:

| Version | Stack | Best for |
| --- | --- | --- |
| Static demo | HTML + CSS + JavaScript (LocalStorage) | quick demo, no server needed |
| Full stack | PHP 8 (PDO) + MySQL/MariaDB | real accounts, persistent bookings |

<img width="1882" height="797" alt="image" src="https://github.com/user-attachments/assets/b99cc3de-0ffa-42b3-a4ac-887cb2d7b23e" />

<img width="1867" height="720" alt="image" src="https://github.com/user-attachments/assets/4a2bf1b2-2c7e-4943-84c8-a877b9f99811" />

<img width="1867" height="902" alt="image" src="https://github.com/user-attachments/assets/9143fc0f-8fa9-4124-871b-bccfffe4eb1c" />

<img width="1877" height="863" alt="image" src="https://github.com/user-attachments/assets/e4c332af-e2fe-4bed-8641-fd797e2d9e5c" />

<img width="1866" height="887" alt="image" src="https://github.com/user-attachments/assets/182a3ee3-e366-4da9-9059-4ba2b4f8ae4f" />

---

## Key Features

- **Two authentication flows** — PHP sessions for the backend version, LocalStorage checks for the static demo.
- **Multi-floor grid** — 100 slots: Ground (A) → Third floor (D), 25 bays per floor.
- **Live colour coding** — green = available, red = booked, gold outline = booked by *you*.
- **Booking engine** — prefilled profile data, available-slot filtering, and a price that is
  calculated for the chosen duration ($5.00 first hour, $3.00 per extra hour, $50.00 cap per started
  24 h block) on the client *and* re-checked on the server.
- **Analytics dashboard** — total / booked / available counts, occupancy %, recent bookings.
- **Support desk** — the contact form files a ticket in `contact_messages` (or in LocalStorage for the demo).

---

## Tech Stack

- **Frontend:** HTML5, CSS3, vanilla JavaScript (ES5-compatible, no build step, no dependencies).
- **Backend:** PHP 8.x with PDO prepared statements, transactions and CSRF-protected forms.
- **Database:** MySQL / MariaDB (InnoDB, foreign keys, `parking_slots.slot_id` unique).

---

## Project Layout

```text
.
├── index.html          # static entry point -> redirects to views/index.html
├── index.php           # PHP entry point     -> redirects to api/index.php
├── api/                # PHP pages (login, signup, home, slots, booking, contact)
│   ├── config.php      # shim that loads config/config.php + partials.php
│   └── partials.php    # shared header / navbar / footer
├── assets/
│   ├── css/styles.css
│   ├── img/*.svg       # icons + hero backdrop (SVG, no binary assets)
│   └── js/             # shared scripts (common.js, price-calc.js, page scripts)
├── config/
│   ├── config.php      # ** the only file holding DB credentials **+ helpers
│   └── session.php     # hardened session bootstrap
├── database/smart_parking.sql
└── views/              # static LocalStorage version
```

---

## 1. Static (JavaScript + LocalStorage) Version

No installation required — open `index.html` (or `views/index.html`) in a browser.
If your browser blocks `file://` scripts, serve the folder instead:

```bash
php -S localhost:8000     # from the project root
# or:  python3 -m http.server 8000
```

Accounts, slots and bookings live in the browser profile, per origin. Useful debugging helpers:

```js
JSON.parse(localStorage.users);          // registered accounts (demo data - no server)
JSON.parse(localStorage.parkingSlots);   // the 100 bays and their status
JSON.parse(localStorage.bookings);       // booking history for this browser
localStorage.clear();                     // reset the demo
```

---

## 2. Full-Stack (PHP + MySQL) Version

### Database setup

1. Start **Apache** and **MySQL** in the XAMPP / WAMP control panel.
2. Open `http://localhost/phpmyadmin/`, create a database named **`smart_parking`**
   (collation `utf8mb4_general_ci`).
3. Use the **Import** tab, choose `database/smart_parking.sql`, click **Go** —
   or run `mysql -u root -p smart_parking < database/smart_parking.sql`.
4. If your MySQL `root` user has a password, edit the defaults in **`config/config.php`**
   (or export `SMART_PARKING_DB_USER` / `SMART_PARKING_DB_PASS` instead of editing the file).

### Run it

Copy the project into your web root, e.g. `C:\xampp\htdocs\smart-parking-system\`, then open:

```text
http://localhost/smart-parking-system/index.php      → forwards to api/index.php (login)
```

Sign in with the seeded demo account **`test@example.com` / `password123`**, or register a new one.

### Page map

| Page | URL | Notes |
| --- | --- | --- |
| Login | `api/index.php` | redirects to `home.php` when already signed in |
| Sign up | `api/signup.php` | validation + `password_hash()` |
| Dashboard | `api/home.php` | stats, occupancy, greeting |
| Slots | `api/parking_slots.php` | 100-bay grid, click an available bay to book |
| Booking | `api/booking.php` | price preview, transactional booking |
| Contact | `api/contact.php` | writes to `contact_messages` |
| Logout | `api/logout.php` | clears session **and** cookie |

---

## Database Tables

| Table | Purpose |
| --- | --- |
| `users` | name, unique email, phone, vehicle plate, bcrypt password hash |
| `parking_slots` | `slot_id` (A1 … D25, UNIQUE), floor, status, `booked_by`, `booked_at` |
| `bookings` | user + slot + duration, plus the vehicle, phone and total price that were charged |
| `contact_messages` | support tickets linked to the sender |

`bookings.user_id` and `contact_messages.user_id` cascade on delete, `parking_slots.booked_by`
is set to `NULL` when a user disappears, and `bookings.slot_id` is RESTRICTed so booking history
cannot be orphaned.

---

## Troubleshooting

| Symptom | Cause / fix |
| --- | --- |
| "Database connection failed" page | Apache/MySQL not running, database missing, or wrong credentials in `config/config.php`. Re-run the import step. |
| Same page, but MySQL is definitely running | Your MySQL may only listen on TCP. Set `$dbHost` to `127.0.0.1` (or export `SMART_PARKING_DB_HOST=127.0.0.1`) and retry. Set `SMART_PARKING_DEBUG=1` to show the driver's message. |
| Everything is unstyled | The pages load `../assets/css/styles.css`; make sure `assets/` sits next to `api/` and `views/` (i.e. the project root itself is copied, not only the subfolder). |
| Login says invalid password for the demo user | Old import: the seeded row used to store plaintext. Re-import `database/smart_parking.sql`, or register a new account. |
| Slots are ordered A1, A10, A11 … | Intended? No — the SQL sorts by `LENGTH(slot_id)` so numbering reads A1, A2 … A10. If you see the alphabetical order you are running an old copy. |
| "Your form session expired" | The CSRF token lives in the PHP session; re-open the login page. Long idle time also expires the session. |

---

## Notes

- The static demo stores passwords in LocalStorage in plain text — it is a UI prototype, so it
  ships with no real credentials and must not be exposed publicly. The PHP version hashes with
  `password_hash()` and is the one to use for anything real.
- "Forgot Password?" is a demo affordance: the static version only confirms whether an email is
  registered (it never reveals the stored password), and the PHP form has no mail server behind it.
