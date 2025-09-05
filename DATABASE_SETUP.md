# 🚌 Bus Tracking System - Database Setup Guide

This guide will help you set up the MySQL database for your bus tracking system using XAMPP.

## 📋 Prerequisites

- **XAMPP** installed and running
- **Apache** and **MySQL** services started
- **PHP** support enabled
- Basic knowledge of MySQL/phpMyAdmin

## 🚀 Quick Setup (Recommended)

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** service
3. Start **MySQL** service
4. Make sure both services show green status

### Step 2: Run Setup Script
1. Open your web browser
2. Navigate to: `http://localhost/Bus-tracking/setup_database.php`
3. The script will automatically:
   - Create the database `bus_tracking_db`
   - Create all necessary tables
   - Insert sample data
   - Create admin user

### Step 3: Verify Setup
1. Test database connection: `http://localhost/Bus-tracking/database/test_connection.php`
2. Access admin dashboard: `http://localhost/Bus-tracking/admin/dashboard.php`
3. Test API endpoints: `http://localhost/Bus-tracking/api/buses.php`

## 🗄️ Manual Database Setup (Alternative)

### Step 1: Create Database
1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Click "New" on the left sidebar
3. Enter database name: `bus_tracking_db`
4. Click "Create"

### Step 2: Import SQL Schema
1. Select the `bus_tracking_db` database
2. Click "Import" tab
3. Choose file: `database/setup.sql`
4. Click "Go" to execute

### Step 3: Create Admin User
Run this SQL command in phpMyAdmin:
```sql
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bustracking.com', 'admin');
```

## 🏗️ Database Schema

### Tables Created

#### 1. `buses` - Bus Information
- `id`: Unique bus identifier
- `bus_number`: Bus registration number
- `route_id`: Associated route
- `capacity`: Maximum passenger capacity
- `current_lat/lng`: GPS coordinates
- `speed`: Current speed in km/h
- `occupancy`: Current passenger count
- `status`: active/inactive/maintenance

#### 2. `routes` - Route Information
- `id`: Route identifier
- `route_name`: Descriptive route name
- `start_location`: Starting point
- `end_location`: Destination
- `frequency_minutes`: Bus frequency
- `estimated_duration_minutes`: Travel time

#### 3. `bus_locations` - Location History
- `bus_id`: Reference to bus
- `latitude/longitude`: GPS coordinates
- `speed`: Speed at that moment
- `occupancy`: Passenger count
- `timestamp`: When location was recorded

#### 4. `users` - System Users
- `username`: Login username
- `password`: Hashed password
- `email`: User email
- `role`: admin/operator

#### 5. `stops` - Bus Stops
- `stop_name`: Stop location name
- `latitude/longitude`: GPS coordinates
- `route_id`: Associated route
- `stop_order`: Sequence in route

## 🔐 Default Login Credentials

- **Username**: `admin`
- **Password**: `admin123`
- **Role**: Administrator

## 📡 API Endpoints

### Buses API (`/api/buses.php`)
- `GET`: Retrieve all active buses
- `POST`: Add new bus or update location
- `PUT`: Update bus information
- `DELETE`: Deactivate bus

### Routes API (`/api/routes.php`)
- `GET`: Retrieve all active routes
- `POST`: Add new route
- `PUT`: Update route information
- `DELETE`: Deactivate route

### Authentication API (`/api/auth.php`)
- `POST`: User login

## 🛠️ Troubleshooting

### Common Issues

#### 1. "Connection failed" Error
- **Solution**: Check if MySQL service is running in XAMPP
- **Verify**: MySQL status should be green in XAMPP Control Panel

#### 2. "Access denied" Error
- **Solution**: Default XAMPP MySQL credentials are `root` with no password
- **Verify**: Check `database/config.php` file

#### 3. "Table doesn't exist" Error
- **Solution**: Run `setup_database.php` script
- **Alternative**: Import `database/setup.sql` manually

#### 4. "Permission denied" Error
- **Solution**: Make sure Apache has read/write permissions to project folder
- **Verify**: Check folder permissions in Windows

### Database Connection Test
Run `database/test_connection.php` to diagnose connection issues.

## 📁 File Structure

```
Bus-tracking/
├── database/
│   ├── config.php          # Database configuration
│   ├── setup.sql          # SQL schema file
│   └── test_connection.php # Connection test
├── api/
│   ├── buses.php          # Buses API
│   ├── routes.php         # Routes API
│   └── auth.php           # Authentication API
├── admin/
│   └── dashboard.php      # Admin dashboard
├── setup_database.php     # Auto-setup script
└── DATABASE_SETUP.md      # This file
```

## 🔄 Updating Database

### Add New Tables
1. Modify `database/setup.sql`
2. Add new table creation SQL
3. Run `setup_database.php` again

### Modify Existing Tables
1. Use phpMyAdmin to alter tables
2. Or create migration scripts
3. Backup data before major changes

## 📊 Sample Data

The setup script automatically creates:
- **3 sample routes** (Patna area)
- **3 sample buses** with GPS coordinates
- **5 bus stops** with locations
- **1 admin user** for system access

## 🚀 Next Steps

After successful database setup:

1. **Test the system**: Visit main application at `Docs/index.html`
2. **Manage data**: Use admin dashboard for bus/route management
3. **API integration**: Connect your frontend to the PHP APIs
4. **Customization**: Modify routes, add more buses, customize locations

## 📞 Support

If you encounter issues:

1. Check XAMPP service status
2. Verify database connection using test file
3. Check error logs in XAMPP
4. Ensure all files are in correct locations

---

**Happy Tracking! 🚌✨**
