<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kalkulator Dapur</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gradient-to-br from-green-50 to-red-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-2">🧮 Kalkulator Dapur SPPG</h1>
            <p class="text-gray-600">Hitung bahan masakan untuk porsi yang diinginkan</p>
        </div>

        <!-- Form Kalkulator -->
        <div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-lg p-8 mb-8">
            <form id="calculatorForm" class="space-y-6">
                @csrf

                <!-- Pilih Menu -->
                <div>
                    <label for="recipe_id" class="block text-sm font-semibold text-gray-700 mb-3">
                        Nama Menu
                    </label>
                    <select name="recipe_id" id="recipe_id" required
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-200 focus:border-orange-500 transition-all">
                        <option value="">-- Pilih Menu Masakan --</option>
                        @foreach ($recipes as $recipe)
                            <option value="{{ $recipe->id }}" data-base-portions="{{ $recipe->base_portions }}">
                                {{ $recipe->name }}
                                {{-- ({{ $recipe->base_portions }} porsi) --}}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Input Jumlah Porsi -->
                <div>
                    <label for="portions" class="block text-sm font-semibold text-gray-700 mb-3">
                        Jumlah Porsi yang Diinginkan
                    </label>
                    <input type="number" name="portions" id="portions" min="1" step="1" required
                        placeholder="Contoh: 100"
                        class="w-full px-4 py-3 text-lg border-2 border-gray-300 rounded-xl focus:ring-4 focus:ring-orange-200 focus:border-orange-500 transition-all">
                </div>

                <!-- Tombol Hitung -->
                <div class="text-center">
                    <button type="submit"
                        class="bg-gradient-to-r from-blue-500 to-blue-800 hover:from-blue-800 hover:to-blue-400 text-white font-bold py-4 px-8 rounded-xl text-lg shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                        Hitung Bahan
                    </button>
                </div>
            </form>
        </div>

        <!-- Loading -->
        <div id="loading" class="hidden text-center py-8">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-500 mx-auto mb-4"></div>
            <p class="text-gray-600">Menghitung bahan...</p>
        </div>

        <!-- Hasil Perhitungan -->
        <div id="results" class="hidden max-w-4xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Hasil Perhitungan</h2>
                <div id="recipeInfo" class="bg-orange-50 rounded-lg p-4 inline-block">
                    <!-- Info resep akan diisi oleh JavaScript -->
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Bahan yang Dibutuhkan :</h3>
                <div id="ingredientsList" class="grid md:grid-cols-2 gap-4">
                    <!-- Daftar bahan akan diisi oleh JavaScript -->
                </div>
            </div>

            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-blue-800">
                    💡 <strong>Catatan:</strong> Hasil perhitungan ini berdasarkan standard Oprational.
                </p>
            </div>
        </div>

        <!-- Error Message -->
        <div id="errorMessage"
            class="hidden max-w-2xl mx-auto bg-red-50 border border-red-200 rounded-lg p-4 text-center">
            <p class="text-red-800"></p>
        </div>

        <!-- Admin Link -->
        <div class="text-center mt-12">
            <a href="#" class="inline-flex items-center text-gray-600 hover:text-orange-600 transition-colors">
                Made With ❤️ <b> Solu8i Project</b>
            </a>
        </div>
    </div>

    <script>
        // Setup CSRF token untuk AJAX
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        document.getElementById('calculatorForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const recipeId = formData.get('recipe_id');
            const portions = formData.get('portions');

            if (!recipeId || !portions) {
                showError('Harap pilih menu dan masukkan jumlah porsi!');
                return;
            }

            // Show loading
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('results').classList.add('hidden');
            document.getElementById('errorMessage').classList.add('hidden');

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
                document.getElementById('loading').classList.add('hidden');
            }
        });

        function displayResults(data) {
            // Update recipe info
            const recipeInfo = document.getElementById('recipeInfo');
            recipeInfo.innerHTML = `
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <div class="text-gray-600">Menu Dipilih</div>
                        <div class="font-semibold text-orange-800">${data.recipe_name}</div>
                    </div>
                    <div>
                        <div class="text-gray-600">Porsi Diminta</div>
                        <div class="font-semibold text-orange-800">${data.requested_portions}</div>
                    </div>
                    
                </div>
            `;

            // Update ingredients list
            const ingredientsList = document.getElementById('ingredientsList');
            ingredientsList.innerHTML = data.ingredients.map(ingredient => `
                <div class="bg-gray-50 rounded-lg p-4 flex justify-between items-center hover:bg-gray-100 transition-colors">
                    <span class="font-medium text-gray-700">${ingredient.name}</span>
                    <span class="text-xl font-bold text-orange-600">
                        ${ingredient.calculated_amount} ${ingredient.unit}
                    </span>
                </div>
            `).join('');

            // Show results
            document.getElementById('results').classList.remove('hidden');

            // Scroll to results
            document.getElementById('results').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.querySelector('p').textContent = message;
            errorDiv.classList.remove('hidden');

            // Auto hide after 5 seconds
            setTimeout(() => {
                errorDiv.classList.add('hidden');
            }, 5000);
        }

        // Auto focus pada form pertama kali load
        document.getElementById('recipe_id').focus();
    </script>
</body>

</html>
