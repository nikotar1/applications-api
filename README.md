# Yii2 Basic Project with Docker (Postgres + Nginx)

## Requirements
- Docker Engine 20+
- Docker Compose v2

## Setup Instructions

1. **Clone the repository**
   ```bash
   git clone <your-repo-url> applications-api
   cd applications-api
   ```

2. **Create environment file**
   ```bash
   cp .env.example .env
   ```

   Adjust values inside `.env` if needed (database name, user, password, ports).

3. **Build and start containers**
   ```bash
   docker compose up -d --build
   ```

4. **Install dependencies**
   ```bash
   docker compose exec php composer install
   ```

5. **Set writable permissions**
   ```bash
   docker compose exec php bash -lc 'chmod -R a+rwX runtime web/assets'
   ```

6. **Run database migrations**
   ```bash
   docker compose exec php php yii migrate --interactive=0
   ```

7. **Access the application**
   ```
   http://localhost
   ```

## API Endpoints

### POST `/request`

Request body:
```json
{
  "user_id": 1,
  "amount": 3000,
  "term": 30
}
```

Responses:

- `201 Created`
  ```json
  {
    "result": true,
    "id": 42
  }
  ```

- `400 Bad Request`
  ```json
  {
    "result": false
  }
  ```

---

### GET `/processor?delay=2`

Responses:

- `200 OK`
  ```json
  {
    "result": true
  }
  ```

- `200 OK`
  ```json
  {
    "result": false
  }
  ```
  
## Дополнительные примечания

Не добавлена логика пользователей, чтобы избежать появления других эндпоинтов и путаницы с `user_id` при использовании автотестов.
