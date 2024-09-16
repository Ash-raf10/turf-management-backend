# Turf Management Backend

### The original project was on [bitbucket](https://bitbucket.org/turf-management/turf-management-backend/src/develop/). I have imported the project on github for better organization.

This repository contains the backend for the Turf Management system. You can view the project specification [here](https://docs.google.com/document/d/1nMc5BU3PvIJLAgdNgFG4Ca_xGuTRBv_7cLsAfWrUg9A/edit?usp=sharing). We have used jira as the project managment tool.

## Prerequisites

Ensure you have the following installed on your machine:

- Git
- Docker
- Docker Compose

## Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd <repository-name>
```

### 2. Copy the Environment Configuration

```bash
cp .env.example .env
```

### 3. Start Docker Containers

Use `docker-compose` to start the application in detached mode:

```bash
sudo docker-compose up -d
```

### 4. Access the PHP Container

Once the containers are up, access the `php-fpm` container:

```bash
sudo docker exec -it turf-management-backend_php-fpm_1 sh
```

### 5. Navigate to the Project Directory

Inside the container, navigate to the Laravel project root:

```bash
cd /var/www
```

### 6. Generate Laravel Application Key

Generate the application key:

```bash
php artisan key:generate
```

### 7. Generate JWT Secret

Set up the JWT authentication secret:

```bash
php artisan jwt:secret
```

### 7. Migrate and seeding

Migrate the db table and seed using:

```bash
php artisan migrate --seed
```

## Additional Notes

- Ensure your `.env` file is properly configured before proceeding with migrations or seeding.
- You may need to run migrations and seeders if your project requires them.

## Postman collection

- you can test the api using the postman collection. The collection is stored on the {root}/postman named as tms.json
- register a user using the api of company->company register
- after succesful registration it will send a otp on the registered mobile and the response will contain a token
- you can get the otp from laravel telesope log
- simply copy the token and the otp
- go to the api of Otp-> match otp, call the api with token and otp
- after success, it will return the jwt token
- use the token as a bearer token
- test the api's
- you can also create end customer using customer->register endpoint

## Telescope

- you can access telescope by visiting {app_url}/telescope
- if visiting telescope gives this error `Mix manifest not found` then run this


```bash
php artisan vendor:publish --tag=telescope-assets
```

## License

This project is licensed under [MIT License](LICENSE).
```
