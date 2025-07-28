@extends('layouts.install')
@section('content')
    <div class="max-w-2xl mx-auto w-full py-10">
        <!-- Success Animation -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-500 rounded-full mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h3 class="text-3xl mb-2 font-bold text-white">
                🎉 Installation Complete!
            </h3>
            <p class="text-gray-400">Your streaming platform has been successfully installed and configured.</p>
        </div>

        @if(session('error'))
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                <strong>Note:</strong> {{ session('error') }}
            </div>
        @endif

        <!-- Installation Summary -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                📊 Installation Summary
            </h4>
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-green-100 text-green-800 rounded-lg p-4 text-center">
                    <div class="font-semibold">✅ Database</div>
                    <div class="text-sm">Configured & Migrated</div>
                </div>
                <div class="bg-green-100 text-green-800 rounded-lg p-4 text-center">
                    <div class="font-semibold">✅ Admin Account</div>
                    <div class="text-sm">Created Successfully</div>
                </div>
                <div class="bg-green-100 text-green-800 rounded-lg p-4 text-center">
                    <div class="font-semibold">✅ Storage</div>
                    <div class="text-sm">Linked & Configured</div>
                </div>
                <div class="bg-green-100 text-green-800 rounded-lg p-4 text-center">
                    <div class="font-semibold">✅ Optimization</div>
                    <div class="text-sm">System Optimized</div>
                </div>
            </div>
        </div>

        <!-- Admin Credentials -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                🔐 Administrator Credentials
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white rounded-lg p-4 border">
                    <div class="text-gray-600 text-sm mb-1">Email Address</div>
                    <div class="text-gray-900 font-semibold text-lg">admin@admin.com</div>
                </div>
                <div class="bg-white rounded-lg p-4 border">
                    <div class="text-gray-600 text-sm mb-1">Password</div>
                    <div class="text-gray-900 font-semibold text-lg">admin</div>
                </div>
            </div>
            <div class="mt-4 text-sm text-blue-700">
                <strong>Important:</strong> Please change these default credentials after your first login for security reasons.
            </div>
        </div>

        <!-- Next Steps -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                🚀 Next Steps
            </h4>
            <div class="space-y-3">
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 mt-0.5">1</div>
                    <div>
                        <div class="text-white font-medium">Access Your Platform</div>
                        <div class="text-gray-400 text-sm">Click the button below to visit your new streaming platform</div>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 mt-0.5">2</div>
                    <div>
                        <div class="text-white font-medium">Login to Admin Panel</div>
                        <div class="text-gray-400 text-sm">Use the credentials above to access /admin and configure your platform</div>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 mt-0.5">3</div>
                    <div>
                        <div class="text-white font-medium">Update Security Settings</div>
                        <div class="text-gray-400 text-sm">Change default passwords and configure security settings</div>
                    </div>
                </div>
                <div class="flex items-start space-x-3">
                    <div class="w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-bold flex-shrink-0 mt-0.5">4</div>
                    <div>
                        <div class="text-white font-medium">Add Your Content</div>
                        <div class="text-gray-400 text-sm">Start uploading movies, TV shows, and configure your streaming library</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Optimization -->
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-8">
            <h4 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center">
                ⚡ Performance Optimization
            </h4>
            <p class="text-yellow-700 text-sm mb-4">
                Your platform has been optimized during installation. For maximum performance in production:
            </p>
            <div class="bg-yellow-100 rounded-lg p-4">
                <div class="text-yellow-800 font-mono text-sm">
                    # Run these commands regularly:<br>
                    php artisan optimize:clear-all<br>
                    ./optimize.sh<br>
                    npm run production
                </div>
            </div>
        </div>

        <!-- Support Information -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4 flex items-center">
                💡 Need Help?
            </h4>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-700 rounded-lg p-4">
                    <div class="text-white font-medium mb-2">📚 Documentation</div>
                    <div class="text-gray-400 text-sm">Check the README.md file for detailed setup and configuration instructions.</div>
                </div>
                <div class="bg-gray-700 rounded-lg p-4">
                    <div class="text-white font-medium mb-2">🔧 Troubleshooting</div>
                    <div class="text-gray-400 text-sm">Review the logs in storage/logs/ if you encounter any issues.</div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4">
            <a href="{{ route('index') }}" 
               class="flex-1 py-4 px-6 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-center transition-colors">
                🏠 Visit Your Platform
            </a>
            <a href="{{ url('/admin') }}" 
               class="flex-1 py-4 px-6 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-center transition-colors">
                ⚙️ Admin Panel
            </a>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-gray-400 text-sm">
                Thank you for choosing our streaming platform! 🎬<br>
                <span class="text-xs">Installation completed at {{ now()->format('Y-m-d H:i:s') }}</span>
            </p>
        </div>
    </div>

    <script>
        // Auto-refresh if the installation is still in progress
        document.addEventListener('DOMContentLoaded', function() {
            // Add some confetti effect or celebration animation here if desired
            console.log('🎉 Installation completed successfully!');
        });
    </script>
@endsection
