<?php $this->load->view('layout/header', ['title' => 'Dashboard']); ?>
<?php $this->load->view('layout/sidebar', ['active_menu' => 'dashboard']); ?>

<!-- Main Content -->
<main id="main-content" class="lg:ml-64 pt-24 px-4 sm:px-6 pb-12 transition-all">
    <div class="mb-8 flex items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 leading-tight">Recent Videos</h2>
            <p class="text-slate-500 text-sm hidden sm:block">Continue watching where you left off</p>
        </div>
        <div class="flex gap-1 p-1 bg-slate-100 rounded-xl">
            <button id="viewGrid" class="p-2 rounded-lg transition-all text-indigo-600 bg-white shadow-sm"><i class="fas fa-th-large"></i></button>
            <button id="viewList" class="p-2 rounded-lg transition-all text-slate-500 hover:text-indigo-600"><i class="fas fa-list"></i></button>
        </div>
    </div>

    <!-- Video Container -->
    <div id="videoContainer" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6">
        <!-- Items are rendered via JS -->
    </div>
</main>

<script>
    const videos = [
        { id: 1, title: "Building a Modern Backend with CI3", author: "DevTeam", views: "1.2k views", time: "2 hours ago", dur: "12:45" },
        { id: 2, title: "Tailwind CSS Layout Tips", author: "Frontend Master", views: "850 views", time: "5 hours ago", dur: "08:20" },
        { id: 3, title: "Docker Containerization Guide", author: "OpsExpert", views: "3.4k views", time: "1 day ago", dur: "15:10" },
        { id: 4, title: "MVC Pattern Explained", author: "CodeGuide", views: "2.1k views", time: "3 days ago", dur: "10:05" },
        { id: 5, title: "PHP 5.6 to 8.x Migration", author: "LegacyHero", views: "500 views", time: "1 week ago", dur: "25:30" },
        { id: 6, title: "Advanced SQL Optimization", author: "DBGuru", views: "4.2k views", time: "2 weeks ago", dur: "18:22" },
        { id: 7, title: "JavaScript Fetch API Tutorial", author: "WebFlow", views: "1.2k views", time: "1 month ago", dur: "09:15" },
        { id: 8, title: "Restful API Best Practices", author: "ApiNinja", views: "900 views", time: "2 months ago", dur: "14:50" }
    ];

    function renderVideos() {
        const container = document.getElementById('videoContainer');
        const mainContent = document.getElementById('main-content');
        
        if (state.viewMode === 'list') {
            mainContent.classList.add('view-list');
            container.className = "flex flex-col gap-6";
            
            container.innerHTML = videos.map((vid, i) => `
                <div class="video-card group cursor-pointer bg-white p-3 rounded-2xl border border-slate-100 hover:shadow-lg transition-all">
                    <div class="thumbnail-container relative aspect-video bg-slate-200 rounded-xl overflow-hidden">
                        <img src="https://picsum.photos/seed/${vid.id + 10}/600/400" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        <span class="absolute bottom-2 right-2 bg-black/80 text-white text-[10px] px-1.5 py-0.5 rounded font-bold">${vid.dur}</span>
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/20 transition-colors flex items-center justify-center">
                            <i class="fas fa-play text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>
                    </div>
                    <div class="video-info py-1">
                        <h3 class="font-bold text-lg text-slate-900 group-hover:text-indigo-600 transition-colors line-clamp-2">${vid.title}</h3>
                        <div class="flex items-center gap-2 mt-2">
                            <img src="https://ui-avatars.com/api/?name=${vid.author}&background=random" class="w-6 h-6 rounded-full">
                            <p class="text-sm text-slate-600">${vid.author}</p>
                        </div>
                        <p class="text-sm text-slate-400 mt-2">${vid.views} • ${vid.time}</p>
                        <p class="text-sm text-slate-500 mt-3 line-clamp-2 hidden sm:block">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    </div>
                </div>
            `).join('');
        } else {
            mainContent.classList.remove('view-list');
            container.className = "grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-6";
            
            container.innerHTML = videos.map((vid, i) => `
                <div class="group cursor-pointer">
                    <div class="relative aspect-video bg-slate-200 rounded-xl overflow-hidden mb-3">
                        <img src="https://picsum.photos/seed/${vid.id + 10}/600/400" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
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
            `).join('');
        }
    }

    // Page-specific initialization
    function initPage(userData) {
        renderVideos();
    }

    document.getElementById('viewGrid').addEventListener('click', () => {
        state.viewMode = 'grid';
        document.getElementById('viewGrid').className = "p-2 rounded-lg transition-all text-indigo-600 bg-white shadow-sm";
        document.getElementById('viewList').className = "p-2 rounded-lg transition-all text-slate-500 hover:text-indigo-600";
        renderVideos();
    });

    document.getElementById('viewList').addEventListener('click', () => {
        state.viewMode = 'list';
        document.getElementById('viewList').className = "p-2 rounded-lg transition-all text-indigo-600 bg-white shadow-sm";
        document.getElementById('viewGrid').className = "p-2 rounded-lg transition-all text-slate-500 hover:text-indigo-600";
        renderVideos();
    });
</script>

<?php $this->load->view('layout/footer'); ?>
