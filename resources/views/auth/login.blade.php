<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>BioVerify AI | Administrator Sign In</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        body {
            background: #f1f5f9;
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-xl overflow-hidden">

        <header class="bg-blue-900 text-white px-6 py-7 text-center">
            <h1 class="text-3xl font-bold">BioVerify AI</h1>
            <p class="text-blue-100 mt-2">Administrator Sign In</p>
        </header>

        <main class="p-6 md:p-8">

            @if ($errors->any())
                <div role="alert" class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                        class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3 text-slate-900 outline-none focus:border-blue-600 focus:ring-2 focus:ring-blue-100">
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1" class="rounded border-slate-300">
                    Keep me signed in
                </label>

                <button type="submit"
                    class="w-full rounded-xl bg-blue-900 px-6 py-3 font-bold text-white transition hover:bg-blue-800">
                    Sign in
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-500">
                Personnel verifying their identity should use
                <a href="{{ route('biometric.login') }}" class="font-semibold text-blue-700 hover:text-blue-900">
                    biometric verification
                </a>.
            </p>
        </main>
    </div>
</body>

</html>
