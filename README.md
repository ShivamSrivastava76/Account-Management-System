
## Project Overview

This Laravel application is a banking system simulation that allows users to:
- Create and manage accounts with Luhn-compliant account numbers
- Perform credit and debit transactions
- View account details and transaction history

## Features

### Account Management
- Account creation with name, type, currency, and optional initial balance
- System-generated 12 digit account numbers using Luhn algorithm
- Account details viewing and updating
- Account deactivation (soft delete)

### Transaction System
- Credit and debit transactions
- Overdraft prevention
- Immutable transaction records
- Transaction history with date filtering

### Security
- Laravel Sanctum for API authentication
- Role-based authorization (account owners only)
- Input validation
- API rate limiting

## Setup Instructions

### Prerequisites
- PHP 8.3
- Composer
- MySQL 5.7+
- Node.js (for frontend assets if needed)

### Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/ShivamSrivastava76/Account-Management-System.git
   cd Account-Management-System
   ```

2. Install dependencies:
   ```bash
   composer install
   npm install
   ```

3. Create and configure `.env` file:
   ```bash
   cp .env.example .env
   ```

4. Run database migrations:
   ```bash
   php artisan migrate
   ```

5. Start the development server:
   ```bash
   php artisan serve
   ```

## API Documentation

### Authentication
All endpoints require authentication using Laravel Sanctum tokens.

### Account Endpoints

#### Create Account
```
POST /api/accounts
```
Request Body:
```json
{
    "account_name": "Shivam Srivastava",
    "account_type": "Personal",
    "currency": "USD",
    "email": "shivam@gmail.com",
    "password":123456789,
    "initial_balance":10000.00
}
```

#### Get Account Details
```
GET /api/accounts/{account_number}
```

#### Update Account
```
PUT /api/accounts/{account_number}
```
Request Body:
```json
{
    "account_type": "Business",
}
```

#### Deactivate Account
```
DELETE /api/accounts/{account_number}
```

### Transaction Endpoints

#### Create Transaction
```
POST /api/transactions
```
Request Body:
```json
{
    "account_number": "xxxxxxxxxxxx",
    "type": "credit",
    "amount": 50000.00,
    "description": "Salary deposit"
}
```

#### Get Transactions
```
GET /api/transactions?account_number=xxxxxxxxxxxx&from=2023-01-01&to=2023-12-31
```

#### Transaction Statement
```
POST /api/accounts/{id}/statement
```

#### Fund transfers
```
POST /api/transfer
```
Request Body:
```json
{
    "from_account_number":"efd864b6-ab84-45f2-939e-aadfa620931b",
    "to_account_number":"c5ec9982-a2d4-4f79-be60-d4803c5ab84d",
    "amount":300
}
```

## Database Schema

### Accounts Table
- `id` - UUID (Primary Key)
- `user_id` - UUID (Foreign Key)
- `account_name` - VARCHAR (unique per user)
- `account_number` - BIGINT (Luhn-compliant, unique)
- `account_type` - ENUM (Personal, Business)
- `currency` - ENUM (USD, EUR, GBP, etc.)
- `balance` - DECIMAL
- `created_at` - TIMESTAMP
- `updated_at` - TIMESTAMP
- `deleted_at` - TIMESTAMP (for soft deletes)

### Transactions Table
- `id` - UUID (Primary Key)
- `account_id` - UUID (Foreign Key)
- `type` - ENUM (Credit, Debit)
- `amount` - DECIMAL (positive)
- `description` - TEXT (optional)
- `created_at` - TIMESTAMP
- `deleted_at` - TIMESTAMP (for soft deletes)

## Implementation Details

### Transaction Processing
- Credits increase account balance
- Debits decrease account balance if sufficient funds exist
- Overdrafts are prevented by default
- All transactions are immutable

### Security Measures
- API authentication with Sanctum tokens
- Input validation for all endpoints
- Rate limiting (60 requests per minute)

## Future Improvements
- Fund transfer between accounts
- PDF statement generation