<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Category - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f7f7f9; }
    .navbar-brand img { height: 30px; margin-right: 10px; }
  </style>
  <link rel="icon" href="../favicon.ico">
  <meta name="robots" content="noindex,nofollow">
  <meta name="description" content="Admin - Add Category">
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
      <div class="col-lg-12 col-xl-12">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-tags me-2"></i>Add New Category</h5>
          </div>
          <div class="card-body">
            <form id="categoryForm" novalidate enctype="multipart/form-data">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" id="name" class="form-control border-secondary" placeholder="e.g., Jerseys" required>
                <div class="invalid-feedback">Please enter a name.</div>
              </div>
              <div class="mb-3">
                <label for="image" class="form-label">Image</label>
                <input type="file" id="image" name="image" class="form-control border-secondary" accept="image/jpeg, image/png" required>
                <div class="invalid-feedback">Please select a JPG or PNG file.</div>
              </div>
              <div class="mb-3">
                <label for="link" class="form-label">Link URL</label>
                <select id="link" class="form-select border-secondary" required>
                  <option value="">Select a link</option>
                  <option value="jerseys.php">jerseys.php</option>
                  <option value="football.php">football.php</option>
                  <option value="cricket-bat.php">cricket-bat.php</option>
                  <option value="sports.php">sports.php</option>
                </select>
                <div class="invalid-feedback">Please enter a valid link URL.</div>
              </div>
              <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea id="description" class="form-control border-secondary" rows="3" placeholder="Short description" required></textarea>
                <div class="invalid-feedback">Please enter a description.</div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-1"></i>Save Category
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-1"></i>Reset
                </button>
              </div>
            </form>
            <div id="alertBox" class="alert mt-4 d-none" role="alert"></div>
          </div>
        </div>
        <!--Display existing categories in a table here -->
        <div class="card mt-4">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Existing Categories</h5>
          </div>
          <div class="card-body">
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Link URL</th>
                  <th style="width: 30%;">Description</th>
                  <th>Image</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="categoriesTableBody">
                <!-- categories will be dynamically populated here from the api_endpoint/get_categories.php -->
                
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    const form = document.getElementById('categoryForm');
    const alertBox = document.getElementById('alertBox');

    function showAlert(type, message) {
      alertBox.className = `alert mt-4 alert-${type}`;
      alertBox.textContent = message;
    }

    async function loadCategories() {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CATEGORIES));
        const data = await res.json();
        if (!Array.isArray(data)) throw new Error('Unexpected response');
        const tbody = document.getElementById('categoriesTableBody');
        if (!tbody) return;
        tbody.innerHTML = data.map((c) => `
          <tr>
            <td>${c.id ?? ''}</td>
            <td>${c.name ?? ''}</td>
            <td>${c.link ?? ''}</td>
            <td>${c.description ?? ''}</td>
            <td>${c.image ? `<img src="../${c.image}" alt="${c.name ?? ''}" style="height:40px;">` : ''}</td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-secondary" disabled>
                <i class="fas fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger" disabled>
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        `).join('');
      } catch (e) {
        showAlert('danger', e.message || 'Failed to load categories');
      }
    }

    document.addEventListener('DOMContentLoaded', loadCategories);

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      showAlert('info', 'Saving...');

      // Simple client-side validation
      const fields = ['name', 'link', 'description'];
      let valid = true;
      fields.forEach((id) => {
        const el = document.getElementById(id);
        if (!el.value.trim()) {
          el.classList.add('is-invalid');
          valid = false;
        } else {
          el.classList.remove('is-invalid');
        }
      });
      const imageInput = document.getElementById('image');
      if (!imageInput.files || imageInput.files.length === 0) {
        imageInput.classList.add('is-invalid');
        valid = false;
      } else {
        imageInput.classList.remove('is-invalid');
        // Validate file type and size using config
        const file = imageInput.files[0];
        if (!CONFIG.isValidImageType(file)) {
          imageInput.classList.add('is-invalid');
          valid = false;
          showAlert('danger', 'Please select a valid JPG or PNG file.');
          return;
        }
        if (!CONFIG.isValidFileSize(file)) {
          imageInput.classList.add('is-invalid');
          valid = false;
          showAlert('danger', 'File size must be less than 5MB.');
          return;
        }
      }
      if (!valid) {
        showAlert('danger', 'Please fill all required fields correctly.');
        return;
      }

      const formData = new FormData();
      formData.append('name', document.getElementById('name').value.trim());
      formData.append('link', document.getElementById('link').value.trim());
      formData.append('description', document.getElementById('description').value.trim());
      formData.append('image', imageInput.files[0]);

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CREATE_CATEGORY), {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to save');
        }
        showAlert('success', `Saved! New category ID: ${data.id}`);
        form.reset();
        await loadCategories();
      } catch (err) {
        showAlert('danger', err.message);
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


