@extends('layouts.install')
@section('content')
    <div class="max-w-4xl mx-auto w-full py-10">
        <div class="mb-6">
            <a class="inline-block" href="{{route('index')}}">
                <x-ui.logo height="38" class="text-gray-700 dark:text-white"/>
            </a>
        </div>
        <div class="mb-6">
            <h3 class="text-3xl mb-2 font-bold text-white">🚀 Streaming Platform Installation</h3>
            <p class="text-gray-400">Complete installation wizard that will set up everything you need to get started.</p>
        </div>

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Installation Progress -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4">📋 Installation Progress</h4>
            <div class="flex items-center space-x-4">
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">1</div>
                    <span class="ml-2 text-blue-400 font-medium">Requirements Check</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold">2</div>
                    <span class="ml-2 text-gray-400">Configuration</span>
                </div>
                <div class="flex-1 h-1 bg-gray-600 mx-2"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 rounded-full bg-gray-600 flex items-center justify-center text-white font-bold">3</div>
                    <span class="ml-2 text-gray-400">Complete</span>
                </div>
            </div>
        </div>

        <!-- Requirements Sections -->
        @foreach($requirements as $sectionName => $sectionItems)
            <div class="mb-8">
                <div class="mb-4">
                    <h4 class="text-xl mb-2 font-semibold text-white flex items-center">
                        @if($sectionName === 'PHP Extensions')
                            🔧 {{ $sectionName }}
                        @elseif($sectionName === 'Directory Permissions')
                            📁 {{ $sectionName }}
                        @elseif($sectionName === 'Server Requirements')
                            🖥️ {{ $sectionName }}
                        @else
                            ⭐ {{ $sectionName }}
                        @endif
                    </h4>
                    <p class="text-sm text-gray-400">
                        @if($sectionName === 'PHP Extensions')
                            Required PHP extensions for the platform to function properly.
                        @elseif($sectionName === 'Directory Permissions')
                            Directory write permissions needed for file operations.
                        @elseif($sectionName === 'Server Requirements')
                            Server software and tools required for installation.
                        @else
                            Optional features that enhance platform functionality.
                        @endif
                    </p>
                </div>
                
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach ($sectionItems as $label => $satisfied)
                        <div class="{{ $satisfied ? 'bg-green-100 text-green-800 border-green-200' : ($sectionName === 'Optional Features' ? 'bg-yellow-100 text-yellow-800 border-yellow-200' : 'bg-red-100 text-red-800 border-red-200') }} rounded-lg p-4 border">
                            <div class="text-center">
                                <div class="mb-2">
                                    @if($satisfied)
                                        <div class="w-6 h-6 mx-auto bg-green-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @elseif($sectionName === 'Optional Features')
                                        <div class="w-6 h-6 mx-auto bg-yellow-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-6 h-6 mx-auto bg-red-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <h5 class="text-sm font-medium">{{ $label }}</h5>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Installation Summary -->
        <div class="bg-gray-800 rounded-lg p-6 mb-8">
            <h4 class="text-xl font-semibold text-white mb-4">📊 Installation Summary</h4>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $totalReqs = collect($requirements)->except('Optional Features')->flatten()->count();
                    $satisfiedReqs = collect($requirements)->except('Optional Features')->flatten()->filter()->count();
                    $optionalReqs = collect($requirements['Optional Features'] ?? [])->filter()->count();
                    $totalOptional = collect($requirements['Optional Features'] ?? [])->count();
                @endphp
                
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">{{ $satisfiedReqs }}/{{ $totalReqs }}</div>
                    <div class="text-sm text-gray-400">Critical Requirements</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-white">{{ $optionalReqs }}/{{ $totalOptional }}</div>
                    <div class="text-sm text-gray-400">Optional Features</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold {{ $satisfied ? 'text-green-400' : 'text-red-400' }}">
                        {{ $satisfied ? '✅' : '❌' }}
                    </div>
                    <div class="text-sm text-gray-400">Ready to Install</div>
                </div>
                <div class="text-center">
                    <div class="text-2xl font-bold text-blue-400">🚀</div>
                    <div class="text-sm text-gray-400">Next: Configuration</div>
                </div>
            </div>
        </div>

        @if(!$satisfied)
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded mb-6">
                <h5 class="font-semibold mb-2">⚠️ Requirements Not Met</h5>
                <p class="text-sm">Please resolve the failed requirements above before proceeding with the installation. Contact your system administrator if you need help setting up the missing requirements.</p>
            </div>
        @endif

        <div class="w-full">
            <a href="{{ $satisfied ? route('install.config') : '#' }}" 
               class="py-4 px-6 rounded-lg {{ $satisfied ? 'bg-blue-600 hover:bg-blue-700' : 'bg-gray-500 cursor-not-allowed' }} flex justify-center items-center font-medium text-white w-full transition-colors"
               {{ $satisfied ? '' : 'onclick="return false;"' }}>
                @if($satisfied)
                    🎯 Continue to Configuration
                @else
                    ⏳ Resolve Requirements First
                @endif
            </a>
        </div>

        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
                Need help? Check our <a href="#" class="text-blue-400 hover:text-blue-300">installation documentation</a> or 
                <a href="#" class="text-blue-400 hover:text-blue-300">contact support</a>.
            </p>
        </div>
    </div>
@endsection
