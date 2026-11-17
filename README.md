# EventHub - Simple Event Planner

A simple event planner built with HTML, CSS, JavaScript, Bootstrap, MySQL and PHP. Plan birthdays, meetings, parties, study sessions and sports events, search them by title or location, filter by category, and mark the ones you are attending.

## Features

- Add events with title, category, date, time, location, description and guest count
- Search events by title or location
- Filter by category (Birthday, Meeting, Party, Study, Sports, Other)
- Upcoming events view showing only events from today onwards
- Mark events as attending and see them highlighted
- Past events are shown faded with a "Past event" badge
- Edit and delete events
- Event stats: total events, upcoming events, attending and total guests
- 5 sample events included (Birthday Bash, Web Development Workshop, Cricket Practice Match, Team Dinner, Project Demo Meeting)

## Setup

1. Import the database:
   ```bash
   mysql -u root -p < database.sql
   ```
2. Update the database credentials in `config.php` if needed.
3. Serve the project folder with PHP:
   ```bash
   php -S localhost:8000
   ```
4. Open `http://localhost:8000` in your browser.

## Tech Stack

- HTML, CSS, JavaScript
- Bootstrap 5
- PHP (PDO)
- MySQL

Built by Hammad Khan.
