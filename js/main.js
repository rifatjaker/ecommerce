// Product filter and search functionality
const categoryFilter = document.getElementById('categoryFilter');
const searchInput = document.getElementById('searchInput');
const productList = document.getElementById('productList');

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

// Event listeners for filters
categoryFilter.addEventListener('change', filterProducts);
searchInput.addEventListener('input', filterProducts);

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