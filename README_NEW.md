# PeriChat — Messaging App (Backend)

A real-time messaging API built with Laravel, supporting private conversations, public groups, file sharing, and live message synchronization over WebSockets.

This project demonstrates practical backend engineering concepts including service-oriented architecture, real-time event broadcasting, API authentication, and containerized deployment.

---

## 🌍 Try it Live

[https://perichat-livid.vercel.app](https://perichat-livid.vercel.app)

Frontend Repository: [PeriChat](https://github.com/KhalidHayKay/PeriChat)

---

## What It Does

- Handles user authentication via Sanctum token-based auth
- Manages private one-to-one and public group conversations
- Delivers messages in real-time via WebSockets (Laravel Reverb / Pusher)
- Tracks unread message counts per user per conversation
- Supports file and media attachments in messages

---

## Architecture

The application is organized with clean separation of concerns:

- **Authentication Layer** — Sanctum token auth for API clients
- **Controller Layer** — HTTP request handling and input validation
- **Service Layer** — Business logic and data operations
- **Broadcasting Layer** — Real-time event delivery via WebSockets
- **Storage Layer** — File upload and attachment management

---

## 🚀 Features

- 🔐 Sanctum token-based authentication
- 💬 Private one-to-one conversations
- 👥 Public and private group conversations
- 📁 File and media sharing
- ⚡ Real-time message delivery via WebSockets
- 🔔 Unread message count tracking
- 😊 Emoji support
- 🐳 Docker and Docker Compose setup

---

## 🏗 Project Structure

```
PeriChat-BE/
├── app/
│   ├── Events/                # Broadcasting events
│   ├── Http/
│   │   ├── Controllers/       # API endpoints
│   │   ├── Requests/          # Input validation
│   │   └── Resources/         # API response formatting
│   ├── Models/                # Eloquent models
│   └── Services/              # Business logic layer
├── database/
│   ├── migrations/            # Database schema
│   └── seeders/               # Database seeders
├── routes/
│   └── api.php                # API route definitions
├── docker/                    # Docker setup
├── .env.example               # Environment template
└── composer.json              # PHP dependencies
```

---

## ⚙️ How It Works

1. Users authenticate and receive a Sanctum token for API access
2. Private conversations are initiated between two users
3. Groups are created and users can join via group ID
4. Messages are stored and broadcasted to conversation channels via WebSockets
5. Unread counts are incremented on new messages and reset when a conversation is viewed
6. File attachments are stored and linked to their messages

---

## 🧪 Getting Started

### Prerequisites

- Docker and Docker Compose

### 1️⃣ Clone the Repository

```bash
git clone https://github.com/KhalidHayKay/PeriChat-BE.git
cd PeriChat-BE
```

### 2️⃣ Setup Environment Variables

```bash
cp .env.example .env
```

Configure your `.env` with database credentials, frontend URL, and WebSocket settings.

### 3️⃣ Start Services

```bash
cd docker
docker-compose up -d
```

### 4️⃣ Initialize the Database

```bash
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

The API will be available at: [http://localhost:8000](http://localhost:8000)

---

## 🔑 API Endpoints

### Authentication

```
POST   /api/auth/register
POST   /api/auth/login
POST   /api/auth/logout
```

### Conversations

```
GET    /api/conversation/subjects
POST   /api/conversation/create/private/{user}
```

### Messaging

```
GET    /api/messaging/conversation/{id}
POST   /api/messaging/conversation/{id}/send
POST   /api/messaging/conversation/{id}/unread/reset
```

### Groups

```
POST   /api/group/new
POST   /api/group/{id}/join
POST   /api/group/{id}/leave
```

---

## 🔄 Real-Time Events

The backend broadcasts these events over WebSocket channels:

```
MessageSent           # New message in a conversation
ConversationCreated   # New conversation started
GroupCreated          # New group created
MemberJoined          # User joined a group
```

---

## 🔐 Security

- Sanctum token authentication on all protected routes
- CORS protection with frontend domain whitelisting
- Bcrypt password hashing
- SQL injection protection via Eloquent ORM
- Input validation on all API endpoints
- Private channel authorization for WebSocket broadcasts

---

## 📚 Built to Practice

- RESTful API design with Laravel
- Real-time broadcasting with WebSockets
- Service-oriented architecture
- Eloquent ORM and database relationships
- Event-driven architecture
- Docker containerization

---

## 👨‍💻 Author

Built by Khalid

**Tech Stack:** Laravel 12 · Laravel Reverb · Sanctum · PostgreSQL · Docker · Nginx
