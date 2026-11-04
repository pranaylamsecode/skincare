<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, MapPin, CheckCircle2 } from 'lucide-vue-next';

defineProps({
  specialists: { type: Array, default: () => [] }
});

const deleteSpecialist = (id, name) => {
  if (confirm(`Are you sure you want to remove specialist "${name}"?`)) {
    router.delete(`/admin/specialists/${id}`);
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Specialists | Admin" />

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Certified Specialists</h1>
          <p class="text-xs text-neutral-500 mt-1">Manage team members, qualifications, cities and locations</p>
        </div>
        <Link 
          href="/admin/specialists/create" 
          class="px-4 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs"
        >
          <Plus class="w-4 h-4" />
          <span>Add Specialist</span>
        </Link>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-5">
        <div 
          v-for="spec in specialists" 
          :key="spec.id" 
          class="bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden flex flex-col justify-between"
        >
          <div>
            <div class="relative h-48 bg-neutral-100 overflow-hidden">
              <img 
                :src="spec.photo ? (spec.photo.startsWith('http') ? spec.photo : '/' + spec.photo) : '/images/Dharvi.jpg'" 
                :alt="spec.name"
                class="w-full h-full object-cover object-top"
              />
              <span class="absolute bottom-2 left-2 px-2 py-0.5 bg-white/95 backdrop-blur-xs text-[#7a2e2e] font-bold text-[10px] rounded-full shadow-xs">
                ✓ {{ spec.badge }}
              </span>
            </div>

            <div class="p-4 space-y-2">
              <div class="flex items-center justify-between">
                <h3 class="font-bold text-sm text-neutral-900">{{ spec.name }}</h3>
                <span class="text-[10px] font-bold uppercase tracking-wider bg-neutral-100 text-neutral-600 px-2 py-0.5 rounded-full">
                  {{ spec.city }}
                </span>
              </div>

              <div class="text-xs font-semibold text-[#b60a0a]">{{ spec.qualification }}</div>

              <div class="text-[11px] text-neutral-500 flex items-start gap-1">
                <MapPin class="w-3.5 h-3.5 text-neutral-400 shrink-0 mt-0.5" />
                <span>{{ spec.location }}</span>
              </div>

              <div v-if="spec.languages && spec.languages.length" class="flex flex-wrap gap-1 pt-1">
                <span 
                  v-for="(lang, i) in spec.languages" 
                  :key="i"
                  class="text-[10px] bg-[#fbeded] text-[#7a2e2e] font-semibold px-2 py-0.5 rounded-full"
                >
                  {{ lang }}
                </span>
              </div>
            </div>
          </div>

          <div class="p-3 bg-neutral-50 border-t border-neutral-100 flex items-center justify-end gap-2">
            <Link 
              :href="`/admin/specialists/${spec.id}/edit`" 
              class="px-3 py-1.5 bg-white border border-neutral-200 text-xs font-semibold text-neutral-700 hover:text-[#7a2e2e] rounded-lg transition flex items-center gap-1.5"
            >
              <Edit class="w-3.5 h-3.5" />
              <span>Edit</span>
            </Link>
            <button 
              type="button" 
              class="p-1.5 text-neutral-400 hover:text-red-600 transition"
              @click="deleteSpecialist(spec.id, spec.name)"
            >
              <Trash2 class="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
