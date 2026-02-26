Laravel Practical Test: API-Based System
Scenario:
Build an API system in Laravel 10 to manage users, devices, and support tickets. You need to build authentication (signup + login), device management, ticket handling, and listings with filtering, sorting, and pagination.
________________________________________
Task Breakdown
________________________________________
1️⃣ User Signup API
● POST /api/signup
● Fields:
○ name
○ email (must be unique)
○ password
○ confirm_password (must match password)
● Validations:
○ Email must be unique (users.email)
○ Password min: 6, confirmation required
● Action:
○ Store user
○ Hash password
○ Return success response
○ Add status field (active/inactive, default active)
○ Return API token on successful signup (Sanctum)
○ Use Form Request validation class
________________________________________
2️⃣ User Login API
● POST /api/login
● Fields:
○ email
○ password
● Logic:
○ Validate credentials
○ On success, return success
○ Allow login only if user status is active
○ Implement login attempt limit (max 5 attempts)
○ Return structured JSON response with proper HTTP status codes
________________________________________
3️⃣ Add Device API
● POST /api/devices
● Protected (auth required)
● Fields:
○ name
○ type
○ unique_num (must be unique)
○ os
● Validation:
○ unique_num must be unique
○ Add purchase_date
○ Add warranty_expiry (must be greater than purchase_date)
○ Store created_by (logged-in user id)
○ Use database indexing on unique_num
________________________________________
4️⃣ List Users & Devices with Pagination, Sort, Search
● GET /api/users
● GET /api/devices
● Protected (auth required)
● Features:
○ ?search=... → Search by name or email (users) or name/type/unique_num (devices)
○ ?sort_by=name&sort_order=asc
○ ?page=1&per_page=10
○ Add filtering:
  ▪ Devices by OS
  ▪ Devices by warranty status (expired / active)
○ Return total_records and total_pages in response
○ Use Eager Loading to avoid N+1 problem
________________________________________
5️⃣ Allocate Multiple Devices to a User
● POST /api/allocate-devices
● Protected (auth required)
● Fields:
○ user_id
○ device_ids (array of device IDs)
● Logic:
○ Many-to-many relation via pivot table device_user
○ Avoid duplicate allocations
○ Add allocated_at column in pivot table
○ Use DB Transaction for allocation process
○ Prevent allocation if device already allocated to 3 users
○ Create API to deallocate device from user
________________________________________
6️⃣ Create Ticket for a Device
● POST /api/tickets
● Protected (auth required)
● Fields:
○ device_id
○ description (optional)
● Logic:
○ Create a ticket
○ Generate ticket_num (e.g., TCKT0001, auto-incremental format)
○ Add status field (open, in_progress, closed — default open)
○ Prevent ticket creation if warranty is expired
○ Ticket number must be unique
○ Use Service class for ticket number generation logic
________________________________________
7️⃣ Ticket Listing API
● GET /api/tickets
● Protected (auth required)
● Features:
○ Search by ticket number, device unique number, or device type
○ Sort by ticket number or created date
○ Pagination (?page=1&per_page=10)
○ Filter by status
○ Filter by date range (from_date, to_date)
○ Eager load related device details
○ Add endpoint to update ticket status (open → in_progress → closed only)
________________________________________
Database Tables Overview
users
| id | name | email | password | status |
devices
| id | name | type | unique_num | os | purchase_date | warranty_expiry | created_by |
device_user
| id | user_id | device_id | allocated_at |
tickets
| id | ticket_num | device_id | description | status | created_at |