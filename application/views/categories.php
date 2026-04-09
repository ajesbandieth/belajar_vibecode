<?php $this->load->view('layout/header', ['title' => 'Manage Categories']); ?>
<?php $this->load->view('layout/sidebar', ['active_menu' => 'categories']); ?>

<!-- Main Content -->
<main id="main-content" class="lg:ml-64 pt-24 px-4 sm:px-6 pb-12 transition-all">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 leading-tight">Manage Categories</h2>
            <p class="text-slate-500 text-sm">Organize your files with custom categories</p>
        </div>
        <button id="btnAddCategory" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl font-semibold hover:bg-indigo-700 transition-all flex items-center justify-center gap-2 shadow-lg shadow-indigo-200">
            <i class="fas fa-plus"></i> Add New Category
        </button>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <!-- Table Header / Search -->
        <div class="p-4 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="relative max-w-sm w-full">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Search categories..." 
                    class="w-full bg-slate-50 border border-slate-200 rounded-lg py-2 pl-10 pr-4 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all text-sm">
            </div>
            <div class="text-sm text-slate-500">
                <span id="showingText">Showing 0 of 0 entries</span>
            </div>
        </div>

        <!-- Table Content -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-600 text-xs uppercase tracking-wider font-semibold">
                        <th class="px-6 py-4 border-b border-slate-100">No</th>
                        <th class="px-6 py-4 border-b border-slate-100">Category Name</th>
                        <th class="px-6 py-4 border-b border-slate-100">Slug</th>
                        <th class="px-6 py-4 border-b border-slate-100">Created At</th>
                        <th class="px-6 py-4 border-b border-slate-100 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody id="categoryTableBody" class="divide-y divide-slate-50">
                    <!-- Data will be rendered here -->
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-spinner fa-spin text-2xl"></i>
                                <p>Loading categories...</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination Footer -->
        <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <button id="prevPage" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-chevron-left text-xs"></i>
                </button>
                <div id="pageNumbers" class="flex gap-1">
                    <!-- Page numbers will be rendered here -->
                </div>
                <button id="nextPage" class="p-2 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 disabled:opacity-50 disabled:cursor-not-allowed transition-all">
                    <i class="fas fa-chevron-right text-xs"></i>
                </button>
            </div>
            <div class="text-xs text-slate-400 hidden sm:block">
                Page <span id="currentPageText">1</span> of <span id="totalPagesText">1</span>
            </div>
        </div>
    </div>
</main>

