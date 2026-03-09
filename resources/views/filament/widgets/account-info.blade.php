<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-5">
            <!-- Header dengan Icon -->
            <div class="flex items-center gap-3 border-b pb-4">
                <div class="p-3 bg-primary-100 rounded-xl">
                    <x-heroicon-o-home class="w-8 h-8 text-primary-600" />
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        Selamat Datang di Sistem Manajemen Peternakan
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Kelola peternakan Anda dengan lebih mudah dan efisien
                    </p>
                </div>
            </div>
            
            <!-- User Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Name Card -->
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 dark:from-blue-900/20 dark:to-blue-800/20 rounded-xl p-4 flex items-center gap-3">
                    <div class="p-2 bg-blue-500 rounded-lg">
                        <x-heroicon-o-user class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-xs text-blue-600 dark:text-blue-400 font-medium">Nama Pengguna</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                    </div>
                </div>
                
                <!-- Email Card -->
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 dark:from-purple-900/20 dark:to-purple-800/20 rounded-xl p-4 flex items-center gap-3">
                    <div class="p-2 bg-purple-500 rounded-lg">
                        <x-heroicon-o-envelope class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-xs text-purple-600 dark:text-purple-400 font-medium">Email</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white truncate">{{ auth()->user()->email }}</p>
                    </div>
                </div>
                
                <!-- Role Card -->
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl p-4 flex items-center gap-3">
                    <div class="p-2 bg-amber-500 rounded-lg">
                        <x-heroicon-o-shield-check class="w-5 h-5 text-white" />
                    </div>
                    <div>
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-medium">Role / Jabatan</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->role ?? 'Administrator' }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Welcome Message -->
            <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                <div class="flex items-start gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                        <x-heroicon-o-sparkles class="w-5 h-5 text-green-600 dark:text-green-400" />
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 dark:text-gray-300">
                            Selamat bekerja, <span class="font-semibold">{{ auth()->user()->name }}</span>! 
                            Semoga harimu menyenangkan dan produktif.
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                            {{ now()->translatedFormat('l, d F Y') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center justify-between pt-3 border-t border-gray-200 dark:border-gray-700">
                <div class="flex gap-2">
                    <x-filament::button 
                        color="gray"
                        icon="heroicon-o-cog-6-tooth"
                        tag="a"
                        href="{{ route('filament.admin.pages.pengaturan') }}"
                        size="sm"
                    >
                        Pengaturan
                    </x-filament::button>
                    
                    <x-filament::button 
                        color="gray"
                        icon="heroicon-o-question-mark-circle"
                        tag="a"
                        href="#"
                        size="sm"
                    >
                        Bantuan
                    </x-filament::button>
                </div>
                
                <form method="POST" action="{{ route('filament.admin.auth.logout') }}">
                    @csrf
                    <x-filament::button 
                        color="danger"
                        icon="heroicon-o-arrow-left-on-rectangle"
                        type="submit"
                        size="sm"
                    >
                        Sign Out
                    </x-filament::button>
                </form>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>