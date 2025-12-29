# Online Hotel Booking System

A comprehensive hotel booking system built with PHP, MySQL, and a modern dark multicolor theme. This system allows customers to search and book hotel rooms while providing administrators with complete management capabilities.

## 🌟 Features

### Customer Features
- **User Registration & Authentication**: Secure account creation and login system
- **Hotel Search & Browsing**: Advanced search with filters for location, price range, and availability
- **Room Booking**: Easy-to-use booking interface with date selection and guest count
- **Booking Management**: View all bookings with status tracking
- **Booking Confirmation**: Detailed confirmation page with all booking information
- **Hotel Details**: Comprehensive hotel and room information display

### Admin Features
- **Dashboard**: System-wide statistics and recent activity monitoring
- **Hotel Management**: Add, edit, and delete hotels
- **Room Management**: Manage rooms for each hotel with detailed specifications
- **Booking Management**: View and manage all customer bookings
- **User Management**: Monitor and manage registered users
- **Activity Logs**: Track all system activities with timestamps and IP addresses

### Additional Features
- **Responsive Design**: Works seamlessly on desktop and mobile devices
- **Dark Multicolor Theme**: Modern UI with vibrant colors and no gradients
- **Real-time Availability**: Check room availability for selected dates
- **Multiple Room Types**: Support for Standard, Deluxe, Suite, Executive, Family Room, and Penthouse
- **Star Ratings**: Display hotel quality with star ratings
- **Secure Authentication**: Password hashing and session management

## 🎨 Design Theme

The system features a dark multicolor theme with the following color palette:
- **Primary Blue**: #00d4ff - Main accent color
- **Hot Pink**: #ff006e - Highlights and important elements
- **Neon Green**: #06ffa5 - Success states and prices
- **Electric Purple**: #8338ec - Secondary accents
- **Bright Yellow**: #ffbe0b - Warnings and special highlights
- **Dark Navy**: #1a1a2e - Primary background
- **Deep Blue**: #16213e - Secondary background
- **Midnight**: #0f3460 - Tertiary background

## 🚀 Installation

### Prerequisites
- XAMPP (or similar PHP/MySQL stack)
- PHP 7.4 or higher
- MySQL 5.7 or higher (running on port 3307)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/Sankrityayana/Online-Hotel-Booking-System.git
   cd Online-Hotel-Booking-System
   ```

2. **Configure MySQL Port**
   - Ensure your MySQL is running on port 3307
   - If using a different port, update `config.php`:
     ```php
     $host = 'localhost:YOUR_PORT';
     ```

3. **Import Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Create a new database or import directly using the SQL file
   - Import `database/database.sql`
   - The database name is `hotel_booking_db`

4. **Configure Database Connection**
   - Open `config.php`
   - Verify database credentials:
     ```php
     $host = 'localhost:3307';
     $username = 'root';
     $password = '';  // Update if you have a password
     $database = 'hotel_booking_db';
     ```

5. **Start Apache and MySQL**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

6. **Access the Application**
   - Open your browser
   - Navigate to: `http://localhost/Online-Hotel-Booking-System/`

## 👤 Default User Accounts

### Admin Account
- **Email**: admin@hotelbooking.com
- **Password**: password
- **Access**: Full system administration

### Customer Account
- **Email**: customer@email.com
- **Password**: password
- **Access**: Hotel browsing and booking

## 📁 Project Structure

```
Online-Hotel-Booking-System/
├── admin/
│   ├── dashboard.php          # Admin dashboard with statistics
│   ├── hotels.php             # Hotel management
│   ├── rooms.php              # Room management for hotels
│   ├── bookings.php           # All bookings management
│   ├── users.php              # User management
│   └── includes/
│       ├── header.php         # Admin header component
│       ├── sidebar.php        # Admin navigation sidebar
│       └── footer.php         # Admin footer component
├── auth/
│   ├── login.php              # User login
│   ├── register.php           # User registration
│   └── logout.php             # Logout handler
├── customer/
│   ├── dashboard.php          # Customer dashboard
│   ├── browse-hotels.php      # Hotel search and listing
│   ├── hotel-details.php      # Detailed hotel and room view
│   ├── book-room.php          # Room booking form
│   ├── booking-confirmation.php # Booking success page
│   ├── my-bookings.php        # Customer booking history
│   └── includes/
│       ├── header.php         # Customer header component
│       ├── sidebar.php        # Customer navigation sidebar
│       └── footer.php         # Customer footer component
├── css/
│   └── style.css              # Dark multicolor theme styles
├── database/
│   └── database.sql           # Complete database schema
├── config.php                 # Database configuration and helper functions
├── index.php                  # Homepage with featured hotels
└── README.md                  # This file
```

## 🗄️ Database Schema

### Main Tables

#### users
- User accounts (customers and admins)
- Fields: id, full_name, email, password, phone, address, user_type, profile_picture, status, timestamps

#### hotels
- Hotel information
- Fields: id, hotel_name, description, address, city, state, country, postal_code, phone, email, star_rating, image, status, timestamps

#### room_types
- Predefined room types (Standard, Deluxe, Suite, etc.)
- Fields: id, type_name, description, created_at

#### rooms
- Room inventory for each hotel
- Fields: id, hotel_id, room_type_id, room_number, floor, price_per_night, capacity, bed_type, size_sqft, description, image, status, timestamps

