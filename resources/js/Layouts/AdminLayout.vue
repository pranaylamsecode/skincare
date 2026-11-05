<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { 
  LayoutDashboard, 
  CalendarCheck, 
  Sparkles, 
  Users, 
  Clock, 
  Mail, 
  Settings, 
  Sliders, 
  LogOut, 
  Menu, 
  X,
  ExternalLink,
  MapPin 
} from 'lucide-vue-next';

const page = usePage();
const user = computed(() => page.props.auth?.user || { name: 'Admin', role: 'admin' });
const flash = page.props.flash || {};

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const navigation = computed(() => {
  if (user.value.role === 'staff') {
    return [
      { name: 'My Schedule', href: '/admin/dashboard', icon: LayoutDashboard },
      { name: 'My Bookings', href: '/admin/bookings', icon: CalendarCheck },
      { name: 'My Time Slots', href: '/admin/time-slots', icon: Clock },
      { name: 'My Profile & Area Map', href: user.value.specialist_id ? `/admin/specialists/${user.value.specialist_id}/edit` : '/admin/specialists', icon: MapPin },
    ];
  }

  return [
    { name: 'Dashboard', href: '/admin/dashboard', icon: LayoutDashboard },
    { name: 'Bookings', href: '/admin/bookings', icon: CalendarCheck },
    { name: 'Services', href: '/admin/services', icon: Sparkles },
    { name: 'Specialists & Staff', href: '/admin/specialists', icon: Users },
    { name: 'Time Slots', href: '/admin/time-slots', icon: Clock },
    { name: 'Email Templates', href: '/admin/email-templates', icon: Mail },
    { name: 'Site Content', href: '/admin/content', icon: Sliders },
    { name: 'Settings', href: '/admin/settings', icon: Settings },
  ];
});

const isActive = (url) => {
  return window.location.pathname.startsWith(url);
};
</script>

<template>
  <div class="min-h-screen bg-neutral-50 flex flex-col font-sans text-neutral-800">
    <!-- Top Bar for Mobile & Desktop -->
    <header class="bg-white border-b border-neutral-200 sticky top-0 z-30 h-16 px-4 md:px-6 flex items-center justify-between shadow-xs">
      <div class="flex items-center gap-3">
        <button 
          type="button" 
          class="lg:hidden p-2 rounded-lg text-neutral-600 hover:bg-neutral-100"
          @click="toggleSidebar"
        >
          <Menu v-if="!isSidebarOpen" class="w-6 h-6" />
          <X v-else class="w-6 h-6" />
        </button>
        <Link href="/admin/dashboard" class="flex items-center gap-2">
          <span class="text-xl font-bold tracking-tight text-[#7a2e2e]">FMZ GLOW</span>
          <span 
            class="text-xs font-semibold px-2 py-0.5 rounded-full uppercase"
            :class="user.role === 'staff' ? 'bg-amber-100 text-amber-800' : 'bg-[#fbeded] text-[#b60a0a]'"
          >
            {{ user.role === 'staff' ? 'Staff Specialist' : 'Admin' }}
          </span>
        </Link>
      </div>

      <div class="flex items-center gap-3">
        <a 
          href="/" 
          target="_blank" 
          class="hidden sm:inline-flex items-center gap-1.5 text-xs font-semibold text-neutral-600 hover:text-[#b60a0a] px-3 py-1.5 rounded-lg border border-neutral-200 hover:bg-neutral-50 transition"
        >
          <span>View Website</span>
          <ExternalLink class="w-3.5 h-3.5" />
        </a>

        <div class="flex items-center gap-2 pl-3 border-l border-neutral-200">
          <div class="w-8 h-8 rounded-full bg-[#7a2e2e] text-white flex items-center justify-center font-bold text-xs shrink-0">
            {{ user.name ? user.name.charAt(0) : 'A' }}
          </div>
          <div class="hidden md:flex flex-col">
            <span class="text-xs font-semibold text-neutral-800">{{ user.name }}</span>
            <span class="text-[10px] text-neutral-400 capitalize">{{ user.role === 'staff' ? 'Specialist' : 'Administrator' }}</span>
          </div>
          
          <Link 
            href="/admin/logout" 
            method="post" 
            as="button" 
            class="text-neutral-500 hover:text-red-600 p-1.5 rounded-lg hover:bg-neutral-100 ml-1 transition"
            title="Log Out"
          >
            <LogOut class="w-4 h-4" />
          </Link>
        </div>
      </div>
    </header>

    <div class="flex flex-1 relative">
      <!-- Mobile Backdrop Overlay -->
      <div 
        v-if="isSidebarOpen" 
        class="fixed inset-0 bg-black/40 z-20 lg:hidden"
        @click="isSidebarOpen = false"
      />

      <!-- Sidebar -->
      <aside 
        class="fixed inset-y-0 left-0 top-16 z-20 w-64 bg-white border-r border-neutral-200 flex flex-col justify-between transition-transform duration-200 lg:sticky lg:top-16 lg:h-[calc(100vh-4rem)] lg:translate-x-0 overflow-y-auto shrink-0"
        :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
      >
        <nav class="p-3 space-y-1">
          <Link 
            v-for="item in navigation" 
            :key="item.name" 
            :href="item.href"
            class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition"
            :class="isActive(item.href) 
              ? 'bg-[#7a2e2e] text-white shadow-xs font-semibold' 
              : 'text-neutral-700 hover:bg-neutral-100 hover:text-neutral-900'"
            @click="isSidebarOpen = false"
          >
            <component :is="item.icon" class="w-4 h-4 shrink-0" />
            <span>{{ item.name }}</span>
          </Link>
        </nav>

        <div class="p-4 border-t border-neutral-200 text-xs text-neutral-500">
          <div>FMZ Glow v2.0 (Laravel + Vue)</div>
          <div class="mt-0.5 text-neutral-400">At-home Skincare System</div>
        </div>
      </aside>

      <!-- Main Content Area -->
      <main class="flex-1 min-w-0 p-4 md:p-6 lg:p-8 max-w-7xl w-full mx-auto">
        <!-- Flash Success Notification -->
        <div 
          v-if="$page.props.flash?.success" 
          class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center justify-between shadow-xs"
        >
          <span>{{ $page.props.flash.success }}</span>
        </div>

        <!-- Flash Error Notification -->
        <div 
          v-if="$page.props.flash?.error" 
          class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm flex items-center justify-between shadow-xs"
        >
          <span>{{ $page.props.flash.error }}</span>
        </div>

        <slot />
      </main>
    </div>
  </div>
</template>
