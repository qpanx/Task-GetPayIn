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








My approach focused on building a clean, maintainable, and scalable Laravel application that delivers all required functionality while emphasizing software craftsmanship. The project is structured using SOLID principles, modular architecture, and RESTful API design, with separation of concerns between the backend (Laravel) and frontend (Blade, Livewire or Vue).

🔧 Backend Implementation
✔️ Models & Relationships
Post has a many-to-many relationship with Platform through PostPlatform.

User can create many posts and has a customizable platform selection.

Used Eloquent relationships and Laravel policies for access control.

Trade-Offs:

Used a separate PostPlatform pivot instead of JSON array in Post to normalize the schema and allow per-platform statuses (better for scalability).

✔️ Authentication (Laravel Sanctum)
Chose Laravel Sanctum for simple token-based authentication suitable for SPAs or mobile clients.

Trade-Offs:

Did not use Laravel Passport or OAuth to avoid added complexity for this use case, since third-party API integration is mocked.

✔️ Post Scheduling and Processing
Created a Laravel job/command that checks due posts and mocks publishing.

Used queues with database driver (or Redis if available) to handle publishing asynchronously.

Trade-Offs:

Mocked publishing logic instead of real API integrations to focus on architecture and scheduling logic.

Kept job retry logic simple for demo purposes.

✔️ Validation Rules
Used Form Requests to validate input data.

Applied platform-specific validation (e.g., Twitter max character count).

Trade-Offs:

Validation is generalized and could be abstracted further using Strategy pattern if platform logic grows.

✔️ Rate Limiting
Implemented using a custom validation rule that checks scheduled posts per user per day.

Trade-Offs:

Opted for a query-based check over Laravel's built-in RateLimiter since it’s data-dependent, not request-based.

✔️ Activity Logging
Logged actions like post creation, updates, deletions, and platform toggling.

Used a simple activity_logs table with a polymorphic relationship.

Trade-Offs:

Lightweight implementation, no external packages like Spatie Activity Log to keep it dependency-light.

🎨 Frontend Implementation
✔️ Post Editor
Form includes: title, content, character counter, image upload, datetime picker, platform multi-select.

Used Livewire (or Vue) for dynamic character count and platform toggling.

Trade-Offs:

Chose Livewire for simplicity and quick development, though Vue could provide better decoupling for larger apps.

✔️ Dashboard
Calendar view using FullCalendar to visualize scheduled posts.

List view with filters (status, date) and status badges.

Used Alpine.js or Vue for interactivity.

Trade-Offs:

Calendar is read-only (no drag & drop reschedule) to keep scope reasonable.

✔️ Platform Settings
User can activate/deactivate available platforms.

Toggle saves to a user_platforms pivot table (if implemented), or filtered dynamically.

📊 Post Analytics (Creative Feature)
Chart.js used to display:

Posts per platform (bar/doughnut)

Success vs failed (pie)

Scheduled vs published (line)

Backend aggregates data using Eloquent + simple caching.

Trade-Offs:

Data is calculated on-demand (no materialized analytics table) for simplicity.

🔐 Security Considerations
API protected with Sanctum.

Authorization via Laravel Gates/Policies.

Validations for upload types, character limits, and scheduled dates.

Escape output in views to prevent XSS.

⚙️ Performance Considerations
Eager loading (with()) used to reduce N+1 queries.

Job queue decouples heavy work from request cycle.

Caching for platform list and analytics queries (if app scales).

Trade-Offs:

Did not implement full-scale caching layers (e.g., Redis) unless available on host.

🧪 Testing (Optional Scope)
Wrote feature tests for post creation and scheduling.

Used factory() and DatabaseMigrations for isolated test environments.

Trade-Offs:

Limited test coverage due to time constraints, but test setup is ready for expansion.

🧠 Custom Feature Idea
✅ User Performance Radar
Added a Radar chart showing user’s:

Posts published per platform

Success rate

Engagement score (mocked)

Helps motivate consistent content creation.