#### bookings
- Customer reservations
- Fields: id, user_id, room_id, hotel_id, check_in_date, check_out_date, num_guests, total_amount, booking_status, payment_status, payment_method, special_requests, timestamps

#### amenities
- Room amenities (WiFi, TV, AC, etc.)
- Fields: id, amenity_name, icon, created_at

#### room_amenities
- Junction table linking rooms to amenities
- Fields: room_id, amenity_id

#### reviews
- Customer hotel reviews
- Fields: id, user_id, hotel_id, booking_id, rating, review_title, review_text, created_at

#### payments
- Payment tracking
- Fields: id, booking_id, amount, payment_method, transaction_id, payment_status, payment_date

#### activity_logs
- System activity tracking
- Fields: id, user_id, action, description, ip_address, created_at

## 🔐 Security Features

- **Password Hashing**: Uses PHP's `password_hash()` with PASSWORD_DEFAULT
- **SQL Injection Prevention**: All inputs sanitized using `mysqli_real_escape_string()`
- **Session Management**: Secure session-based authentication
- **User Type Verification**: Role-based access control for admin and customer areas
- **Input Validation**: Server-side validation for all forms
- **Activity Logging**: Track all user actions with IP addresses

## 🛠️ Helper Functions

The `config.php` file includes numerous helper functions:
- `sanitize()` - Clean user input
- `isLoggedIn()` - Check authentication status
- `isCustomer()` - Verify customer role
- `isAdmin()` - Verify admin role
- `getUserInfo()` - Fetch user details
- `getHotelById()` - Retrieve hotel information
- `getRoomById()` - Retrieve room information
- `isRoomAvailable()` - Check room availability for dates
- `calculateTotalAmount()` - Calculate booking total
- `logActivity()` - Record user actions
- `timeAgo()` - Format relative timestamps
- `formatCurrency()` - Format prices
- `getBookingStatusBadge()` - Get CSS class for booking status
- `getPaymentStatusBadge()` - Get CSS class for payment status
- `uploadFile()` - Handle file uploads
- `getStarRating()` - Display star ratings

## 📱 Responsive Design

The system is fully responsive and works on:
- Desktop computers (1200px+)
- Tablets (768px - 1199px)
- Mobile phones (< 768px)

## 🔄 Booking Status Flow

1. **Pending**: Initial booking state
2. **Confirmed**: Hotel confirms the reservation
3. **Completed**: Customer has checked out
4. **Cancelled**: Booking cancelled by customer or admin

## 💳 Payment Status

1. **Pending**: Payment not yet received
2. **Paid**: Payment completed
3. **Refunded**: Payment refunded to customer

## 🚧 Future Enhancements

- Online payment integration (Stripe, PayPal)
- Email notifications for bookings
- SMS confirmations
- Room availability calendar
- Advanced search filters (amenities, reviews)
- Customer review and rating system
- Hotel image gallery
- Multi-language support
- Discount and coupon system
- Loyalty program
- Analytics dashboard for hotels

## 📝 Usage Guide

### For Customers

1. **Register an Account**
   - Click "Register" in the navigation
   - Fill in your details
   - Click "Register" to create your account

2. **Browse Hotels**
   - Use the search box on the homepage
   - Or click "Browse Hotels" in the customer dashboard
   - Apply filters for city, price range

3. **Book a Room**
   - Select a hotel to view details
   - Choose a room type
   - Click "Book Now"
   - Select check-in/check-out dates
   - Enter number of guests
   - Add special requests (optional)
   - Confirm booking

4. **View Bookings**
   - Go to "My Bookings" in the customer dashboard
   - View all your reservations
   - Check booking and payment status

### For Administrators

1. **Login as Admin**
   - Use admin credentials
   - Access admin dashboard

2. **Manage Hotels**
   - Click "Hotels" in sidebar
   - Add new hotels with all details
   - View existing hotels
   - Delete hotels if needed

3. **Manage Rooms**
   - Click "View Rooms" for any hotel
   - Add new rooms with specifications
   - Set prices and availability
   - Delete rooms if needed

4. **Monitor Bookings**
   - Click "Bookings" in sidebar
   - View all customer reservations
   - Filter by status (pending, confirmed, cancelled)

5. **Manage Users**
   - Click "Users" in sidebar
   - View all registered users
   - Filter by user type
   - Monitor user activity

## 🐛 Troubleshooting

### Database Connection Failed
- Verify MySQL is running on port 3307
- Check credentials in `config.php`
- Ensure database `hotel_booking_db` exists

### Login Not Working
- Clear browser cache and cookies
- Verify user exists in database
- Check password (default: "password")

### Booking Fails
- Ensure room is available for selected dates
- Check that check-out is after check-in
- Verify database has proper foreign key relationships

### Styling Issues
- Clear browser cache
- Verify `css/style.css` is loaded
- Check browser console for errors

## 📄 License

This project is created for educational purposes.

## 👨‍💻 Developer

**Sankrityayana**
- GitHub: [@Sankrityayana](https://github.com/Sankrityayana)

## 🤝 Contributing

Contributions, issues, and feature requests are welcome!

## ⭐ Show Your Support

Give a ⭐️ if you like this project!

---

**Note**: This is a demonstration project. For production use, implement additional security measures, proper payment gateways, and extensive testing.
