<x-layouts.app>
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="bg-white rounded-lg shadow-lg p-8 text-center">
            @if($success)
                <div class="text-green-600 text-6xl mb-4">✅</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Success!</h1>
            @else
                <div class="text-red-600 text-6xl mb-4">❌</div>
                <h1 class="text-2xl font-bold text-gray-900 mb-4">Oops!</h1>
            @endif

            <p class="text-gray-600 mb-6">{{ $message }}</p>

            <a href="/" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition">
                Go to Homepage
            </a>
        </div>
    </div>
</x-layouts.app>
