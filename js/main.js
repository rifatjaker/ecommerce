// Product filter and search functionality
const categoryFilter = document.getElementById('categoryFilter');
const searchInput = document.getElementById('searchInput');
const productList = document.getElementById('productList');
const searchSuggestions = document.getElementById('searchSuggestions');

// Typeahead search variables
let allProducts = [];
let currentFocus = -1;
let searchTimeout;

// Initial filter to show all products
function filterProducts() {
  const category = categoryFilter.value.toLowerCase();
  const searchText = searchInput.value.toLowerCase();
  const cards = productList.querySelectorAll('.product-card');

  cards.forEach(card => {
    const cardCategory = card.getAttribute('data-category');
    const cardText = card.textContent.toLowerCase();
    const matchCategory = category === 'all' || cardCategory === category;
    const matchSearch = cardText.includes(searchText);

    card.style.display = matchCategory && matchSearch ? 'block' : 'none';
  });
}

// Load products for typeahead search
async function loadProductsForSearch() {
  try {
    // Show loading state
    searchInput.placeholder = 'Loading products...';
    // Use the same API endpoint as the main data loading
    const response = await fetch('https://ihr1.bd24.top/shahedsir_api_endpoint/get_homepage_data.php');
    const data = await response.json();
    allProducts = data.product_list || [];
    // Reset placeholder
    searchInput.placeholder = 'Search products...';
  } catch (error) {
    console.error('Failed to load products for search:', error);
    searchInput.placeholder = 'Search products...';
  }
}

// Filter products for typeahead suggestions
function filterProductsForTypeahead(query) {
  if (!query.trim()) return [];
  
  const searchTerm = query.toLowerCase();
  return allProducts.filter(product => 
    product.name.toLowerCase().includes(searchTerm) ||
    product.category.toLowerCase().includes(searchTerm) ||
    product.description.toLowerCase().includes(searchTerm)
  ).slice(0, 8); // Limit to 8 suggestions
}

// Create suggestion item HTML
function createSuggestionItem(product, query) {
  const name = product.name;
  const highlightedName = name.replace(new RegExp(query, 'gi'), match => `<strong>${match}</strong>`);
  
  return `
    <div class="suggestion-item" data-product-id="${product.id}">
      <div class="d-flex align-items-center">
        <img src="${product.image_url}" 
             alt="${product.name}" 
             style="width: 40px; height: 40px; object-fit: cover; border-radius: 4px; margin-right: 12px;">
        <div class="flex-grow-1">
          <div class="suggestion-title">${highlightedName}</div>
          <div class="suggestion-category text-muted small">${product.category} • ৳${product.price}</div>
        </div>
      </div>
    </div>
  `;
}

// Show typeahead suggestions
function showSuggestions(query) {
  const filteredProducts = filterProductsForTypeahead(query);
  
  if (filteredProducts.length === 0) {
    searchSuggestions.classList.add('d-none');
    return;
  }

  const suggestionsHTML = filteredProducts.map(product => 
    createSuggestionItem(product, query)
  ).join('');

  searchSuggestions.innerHTML = suggestionsHTML;
  searchSuggestions.classList.remove('d-none');
  currentFocus = -1;
}

// Hide suggestions
function hideSuggestions() {
  searchSuggestions.classList.add('d-none');
  currentFocus = -1;
}

// Handle keyboard navigation
function handleKeyNavigation(e) {
  const items = searchSuggestions.querySelectorAll('.suggestion-item');
  
  if (e.key === 'ArrowDown') {
    e.preventDefault();
    currentFocus = (currentFocus + 1) % items.length;
    updateActiveItem(items);
  } else if (e.key === 'ArrowUp') {
    e.preventDefault();
    currentFocus = currentFocus <= 0 ? items.length - 1 : currentFocus - 1;
    updateActiveItem(items);
  } else if (e.key === 'Enter') {
    e.preventDefault();
    if (currentFocus >= 0 && items[currentFocus]) {
      selectSuggestion(items[currentFocus]);
    } else {
      // Perform search with current input value
      filterProducts();
    }
  } else if (e.key === 'Escape') {
    hideSuggestions();
  }
}

// Update active item in suggestions
function updateActiveItem(items) {
  items.forEach((item, index) => {
    item.classList.toggle('active', index === currentFocus);
  });
}

// Select a suggestion
function selectSuggestion(suggestionItem) {
  const productId = suggestionItem.dataset.productId;
  const product = allProducts.find(p => p.id == productId);
  
  if (product) {
    searchInput.value = product.name;
    hideSuggestions();
    filterProducts();
  }
}

