# Filter Management Test Application

This project is a Symfony-based web application for managing **filters** and their **criteria**.

It ships with a working ```.env``` file suitable for local development and testing in Docker.
No manual environment variable setup is required to run the application locally or to execute tests. As thus, it's not suited for production deploy.

---

## Tech Stack

- **Backend:** PHP 8.1+, Symfony 6
- **Database:** MySQL 8.0
- **ORM:** Doctrine ORM + Migrations
- **Frontend:** Vue 3 (partial integration), TypeScript
- **Assets:** Webpack Encore
- **Web Server:** Nginx
- **Containerization:** Docker + Docker Compose
- **Package Managers:** Composer, Yarn

---

## Features

- Create, edit, and delete filters
- Add, edit, and remove filter criteria
- Dynamic criteria form (type → subtype → value)
- Criteria value input adapts based on selected type
- Client-side validation with server-side fallback
- Docker-based development environment

---

## Requirements

### All platforms
- Docker Engine 19.03+
- Docker Compose
- Make (optional but recommended)

### macOS / Windows
- Docker Desktop
- **Windows:** WSL2 enabled (recommended)

---

## Installation

### 1. Clone the repository
```bash
git clone https://github.com/Konservin/filter_management_test.git
cd filter_management_test
```

### 2. Build and start the application
```bash
make setup
```
This command performs the following steps: 
- builds Docker images
- starts PHP, MySQL, and Nginx containers
- installs Composer dependencies
- installs frontend dependencies
- builds frontend assets

### 3. Manual build and start (without Make)
If you prefer not to use ```make```, run the steps manually:
```bash
docker compose up -d --build
docker compose exec php composer install
docker compose exec php yarn install
docker compose exec php yarn encore dev
docker compose exec php php bin/console doctrine:migrations:migrate -n
```

### 4. Open the application
```bash
http://localhost:8090
```
---
## Platform Notes
### Linux
- Works out of the box on Ubuntu 22.04+.

### macOS
- Install Docker Desktop
- Ensure file sharing is enabled for the project directory

### Windows (WSL2)
- Install Docker Desktop
- Enable WSL2 integration
- Run all commands from inside WSL (Ubuntu recommended)

---
## Common Commands
### Rebuild containers
```bash
docker compose up -d --build
```

### Stop containers and remove volumes (clean slate)
```bash
docker compose down -v
```

### Rebuild frontend assets
```bash
docker compose exec php yarn encore dev
```

### Troubleshooting
Port already in use
Ensure the following ports are free:
- 8090 — Nginx
- 3305 — MySQL (optional, only if exposed)

### Check running containers:
```bash
docker ps
```
If a port is already in use, stop the conflicting container or adjust the port mapping in docker-compose.yml.

### Run tests:
```bash
make tests
```

---
## Documentation
- [Architecture](docs/ARCHITECTURE.md)