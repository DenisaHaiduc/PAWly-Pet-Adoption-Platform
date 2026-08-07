# 🐾 PAWly – Web Pet Adoption Platform

PAWly este o aplicație web full-stack dedicată gestionării și publicării anunțurilor de adopție pentru animale de companie. Proiectul include autentificare, control al accesului bazat pe roluri (Admin/User), operațiuni CRUD complete, gestionare de fișiere pe server și interacțiuni în timp real prin AJAX.

---

## 📸 Capturi de Ecran (Screenshots)

| Pagina Principală & Căutare Live | Formular Adăugare Anunț |
| :---: | :---: |
| ![Home Page](src/uploads/ok1.png) | ![Add Form](src/uploads/ok3.png) | ![Find your soulmate pet](src/uploads/ok2.png) |


---

## ✨ Funcționalități Principale

- **Autentificare & Sesiuni:** Înregistrare, autentificare și gestionare securizată a sesiunilor de utilizator (roluri de Admin / Utilizator).
- **CRUD Complet (PHP PDO & MySQL):** Creare, citire, editare și ștergere de anunțuri cu filtrare și precompletare de date.
- **Management Fișiere & Upload:** Încărcare securizată de imagini pe server în directorul `uploads/`, cu validare de extensii și curățare la ștergerea anunțului.
- **Interfață Asincronă (AJAX & jQuery):**
  - Căutare live în timp real fără reîncărcarea paginii.
  - Sistem dinamic de opțiuni de tip "Like" cu actualizare instantanee a numărătorului.
- **Quiz Interactiv de Potrivire:** Modul vizual overlay pentru recomandarea animalului potrivit în funcție de preferințele utilizatorului.
- **Containerizare Docker:** Mediul de dezvoltare este configurat complet prin Docker Compose (server web PHP + bază de date MySQL).

---

## 🛠️ Stack Tehnologic

- **Backend:** PHP 8.x (PDO), MySQL
- **Frontend:** HTML5, CSS3, JavaScript (ES6), jQuery 3.7
- **DevOps & DB:** Docker, Docker Compose, PHPMyAdmin

---

## 📁 Structura Proiectului

```text
pawly_docker/
├── docker-compose.yml   # Configurația containerelor Docker
├── init.sql             # Scriptul inițial de populare a bazei de date
└── src/                 # Codul sursă al aplicației PHP/HTML/CSS
    ├── config.php       # Conexiunea PDO la MySQL
    ├── index.php        # Pagina principală cu feed-ul de anunțuri
    ├── add_announcement.php
    ├── edit_announcement.php
    ├── delete_announcement.php
    ├── like.php         # Endpoint AJAX pentru Like-uri
    └── uploads/         # Imagini încărcate de utilizatori