// Enhanced search input handler
function handleSearchInput() {
  const query = searchInput.value.trim();
  
  // Clear previous timeout
  clearTimeout(searchTimeout);
  
  if (query.length < 2) {
    hideSuggestions();
    filterProducts(); // Still filter existing products
    return;
  }

  // Debounce search to avoid too many API calls
  searchTimeout = setTimeout(() => {
    showSuggestions(query);
  }, 300);
}

// Event listeners for filters
categoryFilter.addEventListener('change', filterProducts);
searchInput.addEventListener('input', handleSearchInput);
searchInput.addEventListener('keydown', handleKeyNavigation);

searchInput.addEventListener('focus', function() {
  const query = this.value.trim();
  if (query.length >= 2) {
    showSuggestions(query);
  }
});

// Handle clicks outside search to hide suggestions
document.addEventListener('click', function(e) {
  if (!searchInput.contains(e.target) && !searchSuggestions.contains(e.target)) {
    hideSuggestions();
  }
});

// Handle suggestion clicks
if (searchSuggestions) {
  searchSuggestions.addEventListener('click', function(e) {
    const suggestionItem = e.target.closest('.suggestion-item');
    if (suggestionItem) {
      selectSuggestion(suggestionItem);
    }
  });
}

// Load products when page loads
document.addEventListener('DOMContentLoaded', function() {
  loadProductsForSearch();
});

// -------------------------------------------------------------------------------------------------
//---------------------------------- API END POINT -------------------------------------------------
// -------------------------------------------------------------------------------------------------

const clubsContainer = document.getElementById('clubsContainer');
const categoriesContainer = document.getElementById('categoriesContainer');
const newArrivalsContainer = document.getElementById('newArrivalsContainer');

// --- SKELETONS ---
clubsContainer.innerHTML = `
  <div class="carousel-item active">
    <div class="row justify-content-center g-4">
      ${Array(4).fill().map(() => `
        <div class="col-6 col-md-3 text-center">
          <div class="skeleton" style="width:100px; height:100px; border-radius:50%; margin:auto;"></div>
          <div class="skeleton" style="height:20px; width:60%; margin:10px auto;"></div>
        </div>
      `).join('')}
    </div>
  </div>
`;

categoriesContainer.innerHTML = `
  ${Array(4).fill().map(() => `
    <div class="col-md-3">
      <div class="card category-card shadow-sm h-100">
        <div class="card-img-top skeleton" style="height:200px;"></div>
        <div class="card-body text-center">
          <div class="skeleton" style="height:20px; width:80%; margin:auto;"></div>
        </div>
      </div>
    </div>
  `).join('')}
`;

newArrivalsContainer.innerHTML = `
  ${Array(4).fill().map(() => `
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-img-top skeleton" style="height:200px;"></div>
        <div class="card-body text-center">
          <div class="skeleton" style="height:20px; width:70%; margin:auto;"></div>
        </div>
      </div>
    </div>
  `).join('')}
`;

productList.innerHTML = `
  ${Array(8).fill().map(() => `
    <div class="col-md-3">
      <div class="card shadow-sm h-100">
        <div class="card-img-top skeleton" style="height:200px;"></div>
        <div class="card-body text-center">
          <div class="skeleton" style="height:20px; width:70%; margin:auto;"></div>
        </div>
      </div>
    </div>
  `).join('')}
`;

