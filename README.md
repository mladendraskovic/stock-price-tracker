# Stock Price Tracker

## Requirements

- PHP 8.3
- Composer
- MYSQL 8.0

## Installation

Easiest way to run the application is to use Docker and Laravel Sail. Make sure that you have Docker installed on your machine and that ports for MySQL and Redis are not in use (stop the locally installed services before continuing).

Next, clone the repository and run the following command to install the dependencies:

```bash
docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$(pwd):/var/www/html" \
    -w /var/www/html \
    laravelsail/php83-composer:latest \
    composer install --ignore-platform-reqs
```

Copy the `.env.example` file to `.env`:

```bash
cp .env.example .env
```

Make sure to set the API key for the `ALPHA_VANTAGE_API_KEY` variable. You can claim free api key at [Alpha Vantage Support](https://www.alphavantage.co/support/#api-key).

Then, generate the application key and run the migrations and seed the database by running the following commands:

```bash
./vendor/bin/sail artisan key:generate
./vendor/bin/sail artisan migrate --seed
```

After that you may start the application by running:

```bash
./vendor/bin/sail up
```

This will start the application, you can access it by visiting [http://localhost](http://localhost).

## Usage

To populate the database with stock prices, you can run the following command:

```bash
./vendor/bin/sail artisan app:fetch-stock-price-data
```

Alternatively, you can run the scheduler to fetch the data every minute:

```bash
./vendor/bin/sail artisan schdule:work
```

Finally, you can use the Postman collection to interact with the API. You can find the exported collection in the `postman_collection.json` file in the root directory of the project.
