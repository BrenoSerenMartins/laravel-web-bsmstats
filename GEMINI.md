# BSMStats Project Analysis

## Project Overview

BSMStats is a web application built with Laravel 12 that provides statistics and analytics for the competitive game League of Legends. The platform allows users to search for summoners, view their match history, and analyze their performance through interactive dashboards.

**Key Technologies:**

*   **Backend:** Laravel 12, PHP 8.2
*   **Frontend:** Blade templates, TailwindCSS
*   **Database:** SQLite (default), MySQL/PostgreSQL compatible
*   **Caching:** Laravel Cache
*   **API Integration:** Riot Games API for fetching summoner and match data.

**Core Functionality:**

*   **Summoner Search:** Users can search for League of Legends summoners by their game name and tag line.
*   **Match History:** The application displays a list of recent matches for a given summoner.
*   **Detailed Match View:** Users can view detailed information about a specific match.
*   **Leaderboards:** The application features a leaderboard.
*   **Champion and Item Information:** The application provides information about champions and items.
*   **Data Synchronization:** The application uses Laravel Jobs to synchronize data from the Riot API in the background.

## Building and Running

**Prerequisites:**

*   PHP 8.2 or higher
*   Composer
*   Node.js and npm

**Installation:**

1.  Clone the repository.
2.  Install PHP dependencies: `composer install`
3.  Install frontend dependencies: `npm install`
4.  Create a `.env` file by copying `.env.example`.
5.  Generate an application key: `php artisan key:generate`
6.  Configure your database in the `.env` file.
7.  Run database migrations: `php artisan migrate`
8.  Set up your Riot API key in `config/services.php` or your `.env` file.

**Running the Application:**

*   **Development Server:** `php artisan serve`
*   **Frontend Assets:** `npm run dev`
*   **Queue Worker:** `php artisan queue:work`

**Testing:**

*   Run the test suite: `php artisan test`

## Development Conventions

*   **Routing:** Routes are defined in `routes/web.php`.
*   **Controllers:** Controllers are located in `app/Http/Controllers`.
*   **Models:** Eloquent models are in the `app/Models` directory.
*   **Views:** Blade templates are in the `resources/views` directory.
*   **API Integration:** The `SummonerController` and background jobs handle interactions with the Riot API.
*   **Caching:** The application uses Laravel's caching extensively to improve performance when interacting with the Riot API.
*   **Background Jobs:** Data fetching from the Riot API is queued as jobs to avoid long loading times for the user.
