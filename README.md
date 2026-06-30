
#  Smart Parking System

Smart Parking System is a comprehensive web-based application designed to help users view real-time parking slot availability and book spots instantly. The project features two parallel architectures: a **Pure Frontend (Local Storage-based)** version for quick simulation and a **Full-Stack (PHP + MySQL-backend)** version for production-ready database management.

---

##  Key Features

- **Dual-Authentication Layer:** Secure Signup and Login workflows implemented via both PHP Sessions and JavaScript LocalStorage verification[cite: 2, 4, 12, 13, 16].
- **Interactive Multi-Floor Grid:** A dynamic visual layout showcasing 100 total slots spread across Ground Floor (A) through Third Floor (D) with 25 spots per floor[cite: 9, 15].
- **Live Color-Coded Status:** Instant visibility where Available slots are colored Green and Booked slots are highlighted in Red[cite: 2, 15, 18, 19].
- **Automated Booking System:** Pre-fills user profiles (Vehicle Number, Contact), filters real-time available allocations, and calculates costs dynamically based on the duration[cite: 1, 2, 3].
- **Real-Time Analytics Dashboard:** Displays live counts of Total Slots, Currently Booked, and Available Slots on the landing view[cite: 8, 9, 10, 15].
- **Integrated Customer Support:** An embedded contact desk letting users push inquiries, bug updates, or complaints directly to the administration[cite: 6, 7].

---

## Tech Stack Used

- **Frontend:** HTML5, CSS3 (Custom responsive layouts & animations), JavaScript (ES6+ DOM manipulation and structured state object handling)[cite: 1, 2, 15, 18].
- **Backend:** PHP (Object-Oriented PDO database layer for secure parameter binding)[cite: 3, 5, 12].
- **Database:** MySQL (Relational entity mapping with explicit foreign key cascading and data integrity)[cite: 20].

---

##  Project Directory Structure

Here is the functional breakdown of all the files in this repository:

```text
├── config.php          # Initializes database PDO tracking and server-side session controls
├── smart_parking.sql   # Complete database schema including users, parking_slots, bookings, and messaging tables
│
├── index.php           # PHP Login portal backed by server-side password verification
├── home.php            # Main user dashboard aggregating real-time server database metrics
├── booking.php         # Server-side transaction script validating slot availability and updating states
├── parking_slots.php   # Synchronizes grid items directly with active MySQL slot rows
├── contact.php         # Backend contact engine logging client messages into the db cluster
├── logout.php          # Destroys backend user sessions and forces index.php rerouting
│
├── index.html          # Static frontend entry point utilizing browser memory for sessions
├── signup.html         # Form interface collecting primary user profiles for storage registers
├── home.html           # Mock dashboard using timed triggers to fetch application telemetry
├── booking.html        # Interactive slot selection window complete with baseline rule tables
├── parking_slots.html  # Standard client layout rendering the multi-tier grid blocks
├── contact.html        # Client support panel housing baseline office business info
│
├── common.js           # Shared utility tracking client verification and script execution hooks
├── login.js            # Evaluates static credential arrays and displays fallback password alerts
├── signup.js           # Verifies string constraints and pushes user structures into browser context
├── home.js             # Automatically builds a 100-slot simulation deck inside local registers
├── parking_slots.js    # Standard grid generator creating interactive vectors for floors A, B, C, and D
├── booking.js          # Pre-fills inputs and handles clients trying to assign static variables
│
└── styles.css          # Core stylesheet containing global dark/light assets, flex layouts, and responsive queries

```

---

## Database Setup (For PHP Version)

1. Boot up your local development stack server control panel (**XAMPP / WAMP**) and start both **Apache** and **MySQL**.
2. Open your web browser and navigate to `http://localhost/phpmyadmin/`.
3. Create a brand-new database named **`smart_parking`**.
4. Click on your newly created database, select the **Import** tab on the upper menu, choose the **`smart_parking.sql`** file from your project directory, and click **Go**.
5. *Note:* If your local MySQL setup includes a custom root password, update the `$pass` parameter inside your `config.php` file accordingly.

---

## How to Run the Project

### 1. Launching the Full-Stack Version (PHP + MySQL)

* Move or copy the entire project directory inside your local server web-root (e.g., XAMPP's **`htdocs/smart-parking-system/`**).
* Open your browser and type the local deployment address:
```text
http://localhost/smart-parking-system/index.php

```


* Register a test account to fully evaluate backend data writing and state transitions.

### 2. Launching the Static Frontend Version (JavaScript + LocalStorage)

* No server installation or configuration is required for this route.
* Simply head into your local project directory and double-click the **`index.html`** file to initialize the application inside your browser.
* This version tracks assets completely within your individual browser profile's `LocalStorage`.

---

## Database Tables Architecture

* **`users`**: Securely keeps track of names, unique emails, active phone contacts, profiles, and associated vehicle plates.
* **`parking_slots`**: Manages alphanumeric references (A1 to D25), matching floor categories, and current occupation properties.
* **`bookings`**: Stores active reservations mapping user logs directly to precise durations and historical timestamps.
* **`contact_messages`**: Centralized log organizing customer outreach tickets directly by user identity tracking.

---

## Contribution & License

This platform is developed strictly for educational applications. Feel free to fork this repository, submit issues, or open pull requests to scale the feature set (e.g., adding automated payment processing APIs, QR code scanners, or administrative moderation metrics).

