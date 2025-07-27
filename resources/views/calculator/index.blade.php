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
        <div id="results" class="hidden max-w-5xl mx-auto bg-white rounded-2xl shadow-lg p-8">
            <div class="text-center mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">Hasil Perhitungan</h2>
            </div>

            <div class="mb-6">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Bahan yang Dibutuhkan :</h3>

                <!-- Tabel Bahan -->
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse bg-white rounded-lg overflow-hidden shadow-sm">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-500 to-blue-600 text-white">
                                <th
                                    class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider border-b-2 border-blue-700">
                                    No
                                </th>
                                <th
                                    class="px-6 py-4 text-left text-sm font-semibold uppercase tracking-wider border-b-2 border-blue-700">
                                    Nama Bahan
                                </th>
                                <th
                                    class="px-6 py-4 text-right text-sm font-semibold uppercase tracking-wider border-b-2 border-blue-700">
                                    Jumlah
                                </th>
                                <th
                                    class="px-6 py-4 text-center text-sm font-semibold uppercase tracking-wider border-b-2 border-blue-700">
                                    Satuan
                                </th>
                            </tr>
                        </thead>
                        <tbody id="ingredientsTable">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-4 text-center">
                <p class="text-red-800">
                    <strong>Catatan:</strong> Hasil perhitungan ini berdasarkan standard Oprational.
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

        function formatAmount(amount) {
            const num = parseFloat(amount);

            // Jika angka 0, tampilkan 0
            if (num === 0) {
                return '0';
            }

            // Jika angka bulat, tampilkan tanpa desimal
            if (num % 1 === 0) {
                return num.toString();
            }

            // Untuk angka desimal, tentukan jumlah digit desimal berdasarkan ukuran angka
            let formatted;
            if (num < 0.0001) {
                // Untuk angka sangat kecil, gunakan presisi tinggi
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
            // Update ingredients table
            const ingredientsTable = document.getElementById('ingredientsTable');
            ingredientsTable.innerHTML = data.ingredients.map((ingredient, index) => `
                <tr class="${index % 2 === 0 ? 'bg-gray-50' : 'bg-white'} hover:bg-blue-50 transition-colors">
                    <td class="px-6 py-4 text-sm text-gray-900 font-medium border-b border-gray-200">
                        ${index + 1}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900 border-b border-gray-200">
                        ${ingredient.name}
                    </td>
                    <td class="px-6 py-4 text-sm text-right font-bold text-black-600 border-b border-gray-200">
                        ${ingredient.calculated_amount}
                    </td>
                    <td class="px-6 py-4 text-sm text-center text-gray-700 border-b border-gray-200">
                        ${ingredient.unit}
                    </td>
                </tr>
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
