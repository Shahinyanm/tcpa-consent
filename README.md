# TCPA Consent Management System

## Installation

1. **Backend: Laravel**
    - Clone the repository.
    - Run `composer install`.
    - Configure `.env` file with database and Twilio credentials.
    - Run migrations: `php artisan migrate`.

2. **Frontend: Vue.js**
    - Run `npm install`.
    - Start the development server: `npm run dev`.
3. **Twilio configuration**
    - Add to .env
    `TWILIO_SID=your_twilio_sid`
    `TWILIO_TOKEN=your_twilio_token`
    `TWILIO_PHONE_NUMBER=your_twilio_phone_number`
4. **Websockets**
   -  php artisan reverb:install
   -  php artisan reverb:start --host="0.0.0.0" --port=8080 --hostname="{hostname from env}"

## Features

- Consent registration.
- Verification via Twilio.
- SMS handling webhook.
- Vue.js front-end with live status updates.

## Testing

Run all backend tests:
```bash
php artisan test
