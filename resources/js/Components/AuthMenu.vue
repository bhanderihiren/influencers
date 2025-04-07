<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const mobileMenuOpen = ref(false);

// Configuration object for all header data
const headerConfig = ref({
  logo: {
    src: '/images/logo.png',
    alt: 'Company Logo',
    href: 'home',
    height: 'h-8'
  },
  navigation: [
    { name: 'Home', href: 'home' },
    { name: 'Influencers', href: 'all-influencer' },
  ],
  auth: {
    login: {
      href: 'login',
      text: 'Log in',
      class: 'ml-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
    },
    register: {
      href: 'register',
      text: 'Register',
      class: 'ml-6 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
    }
  },
  styles: {
    activeLink: 'border-indigo-500 text-gray-900',
    inactiveLink: 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700',
    mobileActiveLink: 'bg-indigo-50 border-indigo-500 text-indigo-700',
    mobileInactiveLink: 'border-transparent text-gray-500 hover:bg-gray-50 hover:border-gray-300 hover:text-gray-700'
  }
});

// Methods
const isCurrentRoute = (href) => route().current(href);
</script>

<template>
  <header class="bg-white shadow-sm fixed w-full z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16 items-center">
        <!-- Logo -->
        <div class="flex-shrink-0 flex items-center">
          <Link :href="route(headerConfig.logo.href)">
            <img 
              class="w-auto" 
              :class="headerConfig.logo.height" 
              :src="headerConfig.logo.src" 
              :alt="headerConfig.logo.alt"
            >
          </Link>
        </div>

        <!-- Desktop Navigation -->
        <nav class="hidden md:flex space-x-8">
          <Link 
            v-for="item in headerConfig.navigation"
            :key="item.name"
            :href="route(item.href)"
            class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium"
            :class="isCurrentRoute(item.href) ? headerConfig.styles.activeLink : headerConfig.styles.inactiveLink"
          >
            {{ item.name }}
          </Link>
        </nav>

        <!-- Login/Register Links -->
        <div class="hidden md:ml-4 md:flex md:items-center">
          <Link
            :href="route(headerConfig.auth.register.href)"
            :class="headerConfig.auth.register.class"
          >
            {{ headerConfig.auth.register.text }}
          </Link>
          <Link
            :href="route(headerConfig.auth.login.href)"
            :class="headerConfig.auth.login.class"
          >
            {{ headerConfig.auth.login.text }}
          </Link>
        </div>

        <!-- Mobile menu button -->
        <div class="-mr-2 flex items-center md:hidden">
          <button
            @click="mobileMenuOpen = !mobileMenuOpen"
            type="button"
            class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
          >
            <span class="sr-only">Open main menu</span>
            <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Mobile menu -->
    <div v-show="mobileMenuOpen" class="md:hidden">
      <div class="pt-2 pb-3 space-y-1">
        <Link
          v-for="item in headerConfig.navigation"
          :key="item.name"
          :href="route(item.href)"
          class="block pl-3 pr-4 py-2 border-l-4 text-base font-medium"
          :class="isCurrentRoute(item.href) ? headerConfig.styles.mobileActiveLink : headerConfig.styles.mobileInactiveLink"
        >
          {{ item.name }}
        </Link>
      </div>
      <div class="pt-4 pb-3 border-t border-gray-200">
        <div class="space-y-1">
          <Link
            :href="route(headerConfig.auth.register.href)"
            class="block w-full pl-3 pr-4 py-2 text-left text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
          >
            {{ headerConfig.auth.register.text }}
          </Link>
          <Link
            :href="route(headerConfig.auth.login.href)"
            class="block w-full pl-3 pr-4 py-2 text-left text-base font-medium text-gray-500 hover:text-gray-800 hover:bg-gray-100"
          >
            {{ headerConfig.auth.login.text }}
          </Link>
        </div>
      </div>
    </div>
  </header>
</template>