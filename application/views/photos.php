<?php
$title = "My Photos";
$active_menu = "photos";
include 'layout/header.php';
include 'layout/sidebar.php';
?>

<main id="main-content" class="pt-16 min-h-screen transition-all duration-300 lg:ml-64">
    <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Photo Albums</h1>
                <p class="text-slate-500 mt-1">Browse your uploaded photos grouped by category</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openUploadModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Photo
                </button>
            </div>
        </div>

        <!-- Album Grid -->
        <div id="albumGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            <!-- Loading State -->
            <div class="col-span-full flex justify-center py-12" id="loadingState">
                <i class="fas fa-spinner fa-spin text-4xl text-indigo-500"></i>
            </div>
            
            <!-- Empty State -->
            <div class="col-span-full hidden flex-col items-center justify-center py-16 px-4 bg-white rounded-2xl border border-dashed border-slate-300" id="emptyState">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-images text-3xl text-indigo-500"></i>
                </div>
                <h3 class="text-xl font-bold text-slate-900 mb-2">No photos found</h3>
                <p class="text-slate-500 text-center max-w-sm mb-6">You haven't uploaded any photos yet, or there are no categories with photos.</p>
                <button onclick="openUploadModal()" class="text-indigo-600 font-semibold hover:text-indigo-700">Upload your first photo</button>
            </div>
        </div>
    </div>
</main>

<script>
    async function initPage() {
        fetchCategoriesWithPhotos();
    }

    async function fetchCategoriesWithPhotos() {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        const grid = document.getElementById('albumGrid');
        const loader = document.getElementById('loadingState');
        const empty = document.getElementById('emptyState');

        try {
            const response = await fetch('/api/files/summary', {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            const result = await response.json();
            const albums = result.data || [];

            let albumHtml = '';
            albums.forEach(album => {
                albumHtml += createAlbumCard(
                    album.id, 
                    album.category_name, 
                    album.total_files, 
                    album.cover_path
                );
            });

            loader.classList.add('hidden');
            
            if (albumHtml === '') {
                empty.classList.remove('hidden');
            } else {
                empty.classList.add('hidden');
                grid.querySelectorAll('.album-card').forEach(el => el.remove());
                grid.insertAdjacentHTML('beforeend', albumHtml);
            }

        } catch (e) {
            console.error('Failed to load photos', e);
            loader.classList.add('hidden');
            alert('Failed to load albums');
        }
    }

    function createAlbumCard(id, name, count, coverPath) {
        return `
            <a href="/photos/category/${id}" class="album-card group relative bg-white rounded-2xl p-3 border border-slate-200 shadow-sm hover:shadow-md transition-all block">
                <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden relative mb-3">
                    <img src="/${coverPath}" alt="${name}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    <div class="absolute bottom-3 right-3 bg-white/90 backdrop-blur text-slate-800 text-xs font-bold px-2 py-1 rounded-md">
                        ${count} photos
                    </div>
                </div>
                <div class="px-2 pb-1">
                    <h3 class="font-bold text-slate-900 truncate group-hover:text-indigo-600 transition-colors">${name}</h3>
                </div>
            </a>
        `;
    }
</script>

<?php include 'layout/footer.php'; ?>
