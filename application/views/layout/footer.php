    <!-- Global Upload Modal HTML -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="closeUploadModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4">
            <div id="uploadModalContainer" class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0 duration-300 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-indigo-50/30">
                    <h3 class="text-xl font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-indigo-600"></i> Upload New File
                    </h3>
                    <button onclick="closeUploadModal()" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="uploadForm" class="p-6 space-y-4">
                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700">Select File(s) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input type="file" name="original_name[]" id="uploadOriginalName" multiple required class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer border border-dashed border-slate-300 rounded-xl p-4 bg-slate-50" onchange="handleFileSelection(this)">
                        </div>
                    </div>

                    <div class="space-y-1" id="fileNameContainer">
                        <label class="block text-sm font-semibold text-slate-700">File Name (Optional)</label>
                        <input type="text" name="file_name" id="uploadFileName" placeholder="Leave blank to use original file name" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                        <p class="text-xs text-slate-400 mt-1">If uploading multiple files, they will use their original names.</p>
                    </div>

                    <div class="space-y-1">
                        <label class="block text-sm font-semibold text-slate-700">Category</label>
                        <select id="uploadCategory" name="category_id" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all">
                            <option value="">Uncategorized</option>
                        </select>
                    </div>
                    
                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" onclick="closeUploadModal()" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-lg transition-all">Cancel</button>
                        <button type="submit" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100">
                            Upload Now
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Global State management
        const state = {
            viewMode: 'grid', // 'grid' | 'list'
            sidebarOpen: window.innerWidth >= 1024,
            isMobile: window.innerWidth < 1024
        };

        // Sidebar Actions
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            const backdrop = document.getElementById('sidebarBackdrop');
            state.isMobile = window.innerWidth < 1024;

            if (state.isMobile) {
                const isOpen = !sidebar.classList.contains('-translate-x-full');
                
                if (isOpen) {
                    sidebar.classList.add('-translate-x-full');
                    backdrop.classList.add('opacity-0');
                    setTimeout(() => backdrop.classList.add('hidden'), 300);
                    document.body.classList.remove('overflow-hidden');
                } else {
                    backdrop.classList.remove('hidden');
                    setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
                    sidebar.classList.remove('-translate-x-full');
                    document.body.classList.add('overflow-hidden');
                }
            } else {
                const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
                if (isCollapsed) {
                    sidebar.classList.replace('w-64', 'w-20');
                    mainContent.classList.replace('lg:ml-64', 'lg:ml-20');
                } else {
                    sidebar.classList.replace('w-20', 'w-64');
                    mainContent.classList.replace('lg:ml-20', 'lg:ml-64');
                }
            }
        }

        // Event Listeners
        document.getElementById('sidebarToggle').addEventListener('click', toggleSidebar);
        document.getElementById('sidebarBackdrop').addEventListener('click', toggleSidebar);

        window.addEventListener('resize', () => {
            const wasMobile = state.isMobile;
            state.isMobile = window.innerWidth < 1024;
            
            if (wasMobile !== state.isMobile) {
                const sidebar = document.getElementById('sidebar');
                const main = document.getElementById('main-content');
                const backdrop = document.getElementById('sidebarBackdrop');
                
                sidebar.classList.remove('sidebar-collapsed', '-translate-x-full');
                sidebar.classList.remove('w-20');
                sidebar.classList.add('w-64');
                main.classList.remove('lg:ml-20');
                main.classList.add('lg:ml-64');
                backdrop.classList.add('hidden', 'opacity-0');
                document.body.classList.remove('overflow-hidden');
            }
        });

        // Authentication logic
        async function checkAuth() {
            const token = localStorage.getItem('auth_token');
            if (!token) { window.location.href = '/login'; return; }

            try {
                const response = await fetch('/api/users/current', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });

                if (response.ok) {
                    const result = await response.json();
                    document.getElementById('userName').innerText = result.data.name;
                    document.getElementById('userEmail').innerText = result.data.email;
                    document.getElementById('app').classList.remove('hidden');
                    
                    // Trigger page specific init if exists
                    if (typeof initPage === 'function') initPage(result.data);
                } else {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                }
            } catch (error) {
                window.location.href = '/login';
            }
        }

        document.getElementById('logoutBtn').addEventListener('click', () => {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        });

        // Run auth check on every page using this layout
        checkAuth();

        // Global Upload Modal Logic
        async function openUploadModal() {
            const modal = document.getElementById('uploadModal');
            const container = document.getElementById('uploadModalContainer');
            
            // Populate categories
            const catSelect = document.getElementById('uploadCategory');
            catSelect.innerHTML = '<option value="">Uncategorized</option>';
            
            try {
                const token = localStorage.getItem('auth_token');
                const response = await fetch('/api/categories', {
                    headers: { 'Authorization': 'Bearer ' + token }
                });
                if (response.ok) {
                    const result = await response.json();
                    result.data.forEach(cat => {
                        const opt = document.createElement('option');
                        opt.value = cat.id;
                        opt.textContent = cat.category_name;
                        catSelect.appendChild(opt);
                    });
                }
            } catch (e) { console.error('Error loading categories', e); }

            modal.classList.remove('hidden');
            setTimeout(() => {
                container.classList.remove('scale-95', 'opacity-0');
                container.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeUploadModal() {
            const modal = document.getElementById('uploadModal');
            const container = document.getElementById('uploadModalContainer');
            container.classList.remove('scale-100', 'opacity-100');
            container.classList.add('scale-95', 'opacity-0');
            setTimeout(() => modal.classList.add('hidden'), 300);
        }

        // Initialize Upload Form Listener
        const uploadForm = document.getElementById('uploadForm');

        function handleFileSelection(input) {
            const fileNameContainer = document.getElementById('fileNameContainer');
            const fileNameInput = document.getElementById('uploadFileName');
            if (input.files && input.files.length > 1) {
                fileNameContainer.classList.add('opacity-50');
                fileNameInput.disabled = true;
                fileNameInput.value = '';
            } else {
                fileNameContainer.classList.remove('opacity-50');
                fileNameInput.disabled = false;
            }
        }

        if (uploadForm) {
            uploadForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const form = e.target;
                const btn = form.querySelector('button[type="submit"]');
                const btnText = btn.innerHTML;
                
                const fileInput = document.getElementById('uploadOriginalName');
                const nameInput = document.getElementById('uploadFileName');
                
                // Fallback for file_name inside JS before fetching, 
                // only if single file is selected.
                if (fileInput.files.length === 1 && !nameInput.value.trim()) {
                    nameInput.value = fileInput.files[0].name;
                }

                btn.disabled = true;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Uploading...';

                const formData = new FormData(form);
                const token = localStorage.getItem('auth_token');

                try {
                    const response = await fetch('/api/files/upload', {
                        method: 'POST',
                        headers: { 'Authorization': 'Bearer ' + token },
                        body: formData
                    });

                    if (response.ok) {
                        alert('✅ Upload sukses!');
                        closeUploadModal();
                        form.reset();
                        
                        // Handle data refresh if on a specific gallery page
                        if (typeof fetchCategoriesWithPhotos === 'function') fetchCategoriesWithPhotos();
                        else if (typeof fetchVideos === 'function') fetchVideos();
                        else if (typeof initPage === 'function') initPage(); 
                    } else {
                        const res = await response.json();
                        alert('❌ Error: ' + (res.data || 'Upload gagal'));
                    }
                } catch (err) {
                    console.error('Fetch error during upload:', err);
                    alert('❌ Terjadi kesalahan koneksi saat upload');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = btnText;
                }
            });
        }
    </script>
</body>
</html>
