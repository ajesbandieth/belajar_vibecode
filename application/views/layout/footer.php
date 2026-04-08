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
    </script>
</body>
</html>
