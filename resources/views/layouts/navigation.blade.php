<nav class="sticky top-0 z-50 glass light:glass-light">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-primary-500 to-accent-500 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2L2 19h20L12 2zm0 4l7 13H5l7-13z"/>
                    </svg>
                </div>
                <span class="text-xl font-bold"><span class="text-gradient">Ani</span><span class="text-white light:text-dark-900">Verse</span></span>
            </a>

            {{-- Desktop Navigation --}}
            <div class="hidden md:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('home') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Home</a>
                <a href="{{ route('anime.trending') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('anime.trending') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Trending</a>
                <a href="{{ route('anime.popular') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('anime.popular') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Popular</a>
                <a href="{{ route('anime.top-rated') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('anime.top-rated') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Top Rated</a>
                <a href="{{ route('anime.seasonal') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('anime.seasonal') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Seasonal</a>
                <a href="{{ route('genres.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('genres.*') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Genres</a>
                <a href="{{ route('anikoto.schedule') }}" class="px-3 py-2 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('anikoto.schedule') ? 'text-primary-400 bg-primary-500/10' : 'text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100' }}">Schedule (Anikoto)</a>
            </div>

            {{-- Right Side: Search + Theme Toggle --}}
            <div class="flex items-center gap-3">
                {{-- Search --}}
                <div class="relative hidden sm:block">
                    <input
                        type="text"
                        id="search-autocomplete"
                        placeholder="Search anime..."
                        class="w-48 lg:w-64 pl-9 pr-4 py-2 text-sm rounded-xl bg-dark-800/60 light:bg-dark-100 border border-dark-700 light:border-dark-200 text-white light:text-dark-900 placeholder-dark-500 light:placeholder-dark-400 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 transition-all"
                        autocomplete="off"
                    >
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-dark-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <div id="autocomplete-dropdown" class="hidden absolute top-full mt-2 w-80 right-0 rounded-xl bg-dark-800 light:bg-white border border-dark-700 light:border-dark-200 shadow-2xl overflow-hidden z-50 max-h-96 overflow-y-auto"></div>
                </div>

                {{-- Theme Toggle --}}
                <button onclick="toggleTheme()" class="p-2 rounded-lg hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors" aria-label="Toggle theme">
                    <svg id="sun-icon" class="w-5 h-5 text-amber-400 hidden" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.25a.75.75 0 01.75.75v2.25a.75.75 0 01-1.5 0V3a.75.75 0 01.75-.75zM7.5 12a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0zM18.894 6.166a.75.75 0 00-1.06-1.06l-1.591 1.59a.75.75 0 101.06 1.061l1.591-1.59zM21.75 12a.75.75 0 01-.75.75h-2.25a.75.75 0 010-1.5H21a.75.75 0 01.75.75zM17.834 18.894a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 10-1.061 1.06l1.59 1.591zM12 18a.75.75 0 01.75.75V21a.75.75 0 01-1.5 0v-2.25A.75.75 0 0112 18zM7.758 17.303a.75.75 0 00-1.061-1.06l-1.591 1.59a.75.75 0 001.06 1.061l1.591-1.59zM6 12a.75.75 0 01-.75.75H3a.75.75 0 010-1.5h2.25A.75.75 0 016 12zM6.697 7.757a.75.75 0 001.06-1.06l-1.59-1.591a.75.75 0 00-1.061 1.06l1.59 1.591z"/>
                    </svg>
                    <svg id="moon-icon" class="w-5 h-5 text-dark-300" fill="currentColor" viewBox="0 0 24 24">
                        <path fill-rule="evenodd" d="M9.528 1.718a.75.75 0 01.162.819A8.97 8.97 0 009 6a9 9 0 009 9 8.97 8.97 0 003.463-.69.75.75 0 01.981.98 10.503 10.503 0 01-9.694 6.46c-5.799 0-10.5-4.701-10.5-10.5 0-4.368 2.667-8.112 6.46-9.694a.75.75 0 01.818.162z" clip-rule="evenodd"/>
                    </svg>
                </button>

                {{-- Mobile Menu Button --}}
                <button onclick="toggleMobileMenu()" class="md:hidden p-2 rounded-lg hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors" aria-label="Toggle menu">
                    <svg class="w-5 h-5 text-dark-300 light:text-dark-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile Menu --}}
        <div id="mobile-menu" class="hidden md:hidden pb-4 border-t border-dark-800/50 light:border-dark-200 mt-2 pt-4">
            <div class="flex flex-col gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Home</a>
                <a href="{{ route('anime.trending') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Trending</a>
                <a href="{{ route('anime.popular') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Popular</a>
                <a href="{{ route('anime.top-rated') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Top Rated</a>
                <a href="{{ route('anime.seasonal') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Seasonal</a>
                <a href="{{ route('genres.index') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Genres</a>
                <a href="{{ route('search') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Search</a>
                <a href="{{ route('anikoto.schedule') }}" class="px-3 py-2 text-sm font-medium rounded-lg text-dark-300 light:text-dark-600 hover:text-white light:hover:text-dark-900 hover:bg-dark-800/50 light:hover:bg-dark-100 transition-colors">Schedule (Anikoto)</a>
            </div>
            {{-- Mobile Search --}}
            <div class="mt-3 px-3 sm:hidden">
                <form action="{{ route('search') }}" method="GET">
                    <input type="text" name="query" placeholder="Search anime..." class="w-full pl-4 pr-4 py-2 text-sm rounded-xl bg-dark-800/60 light:bg-dark-100 border border-dark-700 light:border-dark-200 text-white light:text-dark-900 placeholder-dark-500 focus:outline-none focus:ring-2 focus:ring-primary-500/50 transition-all">
                </form>
            </div>
        </div>
    </div>
</nav>
