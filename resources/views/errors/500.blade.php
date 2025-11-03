<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Internal Server Error</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-orange-600">500</h1>
        <p class="text-2xl font-semibold text-gray-900 mb-4">Terjadi Kesalahan Server</p>
        <p class="text-gray-600 mb-6">Maaf, ada yang salah di sisi kami. Tim sedang memperbaiki.</p>
        <a href="{{ route('user.form') }}" class="bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold">
            Kembali ke Home
        </a>
    </div>
</body>
</html>