<!-- Category Modal -->
<div id="categoryModal" class="fixed inset-0 z-50 hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"></div>
    
    <!-- Modal Content -->
    <div class="absolute inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all scale-95 opacity-0 duration-300" id="modalContainer">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 id="modalTitle" class="text-xl font-bold text-slate-900">Add New Category</h3>
                <button id="btnCloseModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="categoryForm" class="p-6 space-y-4">
                <input type="hidden" id="categoryId">
                <div>
                    <label for="categoryName" class="block text-sm font-semibold text-slate-700 mb-1.5">Category Name</label>
                    <input type="text" id="categoryName" name="category_name" required
                        class="w-full bg-slate-50 border border-slate-200 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition-all"
                        placeholder="e.g. Work Videos">
                </div>
                
                <div class="pt-4 flex items-center justify-end gap-3">
                    <button type="button" id="btnCancel" class="px-4 py-2 text-sm font-semibold text-slate-600 hover:bg-slate-50 rounded-lg transition-all">Cancel</button>
                    <button type="submit" id="btnSave" class="px-6 py-2 bg-indigo-600 text-white text-sm font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 flex items-center gap-2">
                        <span id="btnSaveText">Save Category</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Categories Management Logic
    const categoryState = {
        allData: [],
        filteredData: [],
        currentPage: 1,
        itemsPerPage: 5,
        searchQuery: '',
        isLoading: false,
        editingId: null
    };

    function initPage(userData) {
        fetchCategories();
    }

    async function fetchCategories() {
        categoryState.isLoading = true;
        renderTable();
        
        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch('/api/categories', {
                headers: { 'Authorization': 'Bearer ' + token }
            });

            if (response.ok) {
                const result = await response.json();
                categoryState.allData = result.data;
            } else {
                console.error('Failed to fetch categories');
            }
        } catch (error) {
            console.error('Error fetching categories:', error);
        } finally {
            categoryState.isLoading = false;
            applySearch();
        }
    }

    function applySearch() {
        categoryState.filteredData = categoryState.allData.filter(item => 
            item.category_name.toLowerCase().includes(categoryState.searchQuery.toLowerCase()) ||
            item.slug.toLowerCase().includes(categoryState.searchQuery.toLowerCase())
        );
        categoryState.currentPage = 1;
        renderTable();
    }

    function renderTable() {
        const tbody = document.getElementById('categoryTableBody');
        const showingText = document.getElementById('showingText');
        const currentPageText = document.getElementById('currentPageText');
        const totalPagesText = document.getElementById('totalPagesText');
        
        if (categoryState.isLoading) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400"><div class="flex flex-col items-center gap-2"><i class="fas fa-spinner fa-spin text-2xl"></i><p>Loading categories...</p></div></td></tr>`;
            return;
        }

        if (categoryState.filteredData.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="px-6 py-12 text-center text-slate-400"><div class="flex flex-col items-center gap-2"><i class="fas fa-folder-open text-2xl"></i><p>${categoryState.searchQuery ? 'No categories found matching your search' : 'No categories available'}</p></div></td></tr>`;
            showingText.innerText = `Showing 0 of 0 entries`;
            currentPageText.innerText = 1;
            totalPagesText.innerText = 1;
            renderPagination(1);
            return;
        }

        const startIndex = (categoryState.currentPage - 1) * categoryState.itemsPerPage;
        const endIndex = Math.min(startIndex + categoryState.itemsPerPage, categoryState.filteredData.length);
        const paginatedData = categoryState.filteredData.slice(startIndex, endIndex);
        const totalPages = Math.ceil(categoryState.filteredData.length / categoryState.itemsPerPage);

        tbody.innerHTML = paginatedData.map((item, index) => `
            <tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-4 text-sm text-slate-500 font-medium">${startIndex + index + 1}</td>
                <td class="px-6 py-4 text-sm text-slate-900 font-bold">${item.category_name}</td>
                <td class="px-6 py-4 text-sm text-slate-500"><span class="bg-slate-100 px-2 py-0.5 rounded text-xs font-mono">${item.slug}</span></td>
                <td class="px-6 py-4 text-sm text-slate-400">${new Date(item.created_at).toLocaleDateString()}</td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <button onclick="openModal(${item.id})" class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-all" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteCategory(${item.id})" class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');

        showingText.innerText = `Showing ${startIndex + 1} to ${endIndex} of ${categoryState.filteredData.length} entries`;
        currentPageText.innerText = categoryState.currentPage;
        totalPagesText.innerText = totalPages;
        renderPagination(totalPages);
    }

    function renderPagination(totalPages) {
        const container = document.getElementById('pageNumbers');
        const prevBtn = document.getElementById('prevPage');
        const nextBtn = document.getElementById('nextPage');

        prevBtn.disabled = categoryState.currentPage === 1;
        nextBtn.disabled = categoryState.currentPage === totalPages;

        let html = '';
        const maxButtons = 5;
        let startPage = Math.max(1, categoryState.currentPage - Math.floor(maxButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxButtons - 1);

        if (endPage - startPage + 1 < maxButtons) {
            startPage = Math.max(1, endPage - maxButtons + 1);
        }

        for (let i = startPage; i <= endPage; i++) {
            html += `
                <button onclick="goToPage(${i})" class="w-8 h-8 flex items-center justify-center rounded-lg text-sm font-medium transition-all ${i === categoryState.currentPage ? 'bg-indigo-600 text-white shadow-md shadow-indigo-100' : 'text-slate-600 hover:bg-slate-100 bg-white border border-slate-200'}">
                    ${i}
                </button>
            `;
        }
        container.innerHTML = html;
    }

    function goToPage(page) {
        categoryState.currentPage = page;
        renderTable();
    }

    document.getElementById('prevPage').addEventListener('click', () => {
        if (categoryState.currentPage > 1) {
            categoryState.currentPage--;
            renderTable();
        }
    });

    document.getElementById('nextPage').addEventListener('click', () => {
        const totalPages = Math.ceil(categoryState.filteredData.length / categoryState.itemsPerPage);
        if (categoryState.currentPage < totalPages) {
            categoryState.currentPage++;
            renderTable();
        }
    });

    document.getElementById('searchInput').addEventListener('input', (e) => {
        categoryState.searchQuery = e.target.value;
        applySearch();
    });

    // Modal Operations
    const modal = document.getElementById('categoryModal');
    const modalContainer = document.getElementById('modalContainer');
    const categoryForm = document.getElementById('categoryForm');

    function openModal(id = null) {
        categoryState.editingId = id;
        const title = document.getElementById('modalTitle');
        const btnText = document.getElementById('btnSaveText');
        const nameInput = document.getElementById('categoryName');
        const idInput = document.getElementById('categoryId');

        if (id) {
            const item = categoryState.allData.find(c => c.id == id);
            title.innerText = 'Edit Category';
            btnText.innerText = 'Update Category';
            nameInput.value = item ? item.category_name : '';
            idInput.value = id;
        } else {
            title.innerText = 'Add New Category';
            btnText.innerText = 'Save Category';
            categoryForm.reset();
            idInput.value = '';
        }

        modal.classList.remove('hidden');
        setTimeout(() => {
            modalContainer.classList.remove('scale-95', 'opacity-0');
            modalContainer.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        modalContainer.classList.remove('scale-100', 'opacity-100');
        modalContainer.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 300);
    }

    document.getElementById('btnAddCategory').addEventListener('click', () => openModal());
    document.getElementById('btnCloseModal').addEventListener('click', closeModal);
    document.getElementById('btnCancel').addEventListener('click', closeModal);
    document.getElementById('btnCancel').addEventListener('click', closeModal);

    categoryForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const saveBtn = document.getElementById('btnSave');
        saveBtn.disabled = true;
        const originalText = document.getElementById('btnSaveText').innerText;
        document.getElementById('btnSaveText').innerHTML = `<i class="fas fa-spinner fa-spin mr-2"></i> Saving...`;

        const id = document.getElementById('categoryId').value;
        const name = document.getElementById('categoryName').value;
        const token = localStorage.getItem('auth_token');

        const url = id ? `/api/categories/${id}` : '/api/categories';
        const method = id ? 'PUT' : 'POST';

        try {
            const response = await fetch(url, {
                method: method,
                headers: { 
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ category_name: name })
            });

            if (response.ok) {
                closeModal();
                fetchCategories();
            } else {
                const error = await response.json();
                alert('Error: ' + error.data);
            }
        } catch (error) {
            console.error('Error saving category:', error);
            alert('An error occurred while saving.');
        } finally {
            saveBtn.disabled = false;
            document.getElementById('btnSaveText').innerText = originalText;
        }
    });

    async function deleteCategory(id) {
        const item = categoryState.allData.find(c => c.id == id);
        if (!confirm(`Are you sure you want to delete "${item.category_name}"? This will also affect files in this category.`)) return;

        try {
            const token = localStorage.getItem('auth_token');
            const response = await fetch(`/api/categories/${id}`, {
                method: 'DELETE',
                headers: { 'Authorization': 'Bearer ' + token }
            });

            if (response.ok) {
                fetchCategories();
            } else {
                const error = await response.json();
                alert('Error: ' + error.data);
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            alert('An error occurred during deletion.');
        }
    }
</script>

<?php $this->load->view('layout/footer'); ?>
