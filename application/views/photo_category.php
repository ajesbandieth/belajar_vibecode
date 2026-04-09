<?php
$title = "Album Photos";
$active_menu = "photos";
include 'layout/header.php';
include 'layout/sidebar.php';
?>

<main id="main-content" class="pt-16 min-h-screen transition-all duration-300 lg:ml-64 relative">
    <!-- Back Button & Header -->
    <div class="sticky top-16 z-20 bg-slate-50/80 backdrop-blur-md border-b border-slate-200">
        <div class="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto py-4">
            <div class="flex items-center gap-4">
                <a href="/photos" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-indigo-600 hover:border-indigo-200 transition-colors shadow-sm">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-900" id="albumTitle">Loading Album...</h1>
                    <p class="text-sm text-slate-500" id="albumCount">0 photos</p>
                </div>
                <div class="ml-auto inline-flex items-center">
                    <button onclick="openUploadModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm text-sm">
                        <i class="fas fa-upload mr-1"></i> Upload
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Gallery Content -->
    <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <div id="loadingState" class="flex justify-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-indigo-500"></i>
        </div>

        <div id="emptyState" class="hidden flex-col items-center justify-center py-16 px-4 bg-white rounded-2xl border border-dashed border-slate-300">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-image text-3xl text-slate-400"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">Album is empty</h3>
            <p class="text-slate-500">No photos found in this category.</p>
        </div>

        <!-- Grid -->
        <div id="photoGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
            <!-- Photos dynamically injected -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-8 flex justify-center items-center gap-2 hidden">
            <button id="prevBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Prev</button>
            <div id="pageNumbers" class="flex items-center gap-1"></div>
            <button id="nextBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
        </div>
    </div>
</main>

