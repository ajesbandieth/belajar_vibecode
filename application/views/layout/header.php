<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - File Manager' : 'File Manager'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; overflow-x: hidden; }
        .sidebar-item:hover { background-color: rgba(79, 70, 229, 0.1); color: #4f46e5; }
        .sidebar-item.active { background-color: rgba(79, 70, 229, 0.1); color: #4f46e5; border-right: 3px solid #4f46e5; }
        .sidebar-collapsed .sidebar-text { display: none; }
        .sidebar-collapsed .sidebar-item { justify-content: center; padding-left: 0; padding-right: 0; }
        .sidebar-collapsed .sidebar-header { justify-content: center; }
        .sidebar-collapsed .sidebar-item i { margin: 0; width: auto; font-size: 1.25rem; }
        
        #main-content, #sidebar { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
        
        .view-list .video-grid { grid-template-cols: 1fr; }
        .view-list .video-card { display: flex; flex-direction: row; gap: 1.5rem; align-items: flex-start; }
        .view-list .video-card .thumbnail-container { width: 280px; flex-shrink: 0; margin-bottom: 0; }
        .view-list .video-card .video-info { flex: 1; }
        @media (max-width: 640px) {
            .view-list .video-card { flex-direction: column; gap: 0.75rem; }
            .view-list .video-card .thumbnail-container { width: 100%; }
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 hidden" id="app">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-4 sm:px-6 z-40">
        <div class="flex items-center gap-2 sm:gap-4">
            <button id="sidebarToggle" class="p-2 hover:bg-slate-100 rounded-lg text-slate-600 transition-colors">
                <i class="fas fa-bars text-lg"></i>
            </button>
            <div class="flex items-center gap-2">
                <div class="bg-indigo-600 p-2 rounded-lg text-white">
                    <i class="fas fa-box-open text-xl"></i>
                </div>
                <span class="text-xl font-bold tracking-tight hidden sm:inline">Vibe<span class="text-indigo-600">Code</span></span>
            </div>
        </div>

        <div class="flex-1 max-w-2xl mx-4 sm:mx-12 hidden md:block">
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                <input type="text" placeholder="Search for files, videos..." 
                    class="w-full bg-slate-100 border-none rounded-full py-2.5 pl-11 pr-4 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
            </div>
        </div>

        <div class="flex items-center gap-2 sm:gap-4">
            <button class="bg-indigo-600 text-white p-2 sm:px-4 sm:py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline">Upload</span>
            </button>
            <div class="flex items-center gap-3 pl-2 sm:pl-4 border-l border-slate-200">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none" id="userName">User Name</p>
                    <p class="text-xs text-slate-500 truncate max-w-[120px]" id="userEmail">user@example.com</p>
                </div>
                <button id="logoutBtn" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Sidebar Backdrop (Mobile only) -->
    <div id="sidebarBackdrop" class="fixed inset-0 bg-slate-900/50 z-30 hidden lg:hidden transition-opacity duration-300 opacity-0"></div>
