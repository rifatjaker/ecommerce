<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add Club - Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <style>
    body { background-color: #f7f7f9; }
    .navbar-brand img { height: 30px; margin-right: 10px; }
  </style>
  <link rel="icon" href="../favicon.ico">
  <meta name="robots" content="noindex,nofollow">
  <meta name="description" content="Admin - Add Club">
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
      <div class="col-lg-8 col-xl-7">
        <div class="card shadow-sm">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-shield-alt me-2"></i>Add New Club</h5>
          </div>
          <div class="card-body">
            <form id="clubForm" novalidate enctype="multipart/form-data">
              <div class="mb-3">
                <label for="name" class="form-label">Club Name</label>
                <input type="text" id="name" class="form-control border-secondary" placeholder="e.g., Manchester United" required>
                <div class="invalid-feedback">Please enter a club name.</div>
              </div>
              <div class="mb-3">
                <label for="logo" class="form-label">Club Logo</label>
                <input type="file" id="logo" name="logo" class="form-control border-secondary" accept="image/jpeg, image/png, image/webp" required>
                <div class="invalid-feedback">Please select a logo file (JPG, PNG, or WebP).</div>
              </div>
              <div class="mb-3">
                <label for="alt_name" class="form-label">Alternative Name</label>
                <input type="text" id="alt_name" class="form-control border-secondary" placeholder="e.g., Man United, Man Utd" required>
                <div class="invalid-feedback">Please enter an alternative name.</div>
              </div>
              <div class="d-flex align-items-center gap-2">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-1"></i>Save Club
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-1"></i>Reset
                </button>
              </div>
            </form>
            <div id="alertBox" class="alert mt-4 d-none" role="alert"></div>
          </div>
        </div>

        <!-- Display existing clubs in a table -->
        <div class="card mt-4">
          <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-list me-2"></i>Existing Clubs</h5>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Alternative Name</th>
                    <th>Logo</th>
                    <th>Actions</th>
                  </tr>
                </thead>
                <tbody id="clubsTableBody">
                  <!-- clubs will be dynamically populated here from the api_endpoint/get_clubs.php -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Edit Club Modal -->
  <div class="modal fade" id="editClubModal" tabindex="-1" aria-labelledby="editClubModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editClubModalLabel">
            <i class="fas fa-edit me-2"></i>Edit Club
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editClubForm" novalidate enctype="multipart/form-data">
            <input type="hidden" id="editClubId">
            <div class="mb-3">
              <label for="editName" class="form-label">Club Name</label>
              <input type="text" id="editName" class="form-control border-secondary" required>
              <div class="invalid-feedback">Please enter a club name.</div>
            </div>
            <div class="mb-3">
              <label for="editLogo" class="form-label">Club Logo</label>
              <input type="file" id="editLogo" name="logo" class="form-control border-secondary" accept="image/jpeg, image/png, image/webp">
              <div class="form-text text-danger">Leave empty to keep current logo</div>
              <div class="invalid-feedback">Please select a valid logo file.</div>
            </div>
            <div class="mb-3">
              <label for="editAltName" class="form-label">Alternative Name</label>
              <input type="text" id="editAltName" class="form-control border-secondary" required>
              <div class="invalid-feedback">Please enter an alternative name.</div>
            </div>
            <div class="mb-3">
              <label class="form-label">Current Logo</label>
              <div id="currentLogoPreview" class="text-center">
                <img id="currentLogoImg" src="" alt="Current Logo" style="max-height: 100px; max-width: 100%;" class="border rounded">
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="fas fa-times me-1"></i>Cancel
          </button>
          <button type="button" class="btn btn-primary" onclick="updateClub()">
            <i class="fas fa-save me-1"></i>Update Club
          </button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    const form = document.getElementById('clubForm');
    const alertBox = document.getElementById('alertBox');
    let editModal;

    function showAlert(type, message) {
      alertBox.className = `alert mt-4 alert-${type}`;
      alertBox.textContent = message;
    }

    async function loadClubs() {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CLUBS));
        const data = await res.json();
        if (!Array.isArray(data)) throw new Error('Unexpected response');
        const tbody = document.getElementById('clubsTableBody');
        if (!tbody) return;
        tbody.innerHTML = data.map((c) => `
          <tr>
            <td>${c.id ?? ''}</td>
            <td>${c.name ?? ''}</td>
            <td>${c.alt_name ?? ''}</td>
            <td>${c.logo ? `<img src="../${c.logo}" alt="${c.name ?? ''}" style="height:40px;">` : ''}</td>
            <td>
              <button type="button" class="btn btn-sm btn-outline-secondary" onclick="editClub(${c.id})">
                <i class="fas fa-edit"></i> Edit
              </button>
              <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteClub(${c.id})">
                <i class="fas fa-trash"></i> Delete
              </button>
            </td>
          </tr>
        `).join('');
      } catch (e) {
        showAlert('danger', e.message || 'Failed to load clubs');
      }
    }

    async function editClub(clubId) {
      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CLUBS));
        const clubs = await res.json();
        const club = clubs.find(c => c.id == clubId);
        
        if (!club) {
          showAlert('danger', 'Club not found');
          return;
        }

        // Populate edit form
        document.getElementById('editClubId').value = club.id;
        document.getElementById('editName').value = club.name;
        document.getElementById('editAltName').value = club.alt_name;
        
        // Show current logo
        const currentLogoImg = document.getElementById('currentLogoImg');
        if (club.logo) {
          currentLogoImg.src = '../' + club.logo;
          currentLogoImg.style.display = 'block';
        } else {
          currentLogoImg.style.display = 'none';
        }

        // Clear file input
        document.getElementById('editLogo').value = '';

        // Show modal
        editModal.show();
      } catch (err) {
        showAlert('danger', 'Failed to load club data: ' + err.message);
      }
    }

    async function updateClub() {
      const clubId = document.getElementById('editClubId').value;
      const name = document.getElementById('editName').value.trim();
      const altName = document.getElementById('editAltName').value.trim();
      const logoFile = document.getElementById('editLogo').files[0];

      // Validation
      if (!name || !altName) {
        showAlert('danger', 'Name and alternative name are required');
        return;
      }

      // File validation if new file is selected
      if (logoFile) {
        if (!CONFIG.isValidImageType(logoFile) && logoFile.type !== 'image/webp') {
          showAlert('danger', 'Please select a valid JPG, PNG, or WebP file.');
          return;
        }
        if (!CONFIG.isValidFileSize(logoFile)) {
          showAlert('danger', 'File size must be less than 5MB.');
          return;
        }
      }

      const formData = new FormData();
      formData.append('id', clubId);
      formData.append('name', name);
      formData.append('alt_name', altName);
      if (logoFile) {
        formData.append('logo', logoFile);
      }

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.UPDATE_CLUB), {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to update club');
        }
        
        showAlert('success', 'Club updated successfully!');
        editModal.hide();
        await loadClubs();
      } catch (err) {
        showAlert('danger', err.message);
      }
    }

    async function deleteClub(clubId) {
      if (!confirm('Are you sure you want to delete this club?')) {
        return;
      }

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.DELETE_CLUB), {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: clubId })
        });
        const data = await res.json();
        
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to delete club');
        }
        
        showAlert('success', 'Club deleted successfully!');
        await loadClubs();
      } catch (err) {
        showAlert('danger', err.message);
      }
    }

    document.addEventListener('DOMContentLoaded', function() {
      // Initialize Bootstrap modal after DOM is loaded
      editModal = new bootstrap.Modal(document.getElementById('editClubModal'));
      loadClubs();
    });

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      showAlert('info', 'Saving...');

      // Simple client-side validation
      const fields = ['name', 'alt_name'];
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
      const logoInput = document.getElementById('logo');
      if (!logoInput.files || logoInput.files.length === 0) {
        logoInput.classList.add('is-invalid');
        valid = false;
      } else {
        logoInput.classList.remove('is-invalid');
        // Validate file type and size using config
        const file = logoInput.files[0];
        if (!CONFIG.isValidImageType(file) && file.type !== 'image/webp') {
          logoInput.classList.add('is-invalid');
          valid = false;
          showAlert('danger', 'Please select a valid JPG, PNG, or WebP file.');
          return;
        }
        if (!CONFIG.isValidFileSize(file)) {
          logoInput.classList.add('is-invalid');
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
      formData.append('alt_name', document.getElementById('alt_name').value.trim());
      formData.append('logo', logoInput.files[0]);

      try {
        const res = await fetch(CONFIG.getApiUrl(CONFIG.API_ENDPOINTS.CREATE_CLUB), {
          method: 'POST',
          body: formData
        });
        const data = await res.json();
        if (!res.ok || data.success !== true) {
          throw new Error(data.message || 'Failed to save');
        }
        showAlert('success', `Saved! New club ID: ${data.id}`);
        form.reset();
        await loadClubs();
      } catch (err) {
        showAlert('danger', err.message);
      }
    });
  </script>
</body>
</html>
