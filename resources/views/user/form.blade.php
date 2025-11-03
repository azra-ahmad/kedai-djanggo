<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kedai Djanggo - Mulai Pesan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&family=Quicksand:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #EF7722;
            --secondary: #FAA533;
            --light: #EBEBEB;
            --dark: #000000;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            /* Body: Lebih lembut & fun */
            background: linear-gradient(135deg, #FFFFFF 0%, #EBEBEB 50%, #FAFAFA 100%);
            font-weight: 500;
            /* Default medium untuk flow alami */
        }

        .font-display {
            font-family: "Gravitas One", serif;
            font-weight: 400;
            font-style: normal;
        }

        .font-heading {
            font-family: 'Playfair Display', serif;
            /* Sub-heading: Elegant tapi tidak kaku */
        }

        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Wave SVG */
        .wave-svg path {
            fill: var(--light);
        }

        /* Logo Glow (Softened) */
        .logo-glow {
            box-shadow: 0 8px 32px rgba(239, 119, 34, 0.25);
        }

        /* Gradient Background */
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        /* Button Primary */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            box-shadow: 0 6px 20px rgba(239, 119, 34, 0.3);
            font-weight: 700;
            font-family: 'Quicksand', sans-serif;
        }

        .btn-primary:hover {
            box-shadow: 0 10px 28px rgba(239, 119, 34, 0.4);
            transform: translateY(-2px) scale(1.02);
        }

        .btn-primary:active {
            transform: scale(0.98);
        }

        /* Input Focus */
        .input-focus:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(239, 119, 34, 0.15);
            outline: none;
        }

        /* Info Box */
        .info-box {
            background: linear-gradient(135deg, rgba(250, 165, 51, 0.1) 0%, rgba(239, 119, 34, 0.1) 100%);
            border: 2px solid rgba(250, 165, 51, 0.3);
        }

        /* Form Card */
        .form-card {
            background: #FFFFFF;
            border: 2px solid var(--light);
            box-shadow: 0 16px 40px rgba(0, 0, 0, 0.08);
        }

        .form-card:hover {
            box-shadow: 0 20px 48px rgba(239, 119, 34, 0.15);
        }

        /* Gradient Text */
        .gradient-text {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Label & Placeholder (Rounded feel) */
        label,
        input::placeholder {
            font-weight: 500;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <!-- Decorative Top Wave -->
    <div class="gradient-bg fixed inset-0 -z-10 overflow-hidden">
        <svg class="absolute bottom-0 w-full" viewBox="0 0 1440 160" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0,80L48,96C96,112,192,144,288,144C384,144,480,112,576,96C672,80,768,80,864,96C960,112,1056,144,1152,144C1248,144,1344,112,1392,96L1440,80L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z" class="wave-svg path"></path>
        </svg>
    </div>

    <div class="max-w-sm w-full z-10">
        <!-- Logo & Welcome -->
        <div class="text-center mb-6">
            <div class="relative inline-block">
                <div class="absolute inset-0 bg-[#FAA533] rounded-full blur-2xl opacity-40 animate-pulse"></div>
                <img src="{{ asset('images/logo-kedai-djanggo.jpg') }}" alt="Logo Kedai Djanggo" class="relative w-24 h-24 mx-auto rounded-full logo-glow object-cover border-4 border-white shadow-2xl">
            </div>
            <h1 class="text-4xl font-display font-bold tex text=black mt-4 mb-1">KEDAI DJANGGO</h1>
            <p class=" text-gray-900 font-lg">Mulai pesan sekarang ☕</p>
        </div>

        <!-- Form Card -->
        <div class="form-card rounded-3xl p-6 shadow-2xl backdrop-blur-xl bg-white/95">
            <div class="mb-5">
                <h2 class="text-2xl font-heading font-bold text-[#000000] mb-1">Selamat Datang! 👋</h2>
                <p class="text-sm text-gray-600">Isi data untuk pesan</p>
            </div>

            <form action="{{ route('user.submitIdentity') }}" method="POST" class="space-y-4">
                @csrf

                <!-- Name Input -->
                <div>
                    <label for="name" class="block text-sm font-semibold text-[#000000] mb-1.5">
                        Nama Lengkap
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 input-focus text-[#000000] placeholder-gray-400 text-base font-medium"
                            placeholder="Nama Anda">
                    </div>
                    @error('name')
                    <p class="text-[#EF7722] text-sm mt-1.5 flex items-center gap-1 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Phone Input -->
                <div>
                    <label for="phone" class="block text-sm font-semibold text-[#000000] mb-1.5">
                        Nomor Telepon
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                        </div>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="{{ old('phone') }}"
                            required
                            class="w-full pl-12 pr-4 py-3 rounded-xl border-2 border-gray-200 input-focus text-[#000000] placeholder-gray-400 text-base font-medium"
                            placeholder="08xxxxxxxxxx">
                    </div>
                    @error('phone')
                    <p class="text-[#EF7722] text-sm mt-1.5 flex items-center gap-1 font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        {{ $message }}
                    </p>
                    @enderror
                </div>

                <!-- Info Box (Compact) -->
                <div class="info-box rounded-2xl p-3 shadow-inner text-sm">
                    <div class="flex items-start gap-3">
                        <div class="bg-white/80 rounded-xl p-2 shadow-md flex-shrink-0">
                            <svg class="w-4 h-4 text-[#EF7722]" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <p class="text-gray-700 font-medium leading-tight">
                            Data aman untuk konfirmasi & pengantaran ke meja ❤️
                        </p>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full btn-primary text-white py-4 rounded-xl text-lg font-bold shadow-2xl mt-5 transform transition-all duration-300">
                    <span class="flex items-center justify-center gap-2">
                        Mulai Pesan
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    </div>
</body>
</html>