// --- FETCH DATA ---
fetch('https://ihr1.bd24.top/shahedsir_api_endpoint/get_homepage_data.php')
  .then(res => res.json())
  .then(data => {
    // --- CLUBS ---
    let chunkSize = 4;
    let clubChunks = [];
    for (let i = 0; i < data.clubs.length; i += chunkSize) {
      clubChunks.push(data.clubs.slice(i, i + chunkSize));
    }

    clubsContainer.innerHTML = clubChunks.map((group, index) => `
      <div class="carousel-item ${index === 0 ? 'active' : ''}">
        <div class="row justify-content-center g-4">
          ${group.map(club => `
            <div class="col-6 col-md-3 text-center">
              <a href="club.php?id=${club.id}" style="text-decoration:none; color:inherit;">
                <img src="${club.logo}" class="brand-logo mb-2" alt="${club.alt_name}">
                <div class="fw-semibold text-uppercase" style="font-size:1.1rem;">${club.name}</div>
              </a>
            </div>
          `).join('')}
        </div>
      </div>
    `).join('');

    // --- CATEGORIES ---
    categoriesContainer.innerHTML = data.categories.map(cat => `
      <div class="col-md-3">
        <a href="${cat.link}" class="text-decoration-none">
          <div class="card category-card shadow-sm h-100">
            <img src="${cat.image}" class="card-img-top" alt="${cat.name}">
            <div class="card-body text-center">
              <h5 class="card-title">${cat.name}</h5>
              <p class="card-text">${cat.description}</p>
            </div>
          </div>
        </a>
      </div>
    `).join('');

    // --- NEW ARRIVALS ---
    newArrivalsContainer.innerHTML = data.new_arrivals.map(product => {
      // Ensure gallery exists
      const gallery = product.gallery && product.gallery.length ? product.gallery : [product.image_url];

      return `
      <div class="col-md-3 ">
        <div class="card shadow-sm h-100 new-arrival-card">
          <img src="${product.image_url}" data-bs-toggle="modal" data-bs-target="#productModal${product.id}" class="card-img-top img-fluid" style="object-fit: contain; height: 200px; cursor:pointer;" alt="${product.name}">
          <div class="card-body text-center">
            <h6 class="fw-semibold">${product.name}</h6>
            <p class="text-muted mb-0">${Number(product.price).toLocaleString('en-BD')} Tk.</p>
            <button class="btn btn-sm btn-outline-danger mt-2" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
          </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="productModal${product.id}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">${product.name}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <div id="carousel${product.id}" class="carousel slide" data-bs-ride="carousel">
                  <div class="carousel-inner">
                    ${gallery.map((img, idx) => `
                      <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                        <img src="${img}" class="d-block w-100" style="object-fit: contain; max-height:400px;" alt="${product.name}">
                      </div>
                    `).join('')}
                  </div>

                  <!-- Controls should be direct children of carousel, not inside carousel-inner -->
                  <button class="carousel-control-prev" type="button" data-bs-target="#carousel${product.id}" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius:50%;"></span>
                    <span class="visually-hidden">Previous</span>
                  </button>
                  <button class="carousel-control-next" type="button" data-bs-target="#carousel${product.id}" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius:50%;"></span>
                    <span class="visually-hidden">Next</span>
                  </button>
                </div>
                <p class="mt-3">${product.description}</p>
                <h5>${Number(product.price).toLocaleString('en-BD')} Tk.</h5>
              </div>
              <div class="modal-footer">
                <button class="btn btn-primary" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>
      </div>
      `;
    }).join('');

    // --- PRODUCT LIST (Filter/Search Section) ---
    productList.innerHTML = data.product_list.map(product => {
      const gallery = product.gallery && product.gallery.length ? product.gallery : [product.image_url];
      return `
        <div class="col-md-3 product-card" data-category="${product.category.toLowerCase()}">
          <div class="card shadow-sm h-100 new-arrival-card">
            <img src="${product.image_url}" data-bs-toggle="modal" data-bs-target="#productModalSearch${product.id}" class="card-img-top img-fluid" style="object-fit: contain; height: 200px; cursor:pointer;" alt="${product.name}">
            <div class="card-body text-center">
              <h6 class="fw-semibold">${product.name}</h6>
              <p class="text-muted mb-0">${Number(product.price).toLocaleString('en-BD')} Tk.</p>
              <button class="btn btn-sm btn-outline-danger mt-2" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
            </div>
          </div>

          <!-- Modal -->
          <div class="modal fade" id="productModalSearch${product.id}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
              <div class="modal-content">
                <div class="modal-header">
                  <h5 class="modal-title">${product.name}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div id="carouselSearch${product.id}" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                      ${gallery.map((img, idx) => `
                        <div class="carousel-item ${idx === 0 ? 'active' : ''}">
                          <img src="${img}" class="d-block w-100" style="object-fit: contain; max-height:400px;" alt="${product.name}">
                        </div>
                      `).join('')}
                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselSearch${product.id}" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius:50%;"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselSearch${product.id}" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.5); border-radius:50%;"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
                  </div>
                  <p class="mt-3">${product.description}</p>
                  <h5>${Number(product.price).toLocaleString('en-BD')} Tk.</h5>
                </div>
                <div class="modal-footer">
                  <button class="btn btn-primary" onclick="addToCart(${product.id}, '${product.name}', ${product.price})">Add to Cart</button>
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      `;
    }).join('');
    filterProducts(); // Apply initial filter

    // Assuming data.categories is an array of category names
    const categoryFilter = document.getElementById('categoryFilter');
    categoryFilter.innerHTML = '<option value="all">All Categories</option>' +
    data.categories_filter.map(cat => `<option value="${cat.category}">${cat.category}</option>`).join('');
  });

// Example addToCart function
function addToCart(id, name, price) {
  console.log('Added to cart:', id, name, price);
  // Here you can integrate your cart logic
}