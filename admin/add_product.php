<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Product - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f7f7f9; }
    .navbar-brand img { height: 30px; margin-right: 10px; }
    .image-preview { max-width: 100px; max-height: 100px; object-fit: cover; }
    .image-upload-area { border: 2px dashed #ccc; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; }
    .image-upload-area:hover { border-color: #007bff; background-color: #f8f9fa; }
    .image-item { position: relative; display: inline-block; margin: 5px; }
    .remove-image { position: absolute; top: -5px; right: -5px; background: red; color: white; border-radius: 50%; width: 20px; height: 20px; text-align: center; line-height: 18px; cursor: pointer; font-size: 12px; }
  </style>
  <link rel="icon" href="../favicon.ico">
  <meta name="robots" content="noindex,nofollow">
  <meta name="description" content="Admin - Add Product">
  <meta name="author" content="Eyesome Sports">
  <script src="../js/config.js"></script>
</head>
<body>
  <?php 
require_once 'auth.php';
requireAdminAuth();
include 'includes/navbar.php'; 
?>

  <main class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-9">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-box me-2"></i>Add New Product</h5>
          </div>
          <div class="card-body">
            <form id="productForm" novalidate enctype="multipart/form-data">
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="name" class="form-label">Product Name</label>
                    <input type="text" id="name" class="form-control border-secondary" placeholder="e.g., Cricket Bat SS" required>
                    <div class="invalid-feedback">Please enter a product name.</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="category_id" class="form-label">Category</label>
                    <select id="category_id" class="form-select border-secondary" required>
                      <option value="">Select Category</option>
                    </select>
                    <div class="invalid-feedback">Please select a category.</div>
                  </div>
                </div>
              </div>
              
              <div class="row">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="price" class="form-label">Price (৳)</label>
                    <input type="number" id="price" class="form-control border-secondary" placeholder="0.00" step="0.01" min="0" required>
                    <div class="invalid-feedback">Please enter a valid price.</div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="status" class="form-label">Status</label>
                    <select id="status" class="form-select border-secondary" required>
                      <option value="active">Active</option>
                      <option value="inactive">Inactive</option>
                    </select>
                    <div class="invalid-feedback">Please select a status.</div>
                  </div>
                </div>
              </div>

              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="form-control border-secondary" rows="4" placeholder="Product description..." required></textarea>
                <div class="invalid-feedback">Please enter a description.</div>
              </div>

              <div class="mb-3">
                <label class="form-label">Product Images</label>
                <div class="image-upload-area" onclick="document.getElementById('productImages').click()">
                  <i class="fas fa-cloud-upload-alt fa-2x text-muted mb-2"></i>
                  <p class="mb-0">Click to upload images or drag and drop</p>
                  <small class="text-muted">JPG, PNG, WebP (Max 5MB each)</small>
                </div>
                <input type="file" id="productImages" name="images[]" class="d-none" accept="image/jpeg, image/png, image/webp" multiple>
                <div id="imagePreviewContainer" class="mt-3"></div>
                <div class="form-text">First image will be the primary image</div>
              </div>

              <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-1"></i>Save Product
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-1"></i>Reset
                </button>
              </div>
            </form>
            <div id="alertBox" class="alert mt-4 d-none" role="alert"></div>
          </div>
        </div>

        <!-- Display existing products in a table -->
        <div class="card mt-4">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Existing Products</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="productsTableBody">
                  <!-- products will be dynamically populated here -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Edit Product Modal -->
  <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editProductModalLabel">
            <i class="fas fa-edit me-2"></i>Edit Product
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editProductForm" novalidate enctype="multipart/form-data">
            <input type="hidden" id="editProductId">
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editName" class="form-label">Product Name</label>
                  <input type="text" id="editName" class="form-control border-secondary" required>
                  <div class="invalid-feedback">Please enter a product name.</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editCategoryId" class="form-label">Category</label>
                  <select id="editCategoryId" class="form-select border-secondary" required>
                    <option value="">Select Category</option>
                  </select>
                  <div class="invalid-feedback">Please select a category.</div>
                </div>
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editPrice" class="form-label">Price (৳)</label>
                  <input type="number" id="editPrice" class="form-control border-secondary" step="0.01" min="0" required>
                  <div class="invalid-feedback">Please enter a valid price.</div>
                </div>
              </div>
              <div class="col-md-6">
                <div class="mb-3">
                  <label for="editStatus" class="form-label">Status</label>
                  <select id="editStatus" class="form-select border-secondary" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                  </select>
                  <div class="invalid-feedback">Please select a status.</div>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label for="editDescription" class="form-label">Description</label>
              <textarea id="editDescription" class="form-control border-secondary" rows="4" required></textarea>
              <div class="invalid-feedback">Please enter a description.</div>
            </div>

            <div class="mb-3">
              <label class="form-label">Current Images</label>
              <div id="currentImagesContainer" class="d-flex flex-wrap gap-2"></div>
            </div>

            <div class="mb-3">
              <label class="form-label">Add New Images</label>
              <div class="image-upload-area" onclick="document.getElementById('editProductImages').click()">
                <i class="fas fa-plus fa-2x text-muted mb-2"></i>
                <p class="mb-0">Click to add more images</p>
              </div>
              <input type="file" id="editProductImages" name="images[]" class="d-none" accept="image/jpeg, image/png, image/webp" multiple>
              <div id="editImagePreviewContainer" class="mt-3"></div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="updateProduct()">
            <i class="fas fa-save me-1"></i>Update Product
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const form = document.getElementById('productForm');
    const alertBox = document.getElementById('alertBox');
    let editModal;
    let selectedImages = [];
    let editSelectedImages = [];

    function showAlert(type, message) {
      alertBox.className = `alert mt-4 alert-${type}`;
      alertBox.textContent = message;
    }

    async function loadCategories() {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CATEGORIES));
        const categories = await res.json();
        const categorySelect = document.getElementById('category_id');
        const editCategorySelect = document.getElementById('editCategoryId');
        
        categories.forEach(category => {
          categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
          editCategorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
        });
      } catch (e) {
        showAlert('danger', 'Failed to load categories: ' + e.message);
      }
    }

    async function loadProducts() {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.PRODUCTS));
        const data = await res.json();
        if (!Array.isArray(data)) throw new Error('Unexpected response');
        const tbody = document.getElementById('productsTableBody');
        if (!tbody) return;
        
        tbody.innerHTML = data.map((p) => `
          <tr>
            <td>${p.id ?? ''}</td>
            <td>${p.primary_image ? `<img src="../${p.primary_image}" alt="${p.name ?? ''}" class="image-preview">` : '<span class="text-muted">No image</span>'}</td>
            <td>${p.name ?? ''}</td>
            <td>${p.category_name ?? ''}</td>
            <td>৳${p.price ?? '0.00'}</td>
            <td><span class="badge bg-${p.status === 'active' ? 'success' : 'secondary'}">${p.status ?? 'inactive'}</span></td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editProduct(${p.id})">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${p.id})">
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        `).join('');
      } catch (e) {
        showAlert('danger', e.message || 'Failed to load products');
      }
    }

    // Image handling functions
    function handleImageUpload(files, containerId, imagesArray) {
      const container = document.getElementById(containerId);
      container.innerHTML = '';
      
      Array.from(files).forEach((file, index) => {
        if (CONFIG.isValidImageType(file) || file.type === 'image/webp') {
          if (CONFIG.isValidFileSize(file)) {
            const reader = new FileReader();
            reader.onload = function(e) {
              const imageItem = document.createElement('div');
              imageItem.className = 'image-item';
              imageItem.innerHTML = `
                <img src="${e.target.result}" class="image-preview" alt="Preview">
                <span class="remove-image" onclick="removeImage(${index}, '${containerId}', imagesArray)">&times;</span>
              `;
              container.appendChild(imageItem);
            };
            reader.readAsDataURL(file);
            imagesArray.push(file);
          } else {
            showAlert('danger', `File ${file.name} is too large. Max size is 5MB.`);
          }
        } else {
          showAlert('danger', `File ${file.name} is not a valid image type.`);
        }
      });
    }

    function removeImage(index, containerId, imagesArray) {
      imagesArray.splice(index, 1);
      handleImageUpload([], containerId, imagesArray);
    }

    // Event listeners for image uploads
    document.getElementById('productImages').addEventListener('change', function(e) {
      handleImageUpload(e.target.files, 'imagePreviewContainer', selectedImages);
    });

    document.getElementById('editProductImages').addEventListener('change', function(e) {
      handleImageUpload(e.target.files, 'editImagePreviewContainer', editSelectedImages);
    });

    async function editProduct(productId) {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.PRODUCTS));
        const products = await res.json();
        const product = products.find(p => p.id == productId);
        
        if (!product) {
          showAlert('danger', 'Product not found');
          return;
        }

        // Populate edit form
        document.getElementById('editProductId').value = product.id;
        document.getElementById('editName').value = product.name;
        document.getElementById('editCategoryId').value = product.category_id;
        document.getElementById('editPrice').value = product.price;
        document.getElementById('editStatus').value = product.status;
        document.getElementById('editDescription').value = product.description;
        
        // Show current images
        const currentImagesContainer = document.getElementById('currentImagesContainer');
        currentImagesContainer.innerHTML = '';
        if (product.images && product.images.length > 0) {
          product.images.forEach((image, index) => {
            const imageItem = document.createElement('div');
            imageItem.className = 'image-item';
            imageItem.innerHTML = `
              <img src="../${image.image_url}" class="image-preview" alt="Product Image">
              <span class="remove-image" onclick="removeCurrentImage(${image.id})">&times;</span>
            `;
            currentImagesContainer.appendChild(imageItem);
          });
        }

        // Clear new image previews
        document.getElementById('editImagePreviewContainer').innerHTML = '';
        editSelectedImages = [];

        // Show modal
        editModal.show();
      } catch (err) {
        showAlert('danger', 'Failed to load product data: ' + err.message);
      }
    }

    async function updateProduct() {
      const productId = document.getElementById('editProductId').value;
      const name = document.getElementById('editName').value.trim();
      const categoryId = document.getElementById('editCategoryId').value;
      const price = document.getElementById('editPrice').value;
      const status = document.getElementById('editStatus').value;
      const description = document.getElementById('editDescription').value.trim();

      // Validation
      if (!name || !categoryId || !price || !description) {
        showAlert('danger', 'All fields are required');
        return;
      }

      const formData = new FormData();
      formData.append('id', productId);
      formData.append('name', name);
      formData.append('category_id', categoryId);
      formData.append('price', price);
      formData.append('status', status);
      formData.append('description', description);
      
      editSelectedImages.forEach((file, index) => {
        formData.append('images[]', file);
      });

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.UPDATE_PRODUCT), {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to update product');
        }
        
        showAlert('success', 'Product updated successfully!');
        editModal.hide();
        await loadProducts();
      } catch (err) {
        showAlert('danger', err.message);
      }
    }

    async function deleteProduct(productId) {
      if (!confirm('Are you sure you want to delete this product?')) {
        return;
      }

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.DELETE_PRODUCT), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: productId })
        });
        const data = await res.json();
        
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to delete product');
        }
        
        showAlert('success', 'Product deleted successfully!');
        await loadProducts();
      } catch (err) {
        showAlert('danger', err.message);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Bootstrap modal after DOM is loaded
      editModal = new bootstrap.Modal(document.getElementById('editProductModal'));
      loadCategories();
      loadProducts();
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      showAlert('info', 'Saving...');

      // Validation
      const name = document.getElementById('name').value.trim();
      const categoryId = document.getElementById('category_id').value;
      const price = document.getElementById('price').value;
      const status = document.getElementById('status').value;
      const description = document.getElementById('description').value.trim();

      if (!name || !categoryId || !price || !description) {
        showAlert('danger', 'All fields are required');
        return;
      }

      if (selectedImages.length === 0) {
        showAlert('danger', 'Please select at least one product image');
        return;
      }

      const formData = new FormData();
      formData.append('name', name);
      formData.append('category_id', categoryId);
      formData.append('price', price);
      formData.append('status', status);
      formData.append('description', description);
      
      selectedImages.forEach((file, index) => {
        formData.append('images[]', file);
      });

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CREATE_PRODUCT), {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to save');
        }
        showAlert('success', `Saved! New product ID: ${data.id}`);
        form.reset();
        selectedImages = [];
        document.getElementById('imagePreviewContainer').innerHTML = '';
        await loadProducts();
      } catch (err) {
        showAlert('danger', err.message);
      }
    });
  </script>
</body>
</html>
