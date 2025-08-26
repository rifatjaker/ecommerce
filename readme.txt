✅ Sections Included
======================
Hero Banner
Featured Categories
Trending Products
Promotional Banner
Newsletter Signup
Footer with Links

Would you like:
Product filtering or sorting?
Cart functionality using JavaScript or PHP?
Admin dashboard


admin/
├── includes/
│   └── navbar.php          # Shared admin navigation
├── add_category.html       # Categories management (updated)
├── add_club.html          # Clubs management (new)
└── [future pages...]

api_endpoint/
├── create_club.php        # Club creation API
├── get_clubs.php          # Clubs retrieval API
└── [existing endpoints...]


=================================
Visit: /admin/create_admin.php
Default login credentials:
Username: admin
Password: admin123
=================================

Visit: /admin/login.php
Session Management:
30-minute timeout with automatic refresh
Secure session storage with proper cleanup
Cross-page authentication checks

=================================
Database Structure:
admin_users - User accounts and roles
admin_login_logs - Login attempt tracking
admin_activity_logs - Action audit trails