<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar-item:hover { background-color: rgba(79, 70, 229, 0.1); color: #4f46e5; }
        .sidebar-item.active { background-color: rgba(79, 70, 229, 0.1); color: #4f46e5; border-right: 3px solid #4f46e5; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen text-slate-900 hidden" id="app">
    <!-- Navbar -->
    <header class="fixed top-0 left-0 right-0 h-16 bg-white border-b border-slate-200 flex items-center justify-between px-6 z-30">
        <div class="flex items-center gap-2">
            <div class="bg-indigo-600 p-2 rounded-lg text-white">
                <i class="fas fa-box-open text-xl"></i>
            </div>
            <span class="text-xl font-bold tracking-tight">Vibe<span class="text-indigo-600">Code</span></span>
        </div>

        <div class="flex-1 max-w-2xl mx-12 hidden md:block">
            <div class="relative group">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-indigo-600 transition-colors"></i>
                <input type="text" placeholder="Search for files, videos..." 
                    class="w-full bg-slate-100 border-none rounded-full py-2.5 pl-11 pr-4 focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all outline-none">
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-semibold hover:bg-indigo-700 transition-colors flex items-center gap-2">
                <i class="fas fa-plus"></i> <span class="hidden sm:inline">Upload</span>
            </button>
            <div class="flex items-center gap-3 pl-4 border-l border-slate-200">
                <div class="text-right hidden sm:block">
                    <p class="text-sm font-bold leading-none" id="userName">User Name</p>
                    <p class="text-xs text-slate-500" id="userEmail">user@example.com</p>
                </div>
                <button id="logoutBtn" class="w-10 h-10 rounded-full bg-slate-100 hover:bg-red-50 hover:text-red-600 transition-colors flex items-center justify-center">
                    <i class="fas fa-sign-out-alt"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <aside class="fixed top-16 left-0 bottom-0 w-64 bg-white border-r border-slate-200 py-6 overflow-y-auto hidden lg:block">
        <nav class="space-y-1">
            <a href="#" class="sidebar-item active flex items-center gap-3 px-6 py-3 font-medium transition-all">
                <i class="fas fa-home w-5"></i> Dashboard
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
                <i class="fas fa-folder w-5"></i> All Files
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
                <i class="fas fa-image w-5"></i> My Photos
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
                <i class="fas fa-video w-5"></i> My Videos
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all border-t border-slate-50 mt-4 pt-4">
                <i class="fas fa-heart w-5"></i> Favorites
            </a>
            <a href="#" class="sidebar-item flex items-center gap-3 px-6 py-3 font-medium text-slate-600 transition-all">
                <i class="fas fa-trash w-5"></i> Bin
            </a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="lg:ml-64 pt-24 px-6 pb-12">
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">Recent Videos</h2>
                <p class="text-slate-500">Continue watching where you left off</p>
            </div>
            <div class="flex gap-2">
                <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50"><i class="fas fa-th-large"></i></button>
                <button class="p-2 bg-white border border-slate-200 rounded-lg hover:bg-slate-50"><i class="fas fa-list"></i></button>
            </div>
        </div>

        <!-- Video Grid (YouTube Style) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
            <!-- Mockup Items -->
            <script>
                const videos = [
                    { title: "Building a Modern Backend with CI3", author: "DevTeam", views: "1.2k views", time: "2 hours ago", dur: "12:45" },
                    { title: "Tailwind CSS Layout Tips", author: "Frontend Master", views: "850 views", time: "5 hours ago", dur: "08:20" },
                    { title: "Docker Containerization Guide", author: "OpsExpert", views: "3.4k views", time: "1 day ago", dur: "15:10" },
                    { title: "MVC Pattern Explained", author: "CodeGuide", views: "2.1k views", time: "3 days ago", dur: "10:05" },
                    { title: "PHP 5.6 to 8.x Migration", author: "LegacyHero", views: "500 views", time: "1 week ago", dur: "25:30" },
                    { title: "Advanced SQL Optimization", author: "DBGuru", views: "4.2k views", time: "2 weeks ago", dur: "18:22" },
                    { title: "JavaScript Fetch API Tutorial", author: "WebFlow", views: "1.2k views", time: "1 month ago", dur: "09:15" },
                    { title: "Restful API Best Practices", author: "ApiNinja", views: "900 views", time: "2 months ago", dur: "14:50" }
                ];

                document.write(videos.map((vid, i) => `
                    <div class="group cursor-pointer">
                        <div class="relative aspect-video bg-slate-200 rounded-xl overflow-hidden mb-3">
                            <img src="https://picsum.photos/seed/${i+10}/600/400" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute bottom-2 right-2 bg-black/80 text-white text-[10px] px-1.5 py-0.5 rounded font-bold">${vid.dur}</span>
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                                <i class="fas fa-play text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <div class="w-10 h-10 rounded-full bg-slate-200 flex-shrink-0 overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name=${vid.author}&background=random">
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2">${vid.title}</h3>
                                <p class="text-sm text-slate-500 mt-1">${vid.author}</p>
                                <p class="text-xs text-slate-400 font-medium">${vid.views} • ${vid.time}</p>
                            </div>
                        </div>
                    </div>
                `).join(''));
            </script>
        </div>
    </main>

    <script>
        async function checkAuth() {
            const token = localStorage.getItem('auth_token');
            if (!token) {
                window.location.href = '/login';
                return;
            }

            try {
                const response = await fetch('/api/users/current', {
                    method: 'GET',
                    headers: { 
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const result = await response.json();
                    document.getElementById('userName').innerText = result.data.name;
                    document.getElementById('userEmail').innerText = result.data.email;
                    document.getElementById('app').classList.remove('hidden');
                } else {
                    localStorage.removeItem('auth_token');
                    window.location.href = '/login';
                }
            } catch (error) {
                console.error('Auth check failed:', error);
                localStorage.removeItem('auth_token');
                window.location.href = '/login';
            }
        }

        document.getElementById('logoutBtn').addEventListener('click', () => {
            localStorage.removeItem('auth_token');
            window.location.href = '/login';
        });

        checkAuth();
    </script>
</body>
</html>
