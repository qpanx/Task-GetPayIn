# Content Scheduler

A Laravel and Vue.js application for scheduling and publishing social media posts across multiple platforms.

## Features

- User authentication with Laravel Sanctum
- Create, view, update, and delete posts
- Schedule posts for future publication
- Support for multiple social platforms (Twitter, Instagram, LinkedIn, Facebook)
- Platform-specific validation (character limits, image requirements)
- Calendar and list view of scheduled posts
- Platform management for users
- Post analytics
- Rate limiting (max 10 scheduled posts per day)
- Activity logging

## Requirements

- PHP 8.0+
- Composer
- Node.js and NPM
- MySQL or compatible database

## Installation

1. Clone the repository:
   ```
   git clone <repository-url>
   cd content-scheduler
   ```

2. Install PHP dependencies:
   ```
   composer install
   ```

3. Install JavaScript dependencies:
   ```
   npm install
   ```

4. Copy the .env.example file:
   ```
   cp .env.example .env
   ```

5. Generate application key:
   ```
   php artisan key:generate
   ```

6. Configure your database in the .env file:
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=content_scheduler
   DB_USERNAME=<your-username>
   DB_PASSWORD=<your-password>
   ```

7. Run database migrations and seeders:
   ```
   php artisan migrate --seed
   ```

8. Compile assets:
   ```
   npm run dev
   ```

9. Start the development server:
   ```
   php artisan serve
   ```

10. Access the application at http://localhost:8000

## Usage

### Authentication

- Register a new account or use the default test account:
  - Email: test@example.com
  - Password: password

### Scheduling Posts

1. Navigate to "Posts" and click "Create New Post"
2. Fill in the post details (title, content, image)
3. Select platforms to publish to
4. Set a date and time for publishing
5. Submit the form to schedule your post

### Post Processing

- A command will run automatically via the Laravel scheduler to process posts due for publishing
- You can manually trigger post processing with:
  ```
  php artisan app:publish-scheduled-posts
  ```

## Implementation Details

### Backend

- Laravel 10
- RESTful API with Laravel Sanctum for authentication
- Database models with relationships:
  - User: id, name, email, password
  - Post: id, title, content, image_url, scheduled_time, status, user_id
  - Platform: id, name, type, requirements
  - PostPlatform: post_id, platform_id, platform_status
  - ActivityLog: user_id, action, entity_type, entity_id, meta_data

### Frontend

- Vue.js 3 with Composition API
- Vuex for state management
- Vue Router for navigation
- Tailwind CSS for styling
- Responsive design

## License

This project is licensed under the MIT License.

# Laravel Scheduled Posts System

This system provides functionality to schedule and automatically publish posts to various social media platforms at specific times.

## How It Works

1. Users create posts and set a `scheduled_time` and attach platforms to publish to
2. The Laravel scheduler runs a command every minute to check for posts due for publishing
3. Due posts are processed and published to their selected platforms
4. Post status is updated based on the publishing results

## Post Statuses

- `draft`: Post is still being edited and not ready for scheduling
- `scheduled`: Post is scheduled to be published at a specific time
- `published`: Post was successfully published to all platforms
- `partial`: Post was published to some platforms but failed on others

## Setting Up the Scheduler

To ensure scheduled posts are processed, Laravel's scheduler needs to be running. Add the following Cron entry to your server:

```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

Or you can use Laravel's task scheduling with tools like Supervisor.

## Using the Command Manually

You can also run the command manually:

```bash
php artisan posts:publish-scheduled
```

## Implementation Details

The system includes:
- `PublishScheduledPosts` command that runs on schedule
- `PostPublishingService` that handles the actual publishing to different platforms
- Model relations connecting posts to platforms
- Status tracking of posts and per-platform publishing statuses

## Adding New Platforms

To add support for a new platform:
1. Add a new platform type in the `Platform` model
2. Implement a corresponding method in `PostPublishingService`
