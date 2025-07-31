<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulasi Dapur</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'fade-in': 'fadeIn 0.5s ease-in-out',
                        'slide-up': 'slideUp 0.6s ease-out',
                        'bounce-light': 'bounceLight 2s infinite',
                        'float': 'float 6s ease-in-out infinite',
                    },
                    keyframes: {
                        fadeIn: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(10px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        slideUp: {
                            '0%': {
                                opacity: '0',
                                transform: 'translateY(30px)'
                            },
                            '100%': {
                                opacity: '1',
                                transform: 'translateY(0)'
                            }
                        },
                        bounceLight: {
                            '0%, 100%': {
                                transform: 'translateY(0)'
                            },
                            '50%': {
                                transform: 'translateY(-10px)'
                            }
                        },
                        float: {
                            '0%, 100%': {
                                transform: 'translateY(0px) rotate(0deg)',
                                opacity: '0.3'
                            },
                            '50%': {
                                transform: 'translateY(-20px) rotate(180deg)',
                                opacity: '0.8'
                            }
                        }
                    },
                    backdropBlur: {
                        xs: '2px',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-8px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #4f46e5, #7c3aed);
        }

        .floating-particles {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .particle:nth-child(1) {
            width: 4px;
            height: 4px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }

        .particle:nth-child(2) {
            width: 6px;
            height: 6px;
            top: 60%;
            left: 20%;
            animation-delay: 1s;
        }

        .particle:nth-child(3) {
            width: 3px;
            height: 3px;
            top: 40%;
            left: 70%;
            animation-delay: 2s;
        }

        .particle:nth-child(4) {
            width: 5px;
            height: 5px;
            top: 80%;
            left: 80%;
            animation-delay: 3s;
        }

        .particle:nth-child(5) {
            width: 4px;
            height: 4px;
            top: 30%;
            left: 90%;
            animation-delay: 4s;
        }

        .table-row-hover:hover {
            background: linear-gradient(90deg, rgb(239 246 255), rgb(240 249 255));
            transform: scale(1.01);
        }

        /* Loading spinner */
        .spinner {
            border: 3px solid rgba(99, 102, 241, 0.2);
            border-top: 3px solid #6366f1;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }
    </style>
</head>

<body class="min-h-screen gradient-bg font-inter overflow-x-hidden">

    <!-- Floating Particles -->
    <div class="floating-particles">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Navbar -->
    <nav class="glass-effect sticky top-0 z-50 mb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div class="text-3xl">🧮</div>
                    <h1
                        class="text-xl font-bold text-white bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">
                        Kalkulasi SPPG
                    </h1>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:block">
                    <a href="/admin"
                        class="glass-effect px-6 py-2 rounded-full text-white font-medium hover:bg-white/30 transition-all duration-300 hover:-translate-y-1 hover:shadow-lg flex items-center space-x-2">
                        <span>Login</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <button id="mobileMenuBtn"
                    class="md:hidden glass-effect p-2 rounded-lg text-white hover:bg-white/20 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>

            <!-- Mobile Menu -->
            <div id="mobileMenu" class="hidden md:hidden border-t border-white/20 pt-4 pb-4">
                <a href="/admin"
                    class="block glass-effect px-4 py-3 rounded-lg text-white font-medium hover:bg-white/30 transition-colors text-center">
                    Login
                </a>
            </div>
        </div>
    </nav>

    <div class="relative z-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- Calculator Form -->
            <div class="max-w-md mx-auto mb-8">
                <div class="glass-effect rounded-3xl p-8 card-hover animate-fade-in">
                    <!-- Form Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Kalkulasi Bahan Baku</h2>
                    </div>

                    <!-- Form -->
                    <form id="calculatorForm" class="space-y-6">
                        @csrf

                        <!-- Recipe Selection -->
                        <div class="space-y-2">
                            <label for="recipe_id"
                                class="flex items-center space-x-2 text-sm font-semibold text-gray-700">

                                <span>Nama Menu</span>
                            </label>
                            <select name="recipe_id" id="recipe_id" required
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-500/20 transition-all duration-300 bg-white/80 hover:bg-white/90">
                                <option value="">-- Pilih Menu Masakan --</option>
                                @foreach ($recipes as $recipe)
                                    <option value="{{ $recipe->id }}"
                                        data-base-portions="{{ $recipe->base_portions }}">
                                        {{ $recipe->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Portions Input -->
                        <div class="space-y-2">
                            <label for="portions"
                                class="flex items-center space-x-2 text-sm font-semibold text-gray-700">

                                <span>Jumlah Porsi</span>
                            </label>
                            <input type="number" name="portions" id="portions" min="1" step="1" required
                                placeholder="Masukkan jumlah porsi (contoh: 100)"
                                class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-green-500 focus:ring-4 focus:ring-green-500/20 transition-all duration-300 bg-white/80 hover:bg-white/90">
                        </div>

                        <!-- Submit Button -->
                        <button type="submit"
                            class="w-full btn-gradient text-white font-semibold py-4 px-6 rounded-xl hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 flex items-center justify-center space-x-2 group">

                            <span>Hitung Bahan</span>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loading" class="hidden text-center py-12">
                <div class="inline-block">
                    <div class="spinner mx-auto mb-4"></div>
                    <p class="text-white font-medium text-lg">Sedang menghitung bahan yang dibutuhkan...</p>
                </div>
            </div>

            <!-- Results -->
            <div id="results" class="hidden max-w-4xl mx-auto animate-slide-up">
                <div class="glass-effect rounded-3xl p-8 card-hover">
                    <!-- Results Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">Hasil Perhitungan</h2>
                    </div>

                    <!-- Ingredients Table -->
                    <div class="mb-6">
                        <h3 class="flex items-center space-x-2 text-lg font-semibold text-gray-800 mb-4">
                            <span>Bahan yang Dibutuhkan</span>
                        </h3>

                        <div class="overflow-hidden rounded-2xl shadow-lg">
                            <div class="overflow-x-auto">
                                <table class="w-full bg-white">
                                    <thead>
                                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                            <th
                                                class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider w-16">
                                                No</th>
                                            <th
                                                class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider">
                                                Bahan</th>
                                            <th
                                                class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider w-32">
                                                Jumlah</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ingredientsTable" class="divide-y divide-gray-200">
                                        <!-- Data akan diisi oleh JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Note -->
                    <div class="bg-gradient-to-r from-amber-50 to-yellow-50 border-l-4 border-amber-400 rounded-lg p-4">
                        <div class="flex items-start space-x-3">
                            <div class="text-2xl">💡</div>
                            <div>
                                <p class="text-amber-800 font-medium">
                                    <strong>Catatan:</strong> Hasil perhitungan ini berdasarkan Standar Operasional
                                    Prosedur (SOP) dapur.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div id="errorMessage" class="hidden max-w-md mx-auto">
                <div class="bg-gradient-to-r from-red-50 to-pink-50 border border-red-200 rounded-2xl p-6 text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <p class="text-red-800 font-medium"></p>
                </div>
            </div>

            <!-- Footer -->
            <footer class="text-center mt-16 pb-8">
                <p class="text-white/80 text-sm">
                    Made with ❤️ by <span
                        class="font-semibold bg-gradient-to-r from-white to-blue-100 bg-clip-text text-transparent">RBJCorp.id</span>
                </p>
            </footer>

        </div>
    </div>

    <script>
        // Setup CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // DOM elements
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

        // Form submission
        elements.form.addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const recipeId = formData.get('recipe_id');
            const portions = formData.get('portions');

            // Validation
            if (!recipeId || !portions) {
                showError('Harap pilih menu dan masukkan jumlah porsi yang valid!');
                return;
            }

            if (portions < 1) {
                showError('Jumlah porsi harus minimal 1!');
                return;
            }

            // Show loading
            showLoading();

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
                    setTimeout(() => displayResults(data), 800);
                } else {
                    throw new Error(data.message || 'Terjadi kesalahan saat menghitung');
                }

            } catch (error) {
                console.error('Error:', error);
                showError('Terjadi kesalahan: ' + error.message);
            } finally {
                hideLoading();
            }
        });

        // Show loading state
        function showLoading() {
            elements.loading.classList.remove('hidden');
            elements.results.classList.add('hidden');
            elements.errorMessage.classList.add('hidden');
        }

        // Hide loading state
        function hideLoading() {
            elements.loading.classList.add('hidden');
        }

        // Display results
        function displayResults(data) {
            const tbody = elements.ingredientsTable;
            tbody.innerHTML = '';

            data.ingredients.forEach((ingredient, index) => {
                const row = document.createElement('tr');
                row.className = 'table-row-hover transition-all duration-200 ' +
                    (index % 2 === 0 ? 'bg-gray-50' : 'bg-white');

                row.innerHTML = `
                    <td class="px-6 py-4 text-center font-semibold text-blue-600">${index + 1}</td>
                    <td class="px-6 py-4 font-medium text-gray-900">${ingredient.name}</td>
                    <td class="px-6 py-4 text-right font-bold text-green-600">${ingredient.calculated_amount} ${ingredient.unit}</td>
                `;

                tbody.appendChild(row);
            });

            elements.results.classList.remove('hidden');

            // Smooth scroll to results
            setTimeout(() => {
                elements.results.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }

        // Show error message
        function showError(message) {
            elements.errorMessage.querySelector('p').textContent = message;
            elements.errorMessage.classList.remove('hidden');
            elements.results.classList.add('hidden');

            // Auto hide after 5 seconds
            setTimeout(() => {
                elements.errorMessage.classList.add('hidden');
            }, 5000);
        }

        // Mobile menu toggle
        elements.mobileMenuBtn.addEventListener('click', function() {
            elements.mobileMenu.classList.toggle('hidden');
        });

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(e) {
            if (!elements.mobileMenu.contains(e.target) && !elements.mobileMenuBtn.contains(e.target)) {
                elements.mobileMenu.classList.add('hidden');
            }
        });

        // Auto focus on recipe select
        elements.recipeSelect.focus();
    </script>
</body>

</html>
