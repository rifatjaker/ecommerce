// Global configuration for Eyesome Sports E-commerce (Frontend)

const CONFIG = {
    // API endpoints
    API_BASE_URL: 'https://ihr1.bd24.top/shahedsir_api_endpoint',
    API_ENDPOINTS: {
        CATEGORIES: '/get_categories.php',
        CREATE_CATEGORY: '/create_category.php',
        CLUBS: '/get_clubs.php',
        CREATE_CLUB: '/create_club.php',
        UPDATE_CLUB: '/update_club.php',
        DELETE_CLUB: '/delete_club.php',
        PRODUCTS: '/get_products.php',
        CREATE_PRODUCT: '/create_product.php',
        UPDATE_PRODUCT: '/update_product.php',
        DELETE_PRODUCT: '/delete_product.php',
        HOMEPAGE_DATA: '/get_homepage_data.php'
    },
    
    // Site settings
    SITE_NAME: 'Eyesome Sports',
    SITE_URL: 'https://ihr1.bd24.top/shahedsir',
    
    // File upload settings
    ALLOWED_IMAGE_TYPES: ['image/jpeg', 'image/png'],
    MAX_FILE_SIZE: 5 * 1024 * 1024, // 5MB
    
    // Helper function to get full API URL
    getApiUrl: function(endpoint) {
        return this.API_BASE_URL + endpoint;
    },
    
    // Helper function to validate file type
    isValidImageType: function(file) {
        return this.ALLOWED_IMAGE_TYPES.includes(file.type);
    },
    
    // Helper function to validate file size
    isValidFileSize: function(file) {
        return file.size <= this.MAX_FILE_SIZE;
    }
};

// Make it available globally
window.CONFIG = CONFIG;
