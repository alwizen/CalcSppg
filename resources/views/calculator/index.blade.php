<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulasi Dapur</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- CSS Inline - menghilangkan Tailwind CDN yang berat -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            color: #374151;
            min-height: 100vh;
            background: url('/img/bg.png') center/contain no-repeat;
            position: relative;
        }

        .overlay {
            position: absolute;
            inset: 0;
            background-color: rgba(255, 255, 255, 0.75);
            z-index: 1;
        }

        .content {
            position: relative;
            z-index: 10;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        /* Navbar */
        .navbar {
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 50;
            margin-bottom: 2rem;
        }

        .nav-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 0;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #1f2937;
        }

        .login-btn {
            background: #4f46e5;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-weight: 500;
            transition: background 0.2s;
        }

        .login-btn:hover {
            background: #4338ca;
        }

        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #374151;
            cursor: pointer;
        }

        .mobile-menu {
            display: none;
            border-top: 1px solid #e5e7eb;
            padding-top: 1rem;
            padding-bottom: 1rem;
        }

        .mobile-menu .login-btn {
            display: block;
            text-align: center;
            width: 100%;
        }

        /* Form */
        .form-card {
            max-width: 28rem;
            margin: 0 auto 2rem;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
        }

        .form-title {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-title h3 {
            font-size: 1.125rem;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .submit-btn {
            background: #2563eb;
            color: white;
            padding: 0.5rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            display: block;
            margin: 1rem auto 0;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        /* Loading */
        .loading {
            text-align: center;
            padding: 2rem 0;
        }

        .spinner {
            width: 3rem;
            height: 3rem;
            border: 2px solid #f97316;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto 1rem;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Results */
        .results-card {
            max-width: 64rem;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 1.5rem;
        }

        .results-title {
            text-align: center;
            margin-bottom: 1rem;
        }

        .results-title h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 0.5rem;
        }

        .table-section h3 {
            font-size: 1.125rem;
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.75rem;
        }

        .table-container {
            overflow-x: auto;
            margin-bottom: 1rem;
        }

        .results-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 2px solid #e5e7eb;
        }

        .results-table th {
            background: #2563eb;
            color: white;
            padding: 0.75rem 1rem;
            text-align: left;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid #1d4ed8;
            border-right: 1px solid #1d4ed8;
        }

        .results-table th:last-child {
            border-right: none;
        }

        .results-table th:nth-child(1) {
            width: 50px;
            text-align: center;
        }

        .results-table th:nth-child(2) {
            width: 200px;
        }

        .results-table th:nth-child(3) {
            width: 120px;
            text-align: right;
        }

        .results-table td {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #e5e7eb;
            border-right: 1px solid #e5e7eb;
            font-size: 0.875rem;
        }

        .results-table td:last-child {
            border-right: none;
        }

        .results-table tr:nth-child(even) {
            background: #f9fafb;
        }

        .results-table tr:hover {
            background: #eff6ff;
        }

        .results-table td:first-child {
            font-weight: 500;
            color: #1f2937;
        }

        .results-table td:nth-child(3) {
            text-align: right;
            font-weight: bold;
            color: #1f2937;
        }

        /* Note */
        .note {
            background: rgba(254, 243, 199, 0.8);
            border-radius: 0.5rem;
            padding: 0.75rem;
            text-align: center;
        }

        .note p {
            color: #92400e;
            font-size: 0.875rem;
        }

        /* Error */
        .error-card {
            max-width: 28rem;
            margin: 0 auto;
            background: rgba(254, 242, 242, 0.9);
            border: 1px solid #fecaca;
            border-radius: 0.5rem;
            padding: 0.75rem;
            text-align: center;
        }

        .error-card p {
            color: #991b1b;
            font-size: 0.875rem;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 3rem;
            color: #6b7280;
        }

        /* Utilities */
        .hidden {
            display: none !important;
        }

        /* Mobile Responsive */
        @media (max-width: 768px) {
            .logo {
                font-size: 1.5rem;
            }

            .desktop-login {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }

            .mobile-menu.show {
                display: block;
            }

            .results-table th,
            .results-table td {
                padding: 0.5rem 0.75rem;
            }

            .results-table th:nth-child(1),
            .results-table td:first-child {
                width: 40px;
            }

            .results-table th:nth-child(2),
            .results-table td:nth-child(2) {
                width: 150px;
            }

            .results-table th:nth-child(3),
            .results-table td:nth-child(3) {
                width: 100px;
            }
        }

        @media (min-width: 1024px) {
            .login-btn span.lg-hidden {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="overlay"></div>

    <div class="content">
        <!-- Navbar -->
        <nav class="navbar">
            <div class="container">
                <div class="nav-content">
                    <div>
                        <h1 class="logo">🧮</h1>
                    </div>

                    <div class="desktop-login">
                        <a href="/admin" class="login-btn">
                            <span class="lg-visible">Login</span>
                            <span class="lg-hidden">Admin</span>
                        </a>
                    </div>

                    <button class="mobile-menu-btn" id="mobileMenuBtn">
                        <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <div class="mobile-menu" id="mobileMenu">
                    <a href="/admin" class="login-btn">Login</a>
                </div>
            </div>
        </nav>

        <div class="container">
            <!-- Form Kalkulator -->
            <div class="form-card">
                <div class="form-title">
                    <h3>Kalkulasi Bahan Baku Masakan</h3>
                </div>
                <form id="calculatorForm">
                    @csrf

                    <div class="form-group">
                        <label for="recipe_id" class="form-label">Nama Menu</label>
                        <select name="recipe_id" id="recipe_id" required class="form-select">
                            <option value="">-- Pilih Menu Masakan --</option>
                            @foreach ($recipes as $recipe)
                                <option value="{{ $recipe->id }}" data-base-portions="{{ $recipe->base_portions }}">
                                    {{ $recipe->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="portions" class="form-label">Jumlah Porsi yang Diinginkan</label>
                        <input type="number" name="portions" id="portions" min="1" step="1" required
                            placeholder="Contoh: 100" class="form-input">
                    </div>

                    <button type="submit" class="submit-btn">Hitung Bahan</button>
                </form>
            </div>

            <!-- Loading -->
            <div id="loading" class="loading hidden">
                <div class="spinner"></div>
                <p>Menghitung bahan...</p>
            </div>

            <!-- Hasil Perhitungan -->
            <div id="results" class="results-card hidden">
                <div class="results-title">
                    <h2>Hasil Perhitungan</h2>
                </div>

                <div class="table-section">
                    <h3>Bahan yang Dibutuhkan :</h3>

                    <div class="table-container">
                        <table class="results-table">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>Nama Bahan</th>
                                    <th>Jumlah</th>
                                </tr>
                            </thead>
                            <tbody id="ingredientsTable">
                                <!-- Data akan diisi oleh JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="note">
                    <p><strong>Catatan:</strong> Hasil perhitungan ini berdasarkan standard Operasional.</p>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="error-card hidden">
                <p></p>
            </div>

            <!-- Footer -->
            <div class="footer">
                Made With ❤️ <strong>RBJCorp.id</strong>
            </div>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // Setup CSRF token untuk AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Cache DOM elements
        const elements = {
            form: document.getElementById('calculatorForm'),
            loading: document.getElementById('loading'),
            results: document.getElementById('results'),
            errorMessage: document.getElementById('errorMessage'),
            ingredientsTable: document.getElementById('ingredientsTable'),
            mobileMenu: document.getElementById('mobileMenu'),
            mobileMenuBtn: document.getElementById('mobileMenuBtn'),
            recipeSelect: document.getElementById('recipe_id')
        };

        elements.form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const recipeId = formData.get('recipe_id');
            const portions = formData.get('portions');

            if (!recipeId || !portions) {
                showError('Harap pilih menu dan masukkan jumlah porsi!');
                return;
            }

            // Show loading
            elements.loading.classList.remove('hidden');
            elements.results.classList.add('hidden');
            elements.errorMessage.classList.add('hidden');

            try {
                const response = await fetch('/calculate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        recipe_id: recipeId,
                        portions: parseInt(portions)
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    displayResults(data);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan');
                }

            } catch (error) {
                showError('Terjadi kesalahan: ' + error.message);
            } finally {
                elements.loading.classList.add('hidden');
            }
        });

        function formatAmount(amount) {
            const num = parseFloat(amount);

            if (num === 0) {
                return '0';
            }

            if (num % 1 === 0) {
                return num.toString();
            }

            let formatted;
            if (num < 0.0001) {
                formatted = num.toFixed(8);
            } else if (num < 0.001) {
                formatted = num.toFixed(6);
            } else if (num < 0.01) {
                formatted = num.toFixed(5);
            } else if (num < 0.1) {
                formatted = num.toFixed(4);
            } else if (num < 1) {
                formatted = num.toFixed(3);
            } else {
                formatted = num.toFixed(2);
            }

            return formatted.replace(/\.?0+$/, '');
        }

        function displayResults(data) {
            const fragment = document.createDocumentFragment();

            data.ingredients.forEach((ingredient, index) => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${index + 1}</td>
                    <td>${ingredient.name}</td>
                    <td>${ingredient.calculated_amount} ${ingredient.unit}</td>
                `;
                fragment.appendChild(row);
            });

            elements.ingredientsTable.innerHTML = '';
            elements.ingredientsTable.appendChild(fragment);
            elements.results.classList.remove('hidden');

            elements.results.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function showError(message) {
            elements.errorMessage.querySelector('p').textContent = message;
            elements.errorMessage.classList.remove('hidden');

            setTimeout(() => {
                elements.errorMessage.classList.add('hidden');
            }, 5000);
        }

        // Auto focus
        elements.recipeSelect.focus();

        // Mobile menu toggle
        elements.mobileMenuBtn.addEventListener('click', function() {
            elements.mobileMenu.classList.toggle('show');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!elements.mobileMenu.contains(e.target) && !elements.mobileMenuBtn.contains(e.target)) {
                elements.mobileMenu.classList.remove('show');
            }
        });
    </script>
</body>

</html>
