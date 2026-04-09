<?php
$title = "My Videos";
$active_menu = "videos";
include 'layout/header.php';
include 'layout/sidebar.php';
?>

<main id="main-content" class="pt-16 min-h-screen transition-all duration-300 lg:ml-64 relative">
    <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">My Videos</h1>
                <p class="text-slate-500 mt-1" id="videoCount">Loading videos...</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="openUploadModal()" class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-cloud-upload-alt"></i> Upload Video
                </button>
            </div>
        </div>

        <!-- UI States -->
        <div id="loadingState" class="flex justify-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-indigo-500"></i>
        </div>

        <div id="emptyState" class="hidden flex-col items-center justify-center py-16 px-4 bg-white rounded-2xl border border-dashed border-slate-300">
            <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-video-slash text-3xl text-indigo-500"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2">No videos found</h3>
            <p class="text-slate-500">You haven't uploaded any videos yet.</p>
        </div>

        <!-- Video Grid -->
        <div id="videoGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Videos dynamically injected -->
        </div>

        <!-- Pagination -->
        <div id="paginationContainer" class="mt-8 flex justify-center items-center gap-2 hidden">
            <button id="prevBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Prev</button>
            <div id="pageNumbers" class="flex items-center gap-1"></div>
            <button id="nextBtn" class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
        </div>
    </div>
</main>

<!-- Edit Video Modal -->
<div id="editVideoModal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeEditModal()"></div>
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <h3 class="font-bold text-slate-900"><i class="fas fa-edit text-indigo-600 mr-2"></i>Edit Video</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600"><i class="fas fa-times"></i></button>
            </div>
            <form id="editForm" class="p-5 space-y-4">
                <input type="hidden" id="editFileId">
                <div class="space-y-1">
                    <label class="block text-sm font-semibold text-slate-700">Video Title</label>
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
    let allVideos = [];
    let currentPage = 1;
    const itemsPerPage = 10;

    async function initPage() {
        fetchVideos();
    }

    async function fetchVideos(page = 1) {
        const token = localStorage.getItem('auth_token');
        if (!token) return;

        const loader = document.getElementById('loadingState');
        const grid = document.getElementById('videoGrid');
        loader.classList.remove('hidden');
        grid.classList.add('opacity-50');

        try {
            const params = new URLSearchParams({ 
                type: 'video',
                page: page,
                limit: itemsPerPage
            });

            const response = await fetch(`/api/files?${params.toString()}`, {
                headers: { 'Authorization': `Bearer ${token}` }
            });
            
            if (response.ok) {
                const result = await response.json();
                allVideos = result.data || [];
                const pagination = result.pagination;
                
                document.getElementById('videoCount').innerText = `${pagination.total_items} videos`;
                currentPage = pagination.current_page;
                renderGallery(allVideos, pagination);
            }
        } catch (e) {
            console.error(e);
        } finally {
            loader.classList.add('hidden');
            grid.classList.remove('opacity-50');
        }
    }

    function renderGallery(videos, pagination) {
        const grid = document.getElementById('videoGrid');
        const empty = document.getElementById('emptyState');
        const paginationContainer = document.getElementById('paginationContainer');

        grid.innerHTML = '';

        if (!videos || videos.length === 0) {
            empty.classList.remove('hidden');
            paginationContainer.classList.add('hidden');
            return;
        }

        empty.classList.add('hidden');
        paginationContainer.classList.remove('hidden');

        videos.forEach(video => {
            const fileSize = (video.file_size / (1024 * 1024)).toFixed(2) + ' MB';
            grid.innerHTML += `
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm hover:shadow-md transition-all overflow-hidden flex flex-col">
                    <div class="relative bg-slate-900 group aspect-video">
                        <video src="/${video.file_path}" controls class="w-full h-full object-cover">
                            Your browser does not support HTML video.
                        </video>
                    </div>
                    <div class="p-4 flex-1 flex flex-col justify-between">
                        <div>
                            <h3 class="font-bold text-slate-800 line-clamp-2" title="${video.file_name}">${video.file_name}</h3>
                            <p class="text-xs text-slate-500 mt-1">${fileSize}</p>
                        </div>
                        <div class="flex gap-2 mt-4 pt-4 border-t border-slate-100">
                            <button onclick="openEditModal(${video.id}, '${video.file_name}')" class="flex-1 text-indigo-600 bg-indigo-50 hover:bg-indigo-100 py-1.5 rounded-lg text-sm font-semibold transition">Edit Title</button>
                            <button onclick="deleteVideo(${video.id})" class="text-red-500 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition"><i class="fas fa-trash"></i></button>
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

        prevBtn.onclick = () => { if(currentPage > 1) fetchVideos(currentPage - 1); };
        nextBtn.onclick = () => { if(currentPage < totalPages) fetchVideos(currentPage + 1); };

        pageNumbers.innerHTML = '';
        
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);
        if (endPage - startPage < 4) startPage = Math.max(1, endPage - 4);

        for (let i = startPage; i <= endPage; i++) {
            const activeClass = i === currentPage ? 'bg-indigo-600 text-white' : 'hover:bg-slate-100 text-slate-600';
            pageNumbers.innerHTML += `<button onclick="fetchVideos(${i})" class="w-8 h-8 flex items-center justify-center rounded-md font-medium text-sm transition ${activeClass}">${i}</button>`;
        }
    }

    function openEditModal(id, name) {
        document.getElementById('editFileId').value = id;
        document.getElementById('editFileName').value = name;
        document.getElementById('editVideoModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editVideoModal').classList.add('hidden');
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
                alert('Judul video diperbarui!');
                closeEditModal();
                fetchVideos();
            } else {
                alert('Gagal memperbarui video.');
            }
        } catch (e) {
            console.error(e);
            alert('Kesalahan koneksi.');
        }
    });

    async function deleteVideo(id) {
        if (!confirm('Apakah Anda yakin ingin menghapus video ini beserta metadatanya?')) return;
        const token = localStorage.getItem('auth_token');

        try {
            const res = await fetch(`/api/files/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': `Bearer ${token}` }
            });

            if (res.ok) {
                alert('Video berhasil dihapus (Soft Delete).');
                fetchVideos();
            } else {
                alert('Gagal menghapus video.');
            }
        } catch (e) {
            console.error(e);
        }
    }
</script>

<?php include 'layout/footer.php'; ?>
