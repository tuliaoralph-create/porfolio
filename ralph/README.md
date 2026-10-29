# Ralph Tuliao — Modern Red & Black Portfolio Website

A modern, high-performance portfolio website for **Ralph Tuliao** — Full-Stack Developer & IoT Engineer. Built with a sleek **Red & Black** aesthetic, dynamic **PHP & MySQL** backend, full **authentication system**, live demo embeds, and premium micro-interactions.

---

## 🚀 Showcased Projects

| # | Project | Category | Live Demo |
|---|---------|----------|-----------|
| 1 | **Tapseafood: Online Food Ordering** | Web | `http://localhost/tapseafood` |
| 2 | **Hotel Online Booking** | Web | `http://localhost/hotel-booking` |
| 3 | **Arduino Water Dispenser** | IoT | — |

---

## 🎨 Design & Tech Stack

- **Palette**: Obsidian Black (`#070709`, `#13131a`) × Crimson Red (`#ff1e42`) with glassmorphic cards & ambient glow
- **Typography**: *Outfit* (body) + *Space Grotesk* (headings) via Google Fonts
- **Backend**: PHP 7.4+ / 8.x with PDO MySQL
- **Resilience**: `db.php` includes a zero-crash fallback data layer — works immediately without MySQL

---

## 🗄️ Database Setup

1. Start **Apache** and **MySQL** in XAMPP Control Panel
2. Go to `http://localhost/phpmyadmin/`
3. Click **Import** → choose `database.sql` → click **Go**
4. Database `ralph_portfolio` is created with all tables and seed data

> **Auto-setup**: If the DB doesn't exist, `db.php` will automatically run `database.sql` on first load.

---

## 🔑 Authentication

The site includes a full login/register system:

| Feature | Details |
|---------|---------|
| Register | Username, email, bcrypt-hashed password |
| Login | Username **or** email + password |
| Session | PHP sessions — persists across page loads |
| Admin seed | `admin` / `Admin@1234` (change in production!) |

API endpoint: `api_auth.php` — actions: `register`, `login`, `logout`, `me`

---

## 📄 Resume PDF

Place your resume file at:
```
assets/resume/ralph-tuliao-resume.pdf
```
The **Download PDF** button in the Resume modal automatically appears when the file exists, and shows **"PDF Coming Soon"** when it doesn't.

---

## ✨ Key Interactive Features

| Feature | Description |
|---------|-------------|
| **Scroll-Reveal** | Cards and sections fade in as you scroll (IntersectionObserver) |
| **Typewriter Hero** | The red role text cycles through phrases with a blinking cursor |
| **Skill Bar Animation** | Progress bars fill smoothly when scrolled into view |
| **3D Card Tilt** | Project cards respond to mouse position with subtle 3D perspective tilt |
| **Live Demo Modal** | Embeds project demos in a full-screen iframe with loading spinner |
| **Project Filter** | Filter by All / Web Apps / IoT & Hardware with animated transitions |
| **Resume Modal** | Inline resume viewer with education, experience, skills, and contact info |
| **Auth Modal** | Login/Register with tab switching, password toggle, and shake-on-error |
| **Back to Top** | Floating red button appears after scrolling 400px |
| **Cursor Glow** | Subtle red radial glow follows the mouse on desktop |
| **Toast Notifications** | Center-bottom animated success/error toasts |
| **Contact Form** | AJAX submission — saves to DB or logs to `messages_log.txt` as fallback |
| **Social Links** | GitHub, LinkedIn, and Email icons in the footer |

---

## 🌐 Viewing the Portfolio

```
http://localhost/ralph/
```

---

## 📁 File Structure

```
ralph/
├── index.php              # Main portfolio page
├── db.php                 # DB connection + fallback data layer
├── api_auth.php           # Auth API (register/login/logout/me)
├── api_contact.php        # Contact form API
├── database.sql           # Full schema + seed data
├── assets/
│   ├── css/style.css      # Full stylesheet (Red & Black theme)
│   ├── js/main.js         # All client-side interactivity
│   ├── images/            # Project + profile images
│   └── resume/            # Place ralph-tuliao-resume.pdf here
└── README.md
```
