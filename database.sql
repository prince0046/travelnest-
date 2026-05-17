-- TravelNest Database Schema
-- Run this file to create and seed the database
-- NOTE: Passwords stored as plaintext for seeding only.
-- They are auto-upgraded to bcrypt on first login.
-- OR run setup.php to upgrade all passwords immediately.
-- admin@travelnest.com password: admin123
-- user@demo.com password: demo123


CREATE DATABASE IF NOT EXISTS travelnest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE travelnest;

-- ===================== USERS =====================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    city VARCHAR(80),
    role ENUM('user','admin') DEFAULT 'user',
    tier ENUM('Bronze','Silver','Gold','Platinum') DEFAULT 'Bronze',
    total_spent DECIMAL(12,2) DEFAULT 0,
    total_bookings INT DEFAULT 0,
    profile_pic VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================== FLIGHTS =====================
CREATE TABLE IF NOT EXISTS flights (
    id INT AUTO_INCREMENT PRIMARY KEY,
    flight_code VARCHAR(20) NOT NULL,
    airline VARCHAR(80) NOT NULL,
    from_city VARCHAR(80) NOT NULL,
    from_code VARCHAR(10) NOT NULL,
    to_city VARCHAR(80) NOT NULL,
    to_code VARCHAR(10) NOT NULL,
    departure_time VARCHAR(10) NOT NULL,
    arrival_time VARCHAR(15) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    stops VARCHAR(30) DEFAULT 'Direct',
    price DECIMAL(10,2) NOT NULL,
    class ENUM('Economy','Business','First Class') DEFAULT 'Economy',
    seats_available INT DEFAULT 50,
    aircraft VARCHAR(50),
    terminal VARCHAR(10),
    baggage VARCHAR(20),
    emoji VARCHAR(10) DEFAULT '✈️',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== HOTELS =====================
CREATE TABLE IF NOT EXISTS hotels (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    city VARCHAR(80) NOT NULL,
    country VARCHAR(80) DEFAULT 'India',
    stars TINYINT DEFAULT 3,
    rating DECIMAL(3,1) DEFAULT 8.0,
    price_per_night DECIMAL(10,2) NOT NULL,
    description TEXT,
    amenities TEXT,
    emoji VARCHAR(10) DEFAULT '🏨',
    latitude DECIMAL(10,7),
    longitude DECIMAL(10,7),
    free_cancellation TINYINT(1) DEFAULT 1,
    meal_plan VARCHAR(50) DEFAULT 'Room Only',
    total_rooms INT DEFAULT 100,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== PACKAGES =====================
CREATE TABLE IF NOT EXISTS packages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    nights INT DEFAULT 3,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    inclusions TEXT,
    highlights TEXT,
    emoji VARCHAR(10) DEFAULT '📦',
    tag VARCHAR(50),
    max_persons INT DEFAULT 4,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== TRAINS =====================
CREATE TABLE IF NOT EXISTS trains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    train_number VARCHAR(20) NOT NULL,
    train_name VARCHAR(100) NOT NULL,
    from_station VARCHAR(100) NOT NULL,
    to_station VARCHAR(100) NOT NULL,
    departure_time VARCHAR(10) NOT NULL,
    arrival_time VARCHAR(15) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    train_type VARCHAR(50),
    price_1a DECIMAL(8,2) DEFAULT 0,
    price_2a DECIMAL(8,2) DEFAULT 0,
    price_3a DECIMAL(8,2) DEFAULT 0,
    price_sl DECIMAL(8,2) DEFAULT 0,
    availability VARCHAR(30) DEFAULT 'Available',
    running_days VARCHAR(50) DEFAULT 'Daily',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== BUSES =====================
CREATE TABLE IF NOT EXISTS buses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operator_name VARCHAR(100) NOT NULL,
    from_city VARCHAR(80) NOT NULL,
    to_city VARCHAR(80) NOT NULL,
    departure_time VARCHAR(10) NOT NULL,
    arrival_time VARCHAR(15) NOT NULL,
    duration VARCHAR(20) NOT NULL,
    bus_type VARCHAR(80),
    price DECIMAL(8,2) NOT NULL,
    seats_available INT DEFAULT 30,
    rating DECIMAL(2,1) DEFAULT 4.0,
    amenities TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== CABS =====================
CREATE TABLE IF NOT EXISTS cabs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cab_type VARCHAR(80) NOT NULL,
    vehicle_name VARCHAR(100) NOT NULL,
    capacity INT DEFAULT 4,
    base_fare DECIMAL(8,2) NOT NULL,
    price_per_km DECIMAL(6,2) NOT NULL,
    min_km INT DEFAULT 80,
    amenities TEXT,
    emoji VARCHAR(10) DEFAULT '🚗',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== CRUISES =====================
CREATE TABLE IF NOT EXISTS cruises (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cruise_name VARCHAR(150) NOT NULL,
    ship_name VARCHAR(100) NOT NULL,
    from_port VARCHAR(100) NOT NULL,
    to_port VARCHAR(100) NOT NULL,
    departure_schedule VARCHAR(50),
    arrival_schedule VARCHAR(50),
    nights INT DEFAULT 1,
    price DECIMAL(10,2) NOT NULL,
    cruise_type VARCHAR(50),
    category VARCHAR(50) DEFAULT 'Domestic',
    inclusions TEXT,
    emoji VARCHAR(10) DEFAULT '🚢',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== BOOKINGS =====================
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_ref VARCHAR(20) UNIQUE NOT NULL,
    user_id INT NOT NULL,
    booking_type ENUM('Flight','Hotel','Package','Train','Bus','Cab','Cruise') NOT NULL,
    item_id INT NOT NULL,
    item_name VARCHAR(200) NOT NULL,
    travel_date DATE,
    passengers INT DEFAULT 1,
    base_amount DECIMAL(10,2) NOT NULL,
    tax_amount DECIMAL(10,2) DEFAULT 0,
    discount_amount DECIMAL(10,2) DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL,
    promo_code VARCHAR(30),
    payment_method VARCHAR(30) DEFAULT 'UPI',
    payment_status ENUM('Pending','Paid','Failed','Refunded') DEFAULT 'Paid',
    booking_status ENUM('Confirmed','Pending','Cancelled','Completed') DEFAULT 'Confirmed',
    passenger_name VARCHAR(100),
    passenger_email VARCHAR(150),
    passenger_phone VARCHAR(20),
    notes TEXT,
    pnr_number VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===================== PROMO CODES =====================
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(30) UNIQUE NOT NULL,
    description VARCHAR(150),
    discount_type ENUM('percentage','fixed') DEFAULT 'percentage',
    discount_value DECIMAL(8,2) NOT NULL,
    max_discount DECIMAL(8,2) DEFAULT 500,
    min_booking DECIMAL(8,2) DEFAULT 0,
    applicable_type VARCHAR(30) DEFAULT 'All',
    used_count INT DEFAULT 0,
    usage_limit INT DEFAULT 10000,
    valid_from DATE,
    valid_until DATE,
    status ENUM('Active','Expiring','Expired','Scheduled') DEFAULT 'Active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ===================== WISHLIST =====================
CREATE TABLE IF NOT EXISTS wishlists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_type ENUM('Flight','Hotel','Package','Train','Bus','Cruise') NOT NULL,
    item_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (user_id, item_type, item_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===================== REVIEWS =====================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    item_type VARCHAR(30) NOT NULL,
    item_id INT NOT NULL,
    item_name VARCHAR(150),
    rating TINYINT DEFAULT 5,
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ===================== SUPPORT TICKETS =====================
CREATE TABLE IF NOT EXISTS support_tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    booking_ref VARCHAR(20),
    subject VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    status ENUM('Open','In Progress','Resolved','Closed') DEFAULT 'Open',
    priority ENUM('Low','Medium','High','Urgent') DEFAULT 'Medium',
    admin_reply TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ===================== PASSWORD RESETS =====================
CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL,
    token VARCHAR(64) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_token (token),
    INDEX idx_email (email)
);

-- ===================== LOGIN ATTEMPTS (Rate Limiting) =====================
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip (ip_address),
    INDEX idx_email_time (email, attempted_at)
);

-- ===================== SEED DATA =====================

-- Admin user (password: admin123)
INSERT INTO users (name, email, password, phone, city, role, tier, total_spent, total_bookings) VALUES
('Admin TravelNest', 'admin@travelnest.com', 'admin123', '+91 98765 00001', 'Mumbai', 'admin', 'Platinum', 0, 0);

-- Demo users (password: demo123)
INSERT INTO users (name, email, password, phone, city, role, tier, total_spent, total_bookings) VALUES
('Arjun Mehta',    'user@demo.com',       'demo123', '+91 98765 43210', 'Mumbai',    'user', 'Gold',     124500, 8),
('Priya Sharma',   'priya@example.com',   'demo123', '+91 87654 32109', 'Delhi',     'user', 'Silver',    68200, 5),
('Rahul Verma',    'rahul@example.com',   'demo123', '+91 76543 21098', 'Bangalore', 'user', 'Bronze',    18600, 2),
('Sneha Patel',    'sneha@example.com',   'demo123', '+91 65432 10987', 'Ahmedabad', 'user', 'Silver',    52800, 4),
('Vikram Singh',   'vikram@example.com',  'demo123', '+91 54321 09876', 'Jaipur',    'user', 'Platinum', 218000,11),
('Ananya Iyer',    'ananya@example.com',  'demo123', '+91 43210 98765', 'Chennai',   'user', 'Bronze',    34500, 3),
('Rohan Gupta',    'rohan@example.com',   'demo123', '+91 32109 87654', 'Kolkata',   'user', 'Gold',      98400, 7),
('Kavya Nair',     'kavya@example.com',   'demo123', '+91 21098 76543', 'Kochi',     'user', 'Bronze',    22100, 2),
('Amit Joshi',     'amit@example.com',    'demo123', '+91 10987 65432', 'Pune',      'user', 'Gold',     168000, 9),
('Deepa Krishnan', 'deepa@example.com',   'demo123', '+91 99887 76655', 'Hyderabad', 'user', 'Silver',    88300, 6),
('Nikhil Agarwal', 'nikhil@example.com',  'demo123', '+91 88776 65544', 'Lucknow',   'user', 'Platinum', 312000,14),
('Farhan Khan',    'farhan@example.com',  'demo123', '+91 66554 43322', 'Mumbai',    'user', 'Platinum', 486000,18),
('Tanya Kapoor',   'tanya@example.com',   'demo123', '+91 55443 32211', 'Delhi',     'user', 'Gold',     142000,10),
('Ritu Bhatia',    'ritu@example.com',    'demo123', '+91 88665 44332', 'Noida',     'user', 'Platinum', 234000,12);

-- Flights
INSERT INTO flights (flight_code, airline, from_city, from_code, to_city, to_code, departure_time, arrival_time, duration, stops, price, class, seats_available, aircraft, terminal, baggage, emoji) VALUES
('6E-204',  'IndiGo',        'Mumbai',    'BOM', 'Delhi',     'DEL', '06:00', '08:20',    '2h 20m', 'Direct', 4299,  'Economy',  42, 'A320neo', 'T2', '15kg', '💙'),
('AI-101',  'Air India',     'Mumbai',    'BOM', 'Delhi',     'DEL', '08:30', '11:00',    '2h 30m', 'Direct', 5899,  'Economy',  18, 'B787',    'T2', '25kg', '❤️'),
('UK-965',  'Vistara',       'Mumbai',    'BOM', 'Delhi',     'DEL', '12:15', '14:45',    '2h 30m', 'Direct', 8200,  'Business', 10, 'A321',    'T2', '35kg', '💜'),
('SG-118',  'SpiceJet',      'Delhi',     'DEL', 'Bangalore', 'BLR', '10:45', '13:30',    '2h 45m', 'Direct', 3799,  'Economy',  30, 'B737',    'T1', '15kg', '🧡'),
('EK-500',  'Emirates',      'Mumbai',    'BOM', 'Dubai',     'DXB', '14:20', '16:50',    '2h 30m', 'Direct', 18500, 'Business',  8, 'A380',    'T2', '40kg', '🌟'),
('UK-985',  'Vistara',       'Bangalore', 'BLR', 'Goa',       'GOI', '07:15', '08:30',    '1h 15m', 'Direct', 2899,  'Economy',  55, 'A320',    'T1', '15kg', '💜'),
('AI-663',  'Air India',     'Chennai',   'MAA', 'Mumbai',    'BOM', '16:00', '18:15',    '2h 15m', 'Direct', 4100,  'Economy',  22, 'B737',    'T4', '25kg', '❤️'),
('6E-787',  'IndiGo',        'Delhi',     'DEL', 'Kolkata',   'CCU', '09:30', '12:00',    '2h 30m', 'Direct', 3599,  'Economy',  38, 'A321',    'T3', '15kg', '💙'),
('LH-761',  'Lufthansa',     'Mumbai',    'BOM', 'Frankfurt', 'FRA', '22:00', '05:30+1',  '9h 30m', 'Direct', 52000, 'Business',  4, 'B747',    'T2', '32kg', '🌍'),
('SQ-422',  'Singapore Air', 'Mumbai',    'BOM', 'Singapore', 'SIN', '11:50', '21:30',    '5h 40m', 'Direct', 28500, 'Business',  6, 'A350',    'T2', '35kg', '🦁'),
('6E-441',  'IndiGo',        'Hyderabad', 'HYD', 'Mumbai',    'BOM', '06:30', '08:15',    '1h 45m', 'Direct', 2799,  'Economy',  60, 'A320neo', 'T1', '15kg', '💙'),
('G8-116',  'GoFirst',       'Delhi',     'DEL', 'Goa',       'GOI', '12:00', '14:20',    '2h 20m', 'Direct', 3199,  'Economy',  45, 'A320',    'T1', '15kg', '🟢'),
('QR-556',  'Qatar Airways', 'Delhi',     'DEL', 'Doha',      'DOH', '23:45', '02:30+1',  '4h 45m', 'Direct', 31000, 'Business', 12, 'B787',    'T3', '40kg', '🟣'),
('UK-811',  'Vistara',       'Mumbai',    'BOM', 'Kolkata',   'CCU', '07:00', '09:45',    '2h 45m', 'Direct', 5200,  'Economy',  20, 'A320',    'T2', '20kg', '💜'),
('I5-710',  'AirAsia India', 'Bangalore', 'BLR', 'Delhi',     'DEL', '15:30', '18:30',    '3h 00m', 'Direct', 3499,  'Economy',  52, 'A320',    'T1', '15kg', '🔴'),
('AI-945',  'Air India',     'Delhi',     'DEL', 'London',    'LHR', '14:00', '19:30',    '9h 00m', 'Direct', 65000, 'Business',  9, 'B787',    'T3', '35kg', '❤️'),
('6E-2122', 'IndiGo',        'Pune',      'PNQ', 'Delhi',     'DEL', '05:45', '08:00',    '2h 15m', 'Direct', 3099,  'Economy',  48, 'A320neo', 'T1', '15kg', '💙'),
('SG-8991', 'SpiceJet',      'Ahmedabad', 'AMD', 'Mumbai',    'BOM', '11:00', '12:15',    '1h 15m', 'Direct', 1899,  'Economy',  62, 'B737',    'T1', '15kg', '🧡'),
('AF-218',  'Air France',    'Mumbai',    'BOM', 'Paris',     'CDG', '03:10', '09:45',    '10h 35m','1 Stop', 72000, 'Business',  5, 'A350',    'T2', '40kg', '🇫🇷'),
('QP-1345', 'Akasa Air',     'Mumbai',    'BOM', 'Bangalore', 'BLR', '17:45', '19:30',    '1h 45m', 'Direct', 2399,  'Economy',  68, 'B737MAX', 'T1', '15kg', '🌸'),
('EY-201',  'Etihad',        'Delhi',     'DEL', 'Abu Dhabi', 'AUH', '03:30', '06:00',    '3h 30m', 'Direct', 22000, 'Business',  7, 'A380',    'T3', '40kg', '🕌'),
('G8-501',  'GoFirst',       'Mumbai',    'BOM', 'Srinagar',  'SXR', '06:00', '08:30',    '2h 30m', 'Direct', 4800,  'Economy',  38, 'A320',    'T1', '15kg', '🟢'),
('AI-315',  'Air India',     'Kolkata',   'CCU', 'Bangkok',   'BKK', '13:45', '19:30',    '3h 45m', 'Direct', 19500, 'Economy',  25, 'B787',    'T1', '25kg', '❤️'),
('6E-5010', 'IndiGo',        'Mumbai',    'BOM', 'Jaipur',    'JAI', '07:45', '09:30',    '1h 45m', 'Direct', 2599,  'Economy',  55, 'A320neo', 'T1', '15kg', '💙'),
('TG-317',  'Thai Airways',  'Mumbai',    'BOM', 'Bangkok',   'BKK', '09:45', '15:30',    '5h 45m', 'Direct', 24000, 'Economy',  15, 'B777',    'T2', '30kg', '🇹🇭');

-- Hotels
INSERT INTO hotels (name, city, country, stars, rating, price_per_night, description, amenities, emoji, latitude, longitude, free_cancellation, meal_plan) VALUES
('The Taj Mahal Palace',     'Mumbai',    'India',       5, 9.4, 18500,  'Built in 1903, this iconic hotel overlooks the Gateway of India with 560 rooms and unrivalled Arabian Sea views.',  'Pool,Spa,7 Restaurants,Butler,Valet,Gym,Concierge',              '🏰', 18.9217, 72.8332, 1, 'Breakfast Included'),
('ITC Maurya',               'New Delhi', 'India',       5, 9.0, 15200,  'India iconic luxury hotel home to the legendary Bukhara restaurant, rated No.1 in India for 35 years.',            'Pool,Restaurant,Spa,Gym,Business Center',                        '🏛', 28.5975, 77.1721, 1, 'Room Only'),
('The Leela Palace',         'Bengaluru', 'India',       5, 9.4, 21000,  'Award-winning urban palace with acres of Mughal gardens. Home to the finest ESPA spa in India.',                   'Pool,Spa,Restaurant,Gym,Tennis,Helipad',                         '🏩', 12.9599, 77.6401, 0, 'Breakfast Included'),
('Grand Hyatt Goa',          'Goa',       'India',       5, 8.8, 12800,  'Sprawling beachside resort on Bambolim Bay with 6 outdoor pools, private beach, and 9 dining venues.',             'Beach,6 Pools,Restaurant,Bar,Spa,Water Sports',                  '🌴', 15.4009, 73.7979, 1, 'Breakfast Included'),
('JW Marriott Kolkata',      'Kolkata',   'India',       5, 8.9, 11200,  'Contemporary luxury hotel in New Town with Kolkata best rooftop pool and panoramic city views.',                   'Rooftop Pool,Restaurant,Spa,Gym,Lounge',                         '🏙', 22.5726, 88.3639, 1, 'Room Only'),
('Radisson Blu Chennai',     'Chennai',   'India',       4, 8.5,  7400,  'Contemporary business hotel near Chennai airport with panoramic views and express check-in.',                       'Pool,Restaurant,Gym,Airport Shuttle',                            '🏢', 13.0827, 80.2707, 1, 'Breakfast Included'),
('Burj Al Arab Jumeirah',    'Dubai',     'UAE',         5, 9.8, 185000, 'The world most luxurious hotel. Sail-shaped icon on its own island. All-suite with Rolls-Royce transfers.',         'Private Beach,Helipad,Spa,Michelin Restaurant,Butler,Rolls-Royce','⛵', 25.1412, 55.1853, 0, 'Full Board'),
('The Peninsula Paris',      'Paris',     'France',      5, 9.6,  92000, 'Palatial luxury on Avenue Kleber near the Arc de Triomphe. Rooftop restaurant with Eiffel Tower views.',           'Rooftop Restaurant,Spa,Pool,Butler,Rolls-Royce',                 '🗼', 48.8566,  2.3522, 0, 'Breakfast Included'),
('Park Hyatt Tokyo',         'Tokyo',     'Japan',       5, 9.3,  48000, 'Made famous by Lost in Translation, soaring floors 39-52 above Shinjuku with Mount Fuji views.',                   'Pool,Spa,Restaurant,Gym,Mt Fuji Views',                          '🏯', 35.6654,139.6907, 1, 'Breakfast Included'),
('Umaid Bhawan Palace',      'Jodhpur',   'India',       5, 9.5,  38000, 'Part of the royal palace of the Maharaja of Jodhpur. One of the world greatest heritage hotels.',                  'Pool,Spa,Royal Dining,Heritage Tours,Polo',                      '🏰', 26.2866, 73.0286, 0, 'Full Board'),
('The Oberoi Udaivilas',     'Udaipur',   'India',       5, 9.7,  55000, 'Voted World Best Hotel multiple times. Set on Lake Pichola with private boat to City Palace.',                     'Lake Pool,Spa,Boat,Butler,Heritage Tour,Fine Dining',            '🛕', 24.5744, 73.6802, 0, 'Full Board'),
('Taj Exotica Maldives',     'Maldives',  'Maldives',    5, 9.9, 145000, 'Voted No.1 Resort in Asia. Overwater villas on a private island with private plunge pool and butler.',             'Overwater Villa,Private Pool,Snorkeling,Diving,Spa,Butler',      '🐠',  3.9450, 73.3988, 0, 'Full Board'),
('Kumarakom Lake Resort',    'Kumarakom', 'India',       5, 9.2,  28500, 'Heritage Kerala resort on Vembanad Lake. Traditional architecture with Ayurveda treatments.',                      'Backwater Pool,Spa,Ayurveda,Houseboat,Yoga',                     '🌿',  9.6149, 76.4358, 1, 'Breakfast Included'),
('Singapore Marriott Tang',  'Singapore', 'Singapore',   5, 8.9,  32000, 'Iconic pagoda-style hotel on Orchard Road next to MRT. Best location for shopping in Asia.',                       'Pool,Spa,Restaurant,Gym,Orchard Rd',                            '🇸🇬',  1.3059,103.8317, 1, 'Breakfast Included'),
('Atlantis The Palm',        'Dubai',     'UAE',         5, 9.1,  62000, 'Epic resort on Palm Jumeirah with Aquaventure waterpark, private beach, 17 restaurants.',                          'Waterpark,Private Beach,17 Restaurants,Dolphin Bay,Kids Club',  '🔱', 25.1304, 55.1172, 0, 'Breakfast Included'),
('Aloft Ahmedabad',          'Ahmedabad', 'India',       4, 8.2,   5800, 'Stylish modern hotel in GIFT City with rooftop bar, co-working spaces, and direct metro access.',                  'Pool,Restaurant,Gym,Co-working,Rooftop Bar',                    '🏗', 23.0225, 72.5714, 1, 'Breakfast Included'),
('Four Seasons Mumbai',      'Mumbai',    'India',       5, 9.2,  24000, 'All-glass tower in Worli with 360-degree views of the Arabian Sea. Infinity pool on the 34th floor.',              'Infinity Pool,Spa,4 Restaurants,Butler,Concierge',               '🌆', 19.0176, 72.8562, 0, 'Room Only'),
('Ramada Jaipur',            'Jaipur',    'India',       4, 8.0,   6200, 'Well-appointed hotel near Jaipur airport with traditional Rajasthani decor and rooftop city views.',               'Pool,Restaurant,Gym,Desert Safari',                             '🏰', 26.9124, 75.7873, 1, 'Breakfast Included'),
('The Lalit Grand Palace',   'Srinagar',  'India',       5, 9.0,  16800, '19th-century royal palace on Dal Lake banks with mountain views and shikara rides.',                               'Lake Views,Garden,Restaurant,Shikara,Bonfire',                  '🏔', 34.0837, 74.7973, 1, 'Breakfast Included'),
('Westin Pune',              'Pune',      'India',       5, 8.7,  12200, 'Wellness-focused luxury hotel in Koregaon Park. Famous for Heavenly Beds and award-winning spa.',                  'Heavenly Spa,Pool,Restaurant,Yoga,Gym',                         '🌿', 18.5648, 73.9082, 1, 'Room Only');

-- Packages
INSERT INTO packages (name, destination, category, nights, price, description, inclusions, highlights, emoji, tag) VALUES
('Goa Beach Escape',        'Goa',              'Beach',         4,  18500,  'Perfect beach holiday with golden sands, water sports, and vibrant nightlife.',        'Flights (BOM-GOI)|4 Star Beach Resort|Breakfast Daily|Airport Transfers|Water Sports Day',        'North Goa sightseeing|Fort Aguada|Beach hopping|Anjuna Flea Market|Sunset at Vagator',           '🌊', 'Best Seller'),
('Kerala Backwaters',       'Kerala',           'Cultural',      5,  28500,  'Serene houseboat journey through Allepey backwaters with spice plantation visits.',    'Flights|Houseboat 2N + Resort 3N|All Meals|Transfers|Spice Plantation Tour',                    'Alleppey houseboat|Munnar tea gardens|Kathakali performance|Periyar Wildlife',                   '🌿', 'Nature'),
('Rajasthan Royal Tour',    'Rajasthan',        'Cultural',      7,  45000,  'Explore the majestic forts, palaces, and deserts of royal Rajasthan.',                 'Flights|Heritage Hotels 5 Star|Breakfast + Dinner|AC Coach|Camel Safari',                       'Jaipur City Palace|Jodhpur Blue City|Jaisalmer Desert Camp|Udaipur Lake Palace',                 '🏜', 'Heritage'),
('Bali Honeymoon Special',  'Bali',             'Honeymoon',     6,  75000,  'Romantic getaway to the Island of Gods with private villa and couples spa.',          'Return Flights|5 Star Villa Private Pool|Breakfast|Couples Spa 2 sessions|Romance Decoration',  'Tanah Lot sunset|Ubud rice terraces|Mount Batur sunrise trek|Seminyak beach',                   '💑', 'Honeymoon'),
('Dubai City & Desert',     'Dubai',            'International', 5,  62000,  'Experience towering skyscrapers, gold souks, and thrilling desert safaris.',          'Return Flights|4 Star Dubai Marina|Breakfast|Desert Safari|City Tour|Burj Khalifa Top',          'Burj Khalifa 148th floor|Dubai Mall Fountain|Gold Souk|Camel ride|Dhow cruise',                  '🌆', 'International'),
('Manali Snow Adventure',   'Manali',           'Mountain',      4,  15500,  'Thrilling snow adventure in the Himalayas with skiing and mountain excursions.',       'Volvo Bus|Snow View Hotel|Breakfast + Dinner|Skiing Solang Valley|Rohtang Pass',                 'Solang Valley skiing|Rohtang Pass|River rafting|Hidimba Temple|Mall Road',                       '⛷',  'Adventure'),
('Andaman Island Getaway',  'Andaman',          'Beach',         5,  35000,  'Crystal-clear waters, pristine beaches, and world-class snorkeling in Andaman.',      'Flights|Beach Resort|Breakfast|Snorkeling & Scuba|Havelock Ferry',                              'Radhanagar Beach Asia 3|Cellular Jail|Elephant Beach|Baratang Limestone Caves',                  '🐚', 'Island Paradise'),
('Singapore Family Fun',    'Singapore',        'International', 5,  88000,  'Magical family holiday with Universal Studios, Night Safari, and world-class dining.', 'Return Flights|4 Star Sentosa Resort|Breakfast|Universal Studios|Singapore Zoo',                'Universal Studios|Night Safari|Gardens by the Bay|Marina Bay Sands|Clarke Quay',                 '🎡', 'Family'),
('Uttarakhand Spiritual',   'Rishikesh',        'Spiritual',     6,  18000,  'Spiritual journey to the yoga capital with Ganga Aarti and meditation sessions.',     'Train Tickets|Ashram + Hotel|Satvik Meals|Yoga & Meditation|Ganga Aarti 3 evenings',            'Haridwar Ganga Aarti|Rishikesh rafting|Triveni Ghat|Beatles Ashram|Neelkanth',                   '🙏', 'Spiritual'),
('Ladakh Bike Expedition',  'Ladakh',           'Adventure',     8,  52000,  'Epic motorcycle journey on the world''s highest motorable roads through breathtaking Ladakh.','Flight to Leh|Royal Enfield + Fuel|Camping + Guesthouses|All Meals|Expert Guide',            'Khardung La worlds highest road|Pangong Tso Lake|Nubra Valley|Magnetic Hill',                   '🏍', 'Extreme'),
('Thailand Explorer',       'Bangkok & Phuket', 'International', 7,  68000,  'Discover Thailand''s vibrant capital and stunning southern islands in one trip.',     'Return Flights|Bangkok 3N + Phuket 4N|Breakfast|Phi Phi Islands|Elephant Sanctuary',           'Grand Palace|Wat Pho|Phi Phi Islands|Tiger Kingdom|Muay Thai show',                             '🙏', 'Popular'),
('Coorg Coffee Retreat',    'Coorg',            'Mountain',      3,  12500,  'Tranquil escape to the coffee plantations of Coorg with Ayurveda and nature walks.',  'AC Transport from Bengaluru|Plantation Resort 4 Star|All Meals|Coffee Plantation Tour',         'Coffee estate at dawn|Dubare Elephant Camp|Abbey Falls|Rajas Seat sunset',                       '☕', 'Weekend'),
('Jim Corbett Wildlife',    'Jim Corbett',      'Wildlife',      3,  22000,  'Thrilling wildlife safaris in India''s most famous tiger reserve.',                   'Delhi-Corbett Transfer|Jungle Luxury Resort|All Meals|3 Jeep Safaris|Elephant Safari',          'Tiger spotting safari|Elephant ride at dawn|580+ bird species|Kosi River fishing',               '🐯', 'Wildlife'),
('Maldives Luxury Escape',  'Maldives',         'Beach',         5,  145000, 'Ultimate luxury with overwater villa, full board dining, and private marine activities.','Flights + Seaplane|Overwater Villa 5 Star|Full Board|Diving + Snorkeling|Couples Spa 3 sessions','Overwater bungalow sunrise|House reef snorkeling|Underwater restaurant|Sunset dolphin cruise',   '🐠', 'Ultra Luxury'),
('Nepal Everest Base Camp', 'Nepal',            'Adventure',     12, 95000,  'The ultimate trekking challenge to the base of the world''s highest mountain.',       'Flights to Kathmandu|Tea House Accommodation|All Meals on Trek|Sherpa Guide|Porter Service',    'EBC at 5364m|Kala Patthar viewpoint|Namche Bazaar|Tengboche Monastery|Lukla flight',             '🏔', 'Bucket List'),
('Kashmir Heaven',          'Srinagar',         'Mountain',      5,  24500,  'Breathtaking valley of snow-capped peaks, Dal Lake shikara rides, and alpine meadows.','Flights to Srinagar|Houseboat 2N + Hotel 3N|Breakfast + Dinner|Shikara Ride|Gulmarg Gondola',  'Dal Lake Shikara ride|Gulmarg Gondola ride|Pahalgam valley|Mughal Gardens|Sonamarg glacier',    '❄️', 'Scenic'),
('Switzerland Dream',       'Switzerland',      'International', 8,  185000, 'Fairytale landscapes of snow-capped Alps, crystal lakes, and chocolate-box towns.',   'Return Flights|4 Star Hotels|Breakfast Daily|Swiss Travel Pass|Jungfrau Top of Europe',         'Jungfraujoch Top of Europe|Interlaken Paragliding|Lucerne Chapel Bridge|Zermatt Matterhorn',     '🏔', 'Premium'),
('Vietnam Heritage Trail',  'Vietnam',          'Cultural',      8,  72000,  'Journey through Vietnam''s stunning landscapes, ancient towns, and UNESCO heritage.', 'Return Flights|Hotels Hanoi+Ha Long+Hoi An|Breakfast|Ha Long Bay Cruise 2N|Hoi An Lantern Tour','Ha Long Bay cruise|Hoi An Ancient Town|Hanoi Old Quarter|Cu Chi Tunnels|Mekong Delta',           '🏮', 'International'),
('Meghalaya Discovery',     'Meghalaya',        'Adventure',     6,  32000,  'Explore the wettest place on earth, living root bridges, and crystal-clear rivers.',  'Flights to Guwahati|Eco Resort|Breakfast + Dinner|Root Bridge Trek|Dawki River',                'Double Decker Root Bridge|Dawki Crystal River|Mawsynram|Nohkalikai Falls|Cherrapunji caves',     '🌧', 'Off-beat'),
('Bhutan Spiritual Tour',   'Bhutan',           'Spiritual',     7,  82000,  'Discover the Land of Thunder Dragon with ancient monasteries and pristine landscapes.','Return Flights|3 Star Hotels|Full Board|Tiger''s Nest Trek|City Tours',                         'Paro Taktsang Tiger''s Nest|Thimphu Dzong|Punakha monastery|Dochula Pass|Rice wine ceremony',   '⛩️', 'Cultural');

-- Trains
INSERT INTO trains (train_number, train_name, from_station, to_station, departure_time, arrival_time, duration, train_type, price_1a, price_2a, price_3a, price_sl, availability, running_days) VALUES
('12951', 'Mumbai Rajdhani',          'Mumbai CSTM',          'New Delhi',          '16:35', '08:35+1', '16h 00m', 'Rajdhani',      3865, 2240, 1595,    0, 'Available', 'Daily'),
('12301', 'Howrah Rajdhani',          'Howrah',               'New Delhi',          '14:05', '10:05+1', '20h 00m', 'Rajdhani',      4115, 2390, 1720,    0, 'Available', 'Daily'),
('22221', 'CSMT Rajdhani',            'Mumbai CSMT',          'Hazrat Nizamuddin',  '16:35', '09:55+1', '17h 20m', 'Rajdhani',      4015, 2280, 1640,    0, 'Available', 'Daily'),
('12009', 'Mumbai-Ahmedabad Shatabdi','Mumbai Central',       'Ahmedabad',          '06:25', '12:55',   '6h 30m',  'Shatabdi',         0, 1015,    0,    0, 'Available', 'Daily'),
('12002', 'New Delhi Shatabdi',       'New Delhi',            'Bhopal',             '06:00', '13:55',   '7h 55m',  'Shatabdi',         0, 1365,    0,    0, 'Available', 'Daily'),
('22439', 'Vande Bharat Delhi-VNS',   'New Delhi',            'Varanasi',           '06:00', '14:00',   '8h 00m',  'Vande Bharat',     0, 1795,    0,    0, 'Available', 'Daily'),
('20901', 'Vande Bharat CSMT-Solapur','CSMT Mumbai',          'Solapur',            '06:10', '13:05',   '6h 55m',  'Vande Bharat',     0, 1305,    0,    0, 'Available', 'Daily'),
('12627', 'Karnataka Express',        'New Delhi',            'Bangalore City',     '22:30', '05:30+2', '31h 00m', 'Express',          0, 2580, 1840,  690, 'Available', 'Daily'),
('22119', 'Mumbai-Karmali Tejas',     'Mumbai CSMT',          'Karmali (Goa)',      '05:25', '14:35',   '9h 10m',  'Tejas',            0, 1685,    0,    0, 'Available', 'Mon/Fri/Sat'),
('12953', 'August Kranti Rajdhani',   'Mumbai Central',       'Hazrat Nizamuddin',  '17:40', '10:55+1', '17h 15m', 'Rajdhani',      4015, 2280, 1640,    0, 'Available', 'Daily'),
('12046', 'Chandigarh Shatabdi',      'New Delhi',            'Chandigarh',         '07:40', '11:30',   '3h 50m',  'Shatabdi',         0,  720,    0,    0, 'Available', 'Daily'),
('22691', 'Delhi-Bangalore Rajdhani', 'New Delhi',            'Bangalore City',     '20:00', '06:40+2', '34h 40m', 'Rajdhani',      5405, 3100, 2190,    0, 'Available', 'Daily'),
('12621', 'Tamil Nadu Express',       'New Delhi',            'Chennai Central',    '22:30', '07:40+2', '33h 10m', 'Superfast',        0, 2680, 1900,  760, 'WL 4',      'Daily'),
('12259', 'Sealdah Duronto',          'Sealdah',              'New Delhi',          '20:05', '12:55+1', '16h 50m', 'Duronto',          0, 2290, 1620,    0, 'WL 12',     'Mon/Thu'),
('20503', 'Vande Bharat Guwahati',    'New Delhi',            'Guwahati',           '15:50', '17:00+1', '25h 10m', 'Vande Bharat',     0, 3085,    0,    0, 'Available', 'Tue/Fri/Sun'),
('18477', 'Utkal Express',            'Puri',                 'Haridwar',           '11:00', '19:35+1', '32h 35m', 'Express',          0, 2190, 1565,  625, 'RAC 5',     'Daily'),
('12417', 'Prayagraj Express',        'New Delhi',            'Prayagraj Jn',       '22:30', '07:30+1', '9h 00m',  'Express',          0, 1105,  795,  315, 'Available', 'Daily'),
('12057', 'Jan Shatabdi Dehradun',    'New Delhi',            'Dehradun',           '06:10', '11:35',   '5h 25m',  'Jan Shatabdi',     0,  545,    0,  225, 'Available', 'Daily'),
('22825', 'Shalimar Vande Bharat',    'Shalimar (Howrah)',    'Visakhapatnam',      '05:45', '15:05',   '9h 20m',  'Vande Bharat',     0, 1845,    0,    0, 'Available', 'Daily'),
('12101', 'Jnaneswari Super Deluxe',  'Mumbai CSMT',          'Howrah',             '22:05', '02:05+2', '28h 00m', 'Superfast',        0, 2290, 1640,  650, 'Available', 'Daily');

-- Buses
INSERT INTO buses (operator_name, from_city, to_city, departure_time, arrival_time, duration, bus_type, price, seats_available, rating, amenities) VALUES
('Neeta Travels',       'Pune',      'Mumbai',    '06:00', '09:30',    '3h 30m',  'Volvo AC Sleeper',         350, 22, 4.5, 'AC,USB Charging,Blanket,Water Bottle'),
('VRL Travels',         'Bangalore', 'Goa',       '21:30', '08:00+1',  '10h 30m', 'Multi-Axle AC Sleeper',    750, 18, 4.4, 'AC,Charging Point,Blanket,Reading Light'),
('Sharma Transports',   'Delhi',     'Jaipur',    '07:00', '12:30',    '5h 30m',  'Volvo AC Semi-Sleeper',    480, 30, 4.2, 'AC,USB Charging,Movie Screen,Snacks'),
('Karnataka SRTC',      'Bangalore', 'Mysore',    '06:30', '10:00',    '3h 30m',  'Airavata Club Class',      290, 42, 4.3, 'AC,Entertainment System,Reclining Seats'),
('Orange Travels',      'Chennai',   'Bangalore', '22:00', '06:00+1',  '8h 00m',  'AC Sleeper',               620, 25, 4.6, 'AC,Charging,Blanket,Curtain Privacy'),
('Paulo Travels',       'Mumbai',    'Goa',       '22:00', '08:30+1',  '10h 30m', 'Volvo AC Sleeper 2x1',     950, 16, 4.7, 'AC,2x1 Seating,Blanket,Charging,Meals Included'),
('MSRTC Shivneri',      'Pune',      'Mumbai',    '05:30', '08:45',    '3h 15m',  'AC Deluxe',                230, 38, 4.1, 'AC,Reclining Seats,Water Bottle'),
('Raj National Express','Ahmedabad', 'Mumbai',    '21:00', '07:30+1',  '10h 30m', 'AC Sleeper',               780, 20, 4.0, 'AC,Charging,Blanket,Snacks'),
('SRS Travels',         'Hyderabad', 'Bangalore', '21:30', '07:00+1',  '9h 30m',  'Multi-Axle AC Sleeper',    690, 22, 4.3, 'AC,Charging,Blanket,Water'),
('Parveen Travels',     'Chennai',   'Coimbatore','22:30', '05:30+1',  '7h 00m',  'AC Semi-Sleeper',          420, 32, 4.2, 'AC,Charging,Reclining Seats'),
('Green Line Travels',  'Delhi',     'Agra',      '07:00', '10:30',    '3h 30m',  'AC Luxury',                380, 28, 4.4, 'AC,Wi-Fi,Water,Snacks'),
('Kallada Travels',     'Kochi',     'Bangalore', '20:00', '07:30+1',  '11h 30m', 'Multi-Axle AC Sleeper',    850, 20, 4.5, 'AC,Charging,Blanket,2x1 Seating'),
('RSRTC Volvo',         'Jaipur',    'Delhi',     '22:00', '05:30+1',  '7h 30m',  'Volvo AC Sleeper',         550, 30, 4.1, 'AC,Blanket,Charging'),
('Paulo Travels',       'Panaji',    'Mumbai',    '21:30', '09:00+1',  '11h 30m', 'AC Sleeper',               720, 24, 4.2, 'AC,Blanket,Snacks,Charging'),
('Hans Travels',        'Ahmedabad', 'Surat',     '06:30', '10:30',    '4h 00m',  'Non-AC Express',           180, 45, 3.8, 'Reclining Seats,Water');

-- Cabs
INSERT INTO cabs (cab_type, vehicle_name, capacity, base_fare, price_per_km, min_km, amenities, emoji) VALUES
('Hatchback', 'Maruti Suzuki Swift', 4, 49, 12, 80, 'AC,Music', '🚗'),
('Hatchback', 'Hyundai i20', 4, 49, 12, 80, 'AC,Music,Spacious Boot', '🚗'),
('Hatchback', 'Tata Tiago', 4, 39, 9, 60, 'AC,Music', '🚗'),
('Hatchback', 'Maruti Suzuki Baleno', 4, 49, 12, 80, 'AC,Music,Spacious Boot', '🚗'),
('Hatchback', 'Renault Kwid', 4, 45, 10, 60, 'AC,Music', '�'),
('Sedan', 'Maruti Suzuki Dzire', 4, 59, 14, 80, 'AC,Music,Spacious Boot', '�'),
('Sedan', 'Honda City', 4, 69, 16, 80, 'AC,Music,Leather Seats', '�'),
('Sedan', 'Hyundai Verna', 4, 69, 16, 80, 'AC,Music,Leather Seats', '�'),
('Sedan', 'Volkswagen Virtus', 4, 75, 16, 80, 'AC,Music,Leather Seats', '�'),
('SUV / MUV', 'Hyundai Creta', 5, 79, 18, 100, 'AC,Music,Spacious,USB', '🚐'),
('SUV / MUV', 'Tata Nexon', 5, 75, 18, 100, 'AC,Music,Spacious,USB', '🚐'),
('SUV / MUV', 'Kia Seltos', 5, 79, 18, 100, 'AC,Music,Spacious,USB', '🚐'),
('SUV / MUV', 'Toyota Innova Crysta', 7, 89, 20, 100, 'AC,Music,Spacious,USB', '🚐'),
('SUV / MUV', 'Maruti Suzuki Ertiga', 7, 75, 18, 100, 'AC,Music,Spacious,USB', '🚐'),
('SUV / MUV', 'Mahindra XUV700', 7, 85, 20, 100, 'AC,Music,Spacious,USB,Sunroof', '🚐'),
('Premium / Luxury SUV', 'Toyota Fortuner', 7, 99, 24, 120, 'AC,Music,Sunroof,USB,Leather', '🛻'),
('Premium / Luxury SUV', 'Jeep Compass', 5, 95, 22, 120, 'AC,Music,Sunroof,USB,Leather', '🛻'),
('Luxury Sedan', 'Mercedes-Benz E-Class', 4, 199, 45, 160, 'AC,Premium Sound,Wi-Fi,Leather', '💎'),
('Luxury Sedan', 'BMW 5 Series', 4, 199, 45, 160, 'AC,Premium Sound,Wi-Fi,Leather', '💎'),
('Luxury Sedan', 'Audi A6', 4, 199, 45, 160, 'AC,Premium Sound,Wi-Fi,Leather', '💎');

  -- Cruises
INSERT INTO cruises (cruise_name, ship_name, from_port, to_port, departure_schedule, arrival_schedule, nights, price, cruise_type, category, inclusions, emoji) VALUES
('Goa Luxury Weekend',      'MV Angriya',               'Mumbai',    'Goa',                          '16:00 Fri',  '07:00 Sat', 1,      8500,  'Overnight',    'Domestic',      'Cabin,All Meals,Entertainment,Sunset Deck',                        '🌊'),
('Lakshadweep Discovery',   'MV Kavaratti',             'Kochi',     'Lakshadweep Islands',          'Mon 09:00',  'Fri 08:00', 4,     22000,  'Island',       'Domestic',      'Cabin,All Meals,Snorkeling,Island Tour',                            '🏝'),
('Andaman Island Sail',     'MV Akbar',                 'Chennai',   'Port Blair',                   'Thu 10:00',  'Mon 06:00', 3,     15000,  'Coastal',      'Domestic',      '4-Berth Cabin,All Meals,Deck Access',                              '🐚'),
('Arabian Sea Explorer',    'Costa Fortuna',            'Mumbai',    'Mumbai via Maldives',          '10:00 Sat',  '10:00 Fri', 6,     58000,  'International','International', 'Ocean View Cabin,All Meals,Entertainment,Gym,Pool',                 '⛵'),
('Mediterranean Dream',     'MSC Bellissima',           'Barcelona', 'Barcelona Round Trip',         '16:00 Sun',  '08:00 Sun', 10,   125000,  'European',     'International', 'Balcony Cabin,All Meals,Shows,3 Port Tours',                        '🗺'),
('Nile River Luxury',       'Oberoi Zahra',             'Luxor',     'Aswan',                        'Mon 09:00',  'Fri 18:00', 4,     95000,  'River',        'International', 'Suite,All Meals,Temple Tours,Egyptologist Guide',                   '🏺'),
('Kerala Backwaters Cruise','Swagatham Heritage',       'Alleppey',  'Kollam',                       'Daily 10:30','17:30',     0,      1200,  'Day Cruise',   'Domestic',      'Lunch,Coconut Water,Live Commentary',                              '🌿'),
('Norwegian Fjords',        'Hurtigruten MS Kong Harald','Bergen',   'Kirkenes',                     'Daily 20:00','Day 12',    11,   185000,  'Expedition',   'International', 'Cabin,All Meals,Northern Lights Excursion,Fjord Kayaking',          '🇳🇴');

-- Promo codes
INSERT INTO promo_codes (code, description, discount_type, discount_value, max_discount, min_booking, applicable_type, used_count, usage_limit, valid_from, valid_until, status) VALUES
('FIRST50',    '50% off your first booking',          'percentage', 50,   500,  500,   'All',      5284,  10000, '2026-01-01', '2026-03-31', 'Active'),
('SUMMER25',   '25% off summer travel',               'percentage', 25,   750,  1000,  'All',      8821,  20000, '2026-03-01', '2026-04-30', 'Active'),
('FLIGHT15',   '15% off on all flights',              'percentage', 15,   1000, 2000,  'Flight',   3892,   5000, '2026-01-01', '2026-03-31', 'Active'),
('HOTEL20',    '20% off hotel bookings',              'percentage', 20,   2000, 3000,  'Hotel',    4456,   8000, '2026-02-01', '2026-04-30', 'Active'),
('PREMIUM30',  '30% off for premium members',         'percentage', 30,   3000, 5000,  'All',      1344,   2000, '2026-01-01', '2026-04-15', 'Active'),
('HOLI2026',   'Holi Festival 40% off',               'percentage', 40,   1500, 1000,  'All',      7234,   8000, '2026-03-10', '2026-03-20', 'Expiring'),
('NEWUSER',    'Rs.250 off for new users',             'fixed',      250,  250,  500,   'All',     18921, 100000, '2026-01-01', '2026-12-31', 'Active'),
('TRAINPASS',  '10% off on train tickets',            'percentage', 10,   200,  500,   'Train',    2103,   5000, '2026-01-01', '2026-04-30', 'Active'),
('BUSGO',      '20% off bus bookings',                'percentage', 20,   150,  300,   'Bus',      1890,   3000, '2026-01-01', '2026-04-30', 'Active'),
('PLATINUM40', '40% off exclusive for Platinum',      'percentage', 40,   5000, 10000, 'All',       234,   1000, '2026-01-01', '2026-12-31', 'Active'),
('COUPLE20',   '20% off on honeymoon packages',       'percentage', 20,   2500, 5000,  'Package',  3456,   5000, '2026-01-01', '2027-02-14', 'Active'),
('DIWALI50',   '50% off Diwali special',              'percentage', 50,   3000, 2000,  'All',         0,  10000, '2026-10-20', '2026-10-25', 'Scheduled');

-- Sample bookings
INSERT INTO bookings (booking_ref, user_id, booking_type, item_id, item_name, travel_date, passengers, base_amount, tax_amount, total_amount, payment_method, booking_status, passenger_name, passenger_email, passenger_phone, pnr_number) VALUES
('TN10001', 2, 'Flight',  1,  '6E-204 Mumbai → Delhi',           '2026-03-15', 1, 4299,  516,  4815,  'UPI',         'Confirmed', 'Arjun Mehta',    'arjun@example.com', '+91 98765 43210', 'PNR8374651902'),
('TN10002', 2, 'Hotel',   1,  'The Taj Mahal Palace, Mumbai',     '2026-03-20', 1,18500, 2220, 20720,  'Credit Card', 'Confirmed', 'Arjun Mehta',    'arjun@example.com', '+91 98765 43210', 'PNR9182736450'),
('TN10003', 2, 'Package', 1,  'Goa Beach Escape',                 '2026-04-01', 2,18500, 2220, 20720,  'UPI',         'Confirmed', 'Arjun Mehta',    'arjun@example.com', '+91 98765 43210', 'PNR1234567890'),
('TN10004', 3, 'Train',   1,  '12951 Mumbai Rajdhani',            '2026-03-18', 1, 2240,    0,  2240,  'Net Banking', 'Confirmed', 'Priya Sharma',   'priya@example.com', '+91 87654 32109', 'PNR5647382910'),
('TN10005', 5, 'Flight', 13,  'QR-556 Delhi → Doha',             '2026-03-25', 1,31000, 3720, 34720,  'Credit Card', 'Confirmed', 'Vikram Singh',   'vikram@example.com','+91 54321 09876', 'PNR6758493021'),
('TN10006', 7, 'Hotel',   7,  'Burj Al Arab Jumeirah, Dubai',     '2026-04-10', 1,185000,22200,207200, 'Credit Card', 'Confirmed', 'Rohan Gupta',    'rohan@example.com', '+91 32109 87654', 'PNR7869504132'),
('TN10007',11, 'Package',14,  'Maldives Luxury Escape',           '2026-05-01', 2,145000,17400,162400, 'Net Banking', 'Confirmed', 'Nikhil Agarwal', 'nikhil@example.com','+91 88776 65544', 'PNR8970615243'),
('TN10008', 9, 'Bus',     6,  'Paulo Travels: Mumbai → Goa',      '2026-03-22', 1,  950,    0,   950,  'UPI',         'Confirmed', 'Amit Joshi',     'amit@example.com',  '+91 10987 65432', 'PNR9081726354'),
('TN10009', 4, 'Cruise',  1,  'Goa Luxury Weekend - MV Angriya',  '2026-04-18', 2, 8500, 1020,  9520,  'UPI',         'Pending',   'Sneha Patel',    'sneha@example.com', '+91 65432 10987', 'PNR1092837465'),
('TN10010', 6, 'Cab',     6,  'Mercedes E-Class Airport Transfer','2026-03-16', 1,  199,    0,   199,  'Wallet',      'Confirmed', 'Ananya Iyer',    'ananya@example.com','+91 43210 98765', 'PNR2103948576');

-- Reviews
INSERT INTO reviews (user_id, item_type, item_id, item_name, rating, comment) VALUES
(2,  'Hotel',   1, 'The Taj Mahal Palace',        5, 'Absolutely stunning. The sea-facing room was worth every rupee. Impeccable service throughout.'),
(2,  'Flight',  1, '6E-204 Mumbai-Delhi',         4, 'On time departure, clean aircraft, friendly cabin crew. Good value for money.'),
(5,  'Package', 1, 'Goa Beach Escape',            5, 'Best family trip ever. Everything was perfectly organised. Will book again!'),
(3,  'Train',   1, '12951 Mumbai Rajdhani',       4, 'Comfortable journey, food was decent, arrived exactly on time. 2A is worth it.'),
(7,  'Hotel',   4, 'Grand Hyatt Goa',             5, 'Six pools! We never felt the need to leave the resort. Incredible value and service.'),
(9,  'Bus',     6, 'Paulo Travels Mumbai-Goa',    5, 'The 2x1 Volvo sleeper is very comfortable. Reached on time. Clean and well-maintained.'),
(11, 'Package',14, 'Maldives Luxury Escape',      5, 'The overwater villa was a dream come true. TravelNest organised everything flawlessly.'),
(10, 'Hotel',   9, 'Park Hyatt Tokyo',            5, 'The 47th-floor pool with Mt Fuji view is something else entirely. Unforgettable stay.'),
(9,  'Flight', 13, 'QR-556 Delhi-Doha',           5, 'Qatar Business Class is exceptional. The lay-flat bed made it feel like a hotel.'),
(4,  'Package', 3, 'Rajasthan Royal Tour',        4, 'Heritage hotels were magnificent. The Camel Safari guide was very knowledgeable.');
