<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - File Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
    <script>const BASE_URL = '<?php echo base_url(); ?>';</script>
</head>
<body class="bg-slate-50 flex items-center justify-center min-h-screen p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-slate-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-slate-900 mb-2">Welcome Back</h1>
            <p class="text-slate-500">Log in to manage your files</p>
        </div>

        <form id="loginForm" class="space-y-5">
            <div>
                <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email Address</label>
                <input type="email" id="email" name="email" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                    placeholder="name@example.com">
            </div>
            <div>
                <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                <input type="password" id="password" name="password" required
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                    placeholder="••••••••">
            </div>

            <div id="errorMessage" class="hidden p-3 rounded-lg bg-red-50 text-red-600 text-sm font-medium border border-red-100"></div>

            <button type="submit" id="submitBtn"
                class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-indigo-200 transition-all transform active:scale-[0.98]">
                Sign In
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-slate-500">
            Don't have an account? 
            <a href="<?php echo base_url('register'); ?>" class="text-indigo-600 font-semibold hover:underline">Register Now</a>
        </div>
    </div>

    <script>
        // Redirect if already logged in
        if (localStorage.getItem('auth_token')) {
            window.location.href = BASE_URL + 'dashboard';
        }

        document.getElementById('loginForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('submitBtn');
            const errorDiv = document.getElementById('errorMessage');
            
            errorDiv.classList.add('hidden');
            btn.disabled = true;
            btn.innerHTML = 'Signing in...';

            const formData = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                const response = await fetch(BASE_URL + 'api/users/login', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (response.ok) {
                    localStorage.setItem('auth_token', result.data);
                    window.location.href = BASE_URL + 'dashboard';
                } else {
                    errorDiv.innerText = result.data || 'Login failed. Please check your credentials.';
                    errorDiv.classList.remove('hidden');
                }
            } catch (error) {
                errorDiv.innerText = 'Network error. Please check your connection.';
                errorDiv.classList.remove('hidden');
            } finally {
                btn.disabled = false;
                btn.innerHTML = 'Sign In';
            }
        });
    </script>
</body>
</html>
