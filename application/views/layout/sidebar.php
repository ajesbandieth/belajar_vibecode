<!-- Sidebar -->
<aside id="sidebar" class="fixed top-16 left-0 bottom-0 w-64 bg-white border-r border-slate-200 py-6 overflow-y-auto z-30 -translate-x-full lg:translate-x-0">
    <nav class="space-y-1">
        <a href="/dashboard" class="sidebar-item <?php echo (isset($active_menu) && $active_menu == 'dashboard') ? 'active' : ''; ?> flex items-center gap-3 px-6 py-3 font-medium transition-all">
            <i class="fas fa-home w-5"></i> <span class="sidebar-text">Dashboard</span>
        </a>
        <a href="/categories" class="sidebar-item <?php echo (isset($active_menu) && $active_menu == 'categories') ? 'active' : ''; ?> flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
            <i class="fas fa-folder w-5"></i> <span class="sidebar-text">Manage Categories</span>
        </a>
        <a href="/photos" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium <?php echo (isset($active_menu) && $active_menu == 'photos') ? 'active' : 'text-slate-600'; ?> transition-all">
            <i class="fas fa-image w-5"></i> <span class="sidebar-text">My Photos</span>
        </a>
        <a href="/videos" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium <?php echo (isset($active_menu) && $active_menu == 'videos') ? 'active' : 'text-slate-600'; ?> transition-all">
            <i class="fas fa-video w-5"></i> <span class="sidebar-text">My Videos</span>
        </a>
        <a href="#" onclick="openUploadModal(); return false;" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all border-t border-slate-50 mt-4 pt-4">
            <i class="fas fa-cloud-upload-alt w-5 text-indigo-500"></i> <span class="sidebar-text">Upload File</span>
        </a>
        <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all border-t border-slate-50 mt-4 pt-4">
            <i class="fas fa-heart w-5"></i> <span class="sidebar-text">Favorites</span>
        </a>
        <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
            <i class="fas fa-trash w-5"></i> <span class="sidebar-text">Bin</span>
        </a>
    </nav>
</aside>
