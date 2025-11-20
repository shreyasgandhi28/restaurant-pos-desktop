<template>
  <div>
    <!-- Navigation -->
    <nav class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
          <div class="flex">
            <!-- Logo -->
            <div class="shrink-0 flex items-center">
              <a :href="getUrl('/dashboard')" class="flex items-center">
                <img src="/images/logo/logo.png" alt="Restaurant Logo" class="h-10 w-auto">
              </a>
            </div>

            <!-- Navigation Links -->
            <div class="hidden space-x-8 sm:-my-px sm:ml-10 sm:flex">
              <a :href="getUrl('/dashboard')" :class="{'border-indigo-500 text-gray-900': $page?.url === '/dashboard', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': $page?.url !== '/dashboard'}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Dashboard
              </a>
              <a :href="getUrl('/pos')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/pos'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/pos')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                POS
              </a>
              <a :href="getUrl('/tables')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/tables'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/tables')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Tables
              </a>
              <a :href="getUrl('/orders')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/orders'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/orders')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                Orders
              </a>
              <template v-if="$page?.props?.auth?.user?.roles && ($page.props.auth.user.roles.includes('admin') || $page.props.auth.user.roles.includes('manager'))">
                <a :href="getUrl('/staff/salary-advances')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/staff/salary-advances'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/staff/salary-advances')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                  Staff Advances
                </a>
              </template>
              <template v-if="$page?.props?.auth?.user?.roles && $page.props.auth.user.roles.includes('admin')">
                <a :href="getUrl('/menu-items')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/menu-items'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/menu-items')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                  Menu
                </a>
                <a :href="getUrl('/categories')" :class="{'border-indigo-500 text-gray-900': $page?.url?.startsWith('/categories'), 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': !$page?.url?.startsWith('/categories')}" class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium">
                  Categories
                </a>
              </template>
            </div>
          </div>

          <!-- Account Dropdown -->
          <div class="hidden sm:flex sm:items-center sm:ml-6">
            <div class="ml-3 relative" v-click-outside="() => showUserMenu = false">
              <button @click="showUserMenu = !showUserMenu" class="flex items-center text-sm text-gray-700 hover:text-gray-900 focus:outline-none">
                <span>{{ $page?.props?.auth?.user?.name || 'User' }}</span>
                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
              </button>

              <div v-show="showUserMenu" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50">
                <a :href="getUrl('/settings')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                  Settings
                </a>
                <form :action="getUrl('/logout')" method="POST" class="w-full">
                  <input type="hidden" name="_token" :value="$page?.props?.csrf_token || ''" />
                  <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                    Logout
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </nav>

    <!-- Page Header -->
    <div v-if="$slots.header" class="bg-white border-b border-gray-200">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <slot name="header" />
      </div>
    </div>

    <!-- Page Content -->
    <main class="py-6 bg-gray-50 min-h-screen">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <slot />
      </div>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();
const showUserMenu = ref(false);

// Safely get the current origin (works on both client and server)
const currentOrigin = computed(() => {
    try {
        if (typeof window !== 'undefined' && window.location) {
            return window.location.origin;
        }
        // Fallback to app.url from props if available
        if (page?.props?.app?.url) {
            return page.props.app.url;
        }
        return '';
    } catch (e) {
        console.error('Error getting origin:', e);
        return page?.props?.app?.url || '';
    }
});

// Generate URL helper
const getUrl = (path) => {
    if (path.startsWith('http')) return path;
    return `${currentOrigin.value}${path.startsWith('/') ? '' : '/'}${path}`;
};

// Handle navigation events
const handleNavigation = (event) => {
    // Allow default navigation
    return true;
};

// Add navigation event listener
onMounted(() => {
    if (typeof window !== 'undefined') {
        window.addEventListener('popstate', handleNavigation);
        // Ensure any pending navigation is cleared
        if (window.history.state && window.history.state._inertia) {
            delete window.history.state._inertia;
        }
    }
});

// Cleanup event listener
onUnmounted(() => {
    if (typeof window !== 'undefined') {
        window.removeEventListener('popstate', handleNavigation);
    }
});

// Force a full page reload if navigation seems stuck
const forceNavigation = (url) => {
    if (typeof window !== 'undefined') {
        window.location.href = url;
    }
};
</script>
