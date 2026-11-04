<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Mail, Edit, CheckCircle2, Send } from 'lucide-vue-next';

defineProps({
  templates: { type: Array, default: () => [] }
});
</script>

<template>
  <AdminLayout>
    <Head title="Email Templates | Admin" />

    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Email Notification Templates</h1>
        <p class="text-xs text-neutral-500 mt-1">
          Customize transactional emails dispatched to customers and admins. Variables like {name}, {service}, {date} are replaced automatically.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div 
          v-for="tmpl in templates" 
          :key="tmpl.id" 
          class="bg-white rounded-2xl border border-neutral-200 shadow-xs p-5 flex flex-col justify-between"
        >
          <div>
            <div class="flex items-center justify-between mb-2">
              <span class="text-[10px] font-mono uppercase tracking-wider bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded font-bold">
                {{ tmpl.code }}
              </span>
              <span 
                class="text-[10px] font-semibold px-2 py-0.5 rounded-full"
                :class="tmpl.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500'"
              >
                {{ tmpl.is_active ? 'Active' : 'Disabled' }}
              </span>
            </div>

            <h3 class="font-bold text-base text-neutral-900 mb-1">{{ tmpl.name }}</h3>
            <div class="text-xs text-[#7a2e2e] font-semibold mb-3">
              Subject: "{{ tmpl.subject }}"
            </div>

            <div class="text-xs text-neutral-500 bg-neutral-50 p-3 rounded-xl border border-neutral-100 font-mono text-[11px] line-clamp-3 whitespace-pre-line">
              {{ tmpl.content }}
            </div>

            <div class="mt-3 text-[11px] text-neutral-400">
              <b>Variables:</b> {{ tmpl.variables_hint }}
            </div>
          </div>

          <div class="pt-4 mt-4 border-t border-neutral-100 flex items-center justify-end">
            <Link 
              :href="`/admin/email-templates/${tmpl.id}/edit`" 
              class="px-4 py-2 bg-[#7a2e2e] hover:bg-[#52201f] text-white text-xs font-semibold rounded-xl transition flex items-center gap-1.5 shadow-xs"
            >
              <Edit class="w-3.5 h-3.5" />
              <span>Edit Template &amp; Test</span>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
