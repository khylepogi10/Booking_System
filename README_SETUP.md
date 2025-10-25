# Event Booking System - Setup Guide

## Prerequisites
- XAMPP (Apache + MySQL + PHP 8+)
- Web browser

## Installation Steps

### 1. Setup Files
1. Copy the entire project folder to `C:\xampp\htdocs\event_booking`
2. Start XAMPP Control Panel
3. Start Apache and MySQL services

### 2. Database Setup
1. Open your browser and go to `http://localhost/phpmyadmin`
2. Create a new database named `event_booking`
3. Run the setup script:
   - Navigate to `http://localhost/event_booking/setup_database.php`
   - This will create all tables and add sample data
   - **Default Admin Credentials:**
     - Email: `admin@example.com`
     - Password: `admin123`

### 3. Folder Permissions
Ensure the `uploads` folder has write permissions for image uploads.

### 4. Access the System
- **Homepage:** `http://localhost/event_booking/`
- **Login:** `http://localhost/event_booking/login.php`
- **Register:** `http://localhost/event_booking/register.php`

## Features Implemented

### User Features
- User registration and secure login
- Browse and search events by name, location, or date
- View event details with images and pricing
- Book events with real-time seat availability
- View booking history with total spending
- Cancel bookings (refunds seats automatically)
- Auto-logout after 15 minutes of inactivity
- Responsive modern UI design

### Admin Features
- Admin dashboard with statistics (total events, bookings, users)
- Add new events with image upload
- Edit existing events
- Delete events (removes associated image)
- View all bookings with user details and revenue
- Search and filter events
- Modern, responsive admin interface

### Security Features
- Password hashing using `password_hash()`
- SQL injection prevention via prepared statements
- XSS prevention using `htmlspecialchars()`
- Session-based authentication
- Auto-logout for inactive sessions
- Input validation and sanitization

### Technical Features
- Image upload system for events
- Dynamic seat management (decrease on booking, increase on cancellation)
- Price tracking and revenue calculation
- Prevents duplicate bookings
- Mobile-responsive design
- Modern gradient UI with card layouts
- Search and filter functionality
- Booking confirmation page

## Database Schema

### users
- id (INT, Primary Key)
- name (VARCHAR)
- email (VARCHAR, Unique)
- password (VARCHAR, hashed)
- role (ENUM: 'user', 'admin')
- created_at (TIMESTAMP)

### events
- id (INT, Primary Key)
- event_name (VARCHAR)
- description (TEXT)
- date (DATE)
- location (VARCHAR)
- seats (INT)
- price (DECIMAL)
- image (VARCHAR)
- created_at (TIMESTAMP)

### bookings
- id (INT, Primary Key)
- user_id (INT, Foreign Key)
- event_id (INT, Foreign Key)
- booking_date (TIMESTAMP)

## File Structure
```
event_booking/
├── admin/
│   ├── add_event.php       # Add new events
│   ├── edit_event.php      # Edit existing events
│   ├── delete_event.php    # Delete events
│   ├── dashboard.php       # Admin dashboard
│   └── view_bookings.php   # View all bookings
├── user/
│   ├── dashboard.php       # User event browser
│   ├── book_event.php      # Book events
│   ├── my_bookings.php     # View user bookings
│   └── logout.php          # Logout handler
├── uploads/                # Event images storage
├── db.php                  # Database connection
├── index.php               # Homepage
├── login.php               # Login page
├── register.php            # Registration page
└── setup_database.php      # Database setup script
```

## Usage Guide

### For Users
1. Register a new account or login
2. Browse available events on the dashboard
3. Use search bar to filter by name, location, or date
4. Click "Book Now" to reserve a seat
5. View your bookings in "My Bookings"
6. Cancel bookings if needed (seat will be refunded)

### For Admins
1. Login with admin credentials
2. View dashboard statistics
3. Add new events with details and images
4. Manage existing events (edit/delete)
5. View all user bookings and revenue
6. Use search/filter to find specific events

## Troubleshooting

### Database Connection Issues
- Verify MySQL is running in XAMPP
- Check database name is `event_booking`
- Ensure credentials in `db.php` match your setup

### Image Upload Issues
- Check `uploads` folder exists and has write permissions
- Verify PHP file upload settings in `php.ini`

### Session/Login Issues
- Clear browser cookies
- Restart Apache in XAMPP
- Check PHP session configuration

## Support
For issues or questions, refer to the code comments or check the XAMPP error logs.