<!-- Lightbox Modal -->
<div id="lightboxModal" class="fixed inset-0 z-[60] hidden">
    <div class="absolute inset-0 bg-slate-950/95 backdrop-blur-md transition-opacity" onclick="closeLightbox()"></div>
    
    <!-- Close Button -->
    <button onclick="closeLightbox()" class="absolute top-6 right-6 text-white/70 hover:text-white transition-colors z-[70] p-2 bg-white/10 rounded-full">
        <i class="fas fa-times text-2xl"></i>
    </button>

    <!-- Navigation Buttons -->
    <button id="lightboxPrev" onclick="prevLightbox()" class="absolute left-6 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-all z-[70] p-4 bg-white/5 hover:bg-white/10 rounded-full group">
        <i class="fas fa-chevron-left text-3xl group-hover:-translate-x-1 transition-transform"></i>
    </button>
    <button id="lightboxNext" onclick="nextLightbox()" class="absolute right-6 top-1/2 -translate-y-1/2 text-white/70 hover:text-white transition-all z-[70] p-4 bg-white/5 hover:bg-white/10 rounded-full group">
        <i class="fas fa-chevron-right text-3xl group-hover:translate-x-1 transition-transform"></i>
    </button>

    <!-- Content Container -->
    <div class="absolute inset-0 flex items-center justify-center p-4 sm:p-12 pointer-events-none">
        <div class="max-w-5xl w-full h-full flex flex-col items-center justify-center gap-4 pointer-events-auto">
            <img id="lightboxImage" src="" alt="" class="max-w-full max-h-[85vh] object-contain shadow-2xl rounded-lg animate-in zoom-in duration-300">
            <div class="text-center text-white">
                <h4 id="lightboxTitle" class="text-lg font-bold"></h4>
                <p id="lightboxInfo" class="text-sm text-white/60"></p>
            </div>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Photo</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="editForm" class="p-5 space-y-4">
                <input type="hidden" id="editFileId">
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-700">File Name</label>
                    <input type="text" id="editFileName" required class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 rounded-lg">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const categoryId = '<?php echo $category_id; ?>';
    let allPhotos = [];
    let currentPage = 1;
    let currentLightboxIndex = -1;
    const itemsPerPage = 20;

    async function initPage() {
        fetchPhotos();
        
        if (categoryId !== 'uncategorized') {
            fetchCategoryDetails();
        } else {
            document.getElementById('albumTitle').innerText = 'Uncategorized';
        }
        
        // Setup Keyboard Navigation
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('lightboxModal');
            if (modal.classList.contains('hidden')) return;

            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextLightbox();
            if (e.key === 'ArrowLeft') prevLightbox();
        });
    }

    async function fetchCategoryDetails() {
        const token = localStorage.getItem('auth_token');
        try {
            const res = await fetch('/api/categories/' + categoryId, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            if (res.ok) {
                const result = await res.json();
                document.getElementById('albumTitle').innerText = result.data.category_name;
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function fetchPhotos(page = 1) {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        const loader = document.getElementById('loadingState');
        const grid = document.getElementById('photoGrid');
        loader.classList.remove('hidden');
        grid.classList.add('opacity-50');

        try {
            const params = new URLSearchParams({ 
                type: 'image',
                page: page,
                limit: itemsPerPage
            });
            if (categoryId !== 'uncategorized') {
                params.append('category_id', categoryId);
            } else {
                params.append('category_id', 'uncategorized');
            }

            const response = await fetch(`/api/files?${params.toString()}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (response.ok) {
                const result = await response.json();
                allPhotos = result.data;
                const pagination = result.pagination;
                
                document.getElementById('albumCount').innerText = `${pagination.total_items} photos`;
                currentPage = pagination.current_page;
                renderGallery(allPhotos, pagination);
            }
        } catch (e) {
            console.error(e);
        } finally {
            loader.classList.add('hidden');
            grid.classList.remove('opacity-50');
        }
    }

    function renderGallery(photos, pagination) {
        const grid = document.getElementById('photoGrid');
        const empty = document.getElementById('emptyState');
        const paginationContainer = document.getElementById('paginationContainer');

        grid.innerHTML = '';

        if (!photos || photos.length === 0) {
            empty.classList.remove('hidden');
            paginationContainer.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        paginationContainer.classList.remove('hidden');

        photos.forEach((photo, index) => {
            grid.innerHTML += `
                <div class="group relative aspect-square bg-slate-100 rounded-xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg transition-all">
                    <img src="/${photo.file_path}" alt="${photo.file_name}" 
                         onclick="openLightbox(${index})"
                         class="w-full h-full object-cover cursor-pointer hover:scale-105 transition-transform duration-500">
                    
                    <div class="absolute inset-0 bg-slate-900/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col justify-end p-3 pointer-events-none">
                        <p class="text-white text-xs font-semibold truncate mb-2">${photo.file_name}</p>
                        <div class="flex gap-2 pointer-events-auto">
                            <button onclick="openEditModal(${photo.id}, '${photo.file_name}')" class="flex-1 bg-white/20 hover:bg-white text-white hover:text-indigo-600 text-xs py-1.5 rounded transition font-medium">Edit</button>
                            <button onclick="deletePhoto(${photo.id})" class="flex-1 bg-white/20 hover:bg-white text-white hover:text-red-500 text-xs py-1.5 rounded transition"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
        });

        renderPagination(pagination.total_pages);
    }

    function renderPagination(totalPages) {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const pageNumbers = document.getElementById('pageNumbers');

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;

        prevBtn.onclick = () => { if(currentPage > 1) fetchPhotos(currentPage - 1); };
        nextBtn.onclick = () => { if(currentPage < totalPages) fetchPhotos(currentPage + 1); };

        pageNumbers.innerHTML = '';
        
        // Simple pagination logic: show max 5 pages
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === currentPage ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 text-slate-600';
            pageNumbers.innerHTML += `<button onclick="fetchPhotos(${i})" class="w-8 h-8 flex items-center justify-center rounded-md font-medium text-sm transition ${activeClass}">${i}</button>`;
        }
    }

    // Lightbox Logic
    function openLightbox(index) {
        currentLightboxIndex = index;
        const photo = allPhotos[index];
        const modal = document.getElementById('lightboxModal');
        const img = document.getElementById('lightboxImage');
        const title = document.getElementById('lightboxTitle');
        const info = document.getElementById('lightboxInfo');

        img.src = '/'+photo.file_path;
        title.innerText = photo.file_name;
        info.innerText = `Uploaded at ${new Date(photo.created_at).toLocaleDateString()}`;

        // Reset animation
        img.classList.remove('animate-in', 'zoom-in');
        void img.offsetWidth; // Trigger reflow
        img.classList.add('animate-in', 'zoom-in');

        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        updateLightboxNav();
    }

    function closeLightbox() {
        document.getElementById('lightboxModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function nextLightbox() {
        if (currentLightboxIndex < allPhotos.length - 1) {
            openLightbox(currentLightboxIndex + 1);
        }
    }

    function prevLightbox() {
        if (currentLightboxIndex > 0) {
            openLightbox(currentLightboxIndex - 1);
        }
    }

    function updateLightboxNav() {
        const prev = document.getElementById('lightboxPrev');
        const next = document.getElementById('lightboxNext');
        
        prev.style.visibility = (currentLightboxIndex > 0) ? 'visible' : 'hidden';
        next.style.visibility = (currentLightboxIndex < allPhotos.length - 1) ? 'visible' : 'hidden';
    }

    // Modal Edit logic
    function openEditModal(id, name) {
        document.getElementById('editFileId').value = id;
        document.getElementById('editFileName').value = name;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('editFileId').value;
        const name = document.getElementById('editFileName').value;
        const token = localStorage.getItem('auth_token');

        try {
            const res = await fetch(`/api/files/${id}`, {
                method: 'PUT',
                headers: { 'Authorization': `Bearer ${token}`, 'Content-Type': 'application/json' },
                body: JSON.stringify({ file_name: name })
            });

            if (res.ok) {
                alert('File diperbarui!');
                closeEditModal();
                fetchPhotos(currentPage);
            } else {
                alert('Gagal memperbarui file.');
            }
        } catch (e) {
            console.error(e);
            alert('Kesalahan koneksi.');
        }
    });

    async function deletePhoto(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus foto ini?')) return;
        const token = localStorage.getItem('auth_token');

        try {
            const res = await fetch(`/api/files/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (res.ok) {
                alert('Foto berhasil dihapus (Soft Delete).');
                fetchPhotos(currentPage);
            } else {
                alert('Gagal menghapus foto.');
            }
        } catch (e) {
            console.error(e);
        }
    }

</script>

<?php include 'layout/footer.php'; ?>
