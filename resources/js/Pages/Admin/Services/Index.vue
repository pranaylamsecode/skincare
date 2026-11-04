<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, CheckCircle2, XCircle } from 'lucide-vue-next';

defineProps({
  services: { type: Array, default: () => [] },
});

const deleteService = (id, title) => {
  if (confirm(`Are you sure you want to delete "${title}"?`)) {
    router.delete(`/admin/services/${id}`);
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Services | Admin" />

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Services Management</h1>
          <p class="text-xs text-neutral-500 mt-1">Add, edit pricing, packages and bullet features displayed on the website</p>
        </div>
        <Link 
          href="/admin/services/create" 
          class="px-4 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs"
        >
          <Plus class="w-4 h-4" />
          <span>Add New Service</span>
        </Link>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div 
          v-for="svc in services" 
          :key="svc.id" 
          class="bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden flex flex-col justify-between"
        >
          <div class="p-5">
            <div class="flex items-start justify-between gap-3 mb-3">
              <div>
                <span 
                  v-if="svc.tag" 
                  class="text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded-full"
                  :class="svc.tag_style === 'tan' ? 'bg-[#d9b48f]/30 text-[#52201f]' : (svc.tag_style === 'dark' ? 'bg-[#7a2e2e] text-white' : 'bg-[#fbeded] text-[#b60a0a]')"
                >
                  {{ svc.tag }}
                </span>
                <h3 class="font-bold text-base text-neutral-900 mt-1.5">{{ svc.title }}</h3>
              </div>
              <div class="text-right">
                <div class="text-lg font-bold text-[#7a2e2e]">₹{{ Math.round(Number(svc.price)).toLocaleString('en-IN') }}</div>
                <div v-if="svc.original_price" class="text-xs text-neutral-400 line-through">₹{{ Math.round(Number(svc.original_price)).toLocaleString('en-IN') }}</div>
              </div>
            </div>

            <p class="text-xs text-neutral-600 mb-4 line-clamp-2">{{ svc.description }}</p>

            <div v-if="svc.features && svc.features.length" class="space-y-1 text-xs text-neutral-500 mb-4 bg-neutral-50 p-3 rounded-xl border border-neutral-100">
              <div v-for="(feat, i) in svc.features.slice(0, 4)" :key="i" class="flex items-center gap-1.5">
                <span class="text-emerald-600 font-bold">✓</span>
                <span class="truncate">{{ feat }}</span>
              </div>
              <div v-if="svc.features.length > 4" class="text-[11px] text-neutral-400 font-medium">
                + {{ svc.features.length - 4 }} more features
              </div>
            </div>

            <div class="flex items-center gap-2 text-xs text-neutral-400">
              <span class="flex items-center gap-1" :class="svc.is_active ? 'text-emerald-600 font-semibold' : 'text-neutral-400'">
                <CheckCircle2 v-if="svc.is_active" class="w-3.5 h-3.5" />
                <XCircle v-else class="w-3.5 h-3.5" />
                <span>{{ svc.is_active ? 'Active on site' : 'Draft / Hidden' }}</span>
              </span>
              <span v-if="svc.is_featured" class="text-xs text-[#b60a0a] font-bold">· Featured Card</span>
            </div>
          </div>

          <div class="p-3 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2">
            <Link 
              :href="`/admin/services/${svc.id}/edit`" 
              class="px-3 py-1.5 bg-white border border-neutral-200 text-xs font-semibold text-neutral-700 hover:text-[#7a2e2e] rounded-lg transition flex items-center gap-1.5"
            >
              <Edit class="w-3.5 h-3.5" />
              <span>Edit</span>
            </Link>
            <button 
              type="button" 
              class="p-1.5 text-neutral-400 hover:text-red-600 transition"
              @click="deleteService(svc.id, svc.title)"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
