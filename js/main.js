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

//---------------------------------- API END POINT -------------------------------------------------

const clubsContainer = document.getElementById('clubsContainer');
const categoriesContainer = document.getElementById('categoriesContainer');

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
  });

