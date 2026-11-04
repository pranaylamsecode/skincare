<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save, Send } from 'lucide-vue-next';

const props = defineProps({
  template: { type: Object, required: true }
});

const form = useForm({
  name: props.template.name,
  subject: props.template.subject,
  content: props.template.content,
  is_active: Boolean(props.template.is_active),
});

const testForm = useForm({
  test_email: 'fmz@fmzau.com.au',
});

const submit = () => {
  form.put(`/admin/email-templates/${props.template.id}`);
};

const sendTestEmail = () => {
  testForm.post(`/admin/email-templates/${props.template.id}/test`);
};

const insertVariable = (varName) => {
  form.content += varName;
};
</script>

<template>
  <AdminLayout>
    <Head :title="`Edit ${template.name} | Admin`" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <div class="flex items-center justify-between">
        <Link 
          href="/admin/email-templates" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-[#7a2e2e] transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Email Templates</span>
        </Link>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Editor (2 cols) -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs">
          <h1 class="text-base font-bold text-neutral-900 mb-1">
            {{ template.name }}
          </h1>
          <p class="text-xs font-mono text-neutral-400 mb-5">Code: {{ template.code }}</p>

          <form @submit.prevent="submit" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Template Name</label>
              <input 
                v-model="form.name" 
                type="text" 
                required 
                class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Email Subject Line</label>
              <input 
                v-model="form.subject" 
                type="text" 
                required 
                class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-semibold text-[#7a2e2e]"
              />
            </div>

            <div>
              <div class="flex items-center justify-between mb-1">
                <label class="block text-xs font-bold text-neutral-700 uppercase">Email Body Content</label>
                <span class="text-[11px] text-neutral-400">Plain text with auto-formatting</span>
              </div>
              <textarea 
                v-model="form.content" 
                rows="14" 
                required 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono leading-relaxed"
              ></textarea>
            </div>

            <div class="flex items-center gap-2 pt-1 select-none">
              <input type="checkbox" v-model="form.is_active" id="tmplActive" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a] cursor-pointer" />
              <label for="tmplActive" class="text-xs font-semibold text-neutral-700 cursor-pointer">Active and will be sent automatically</label>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-neutral-100">
              <Link 
                href="/admin/email-templates" 
                class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl"
              >
                Cancel
              </Link>
              <button 
                type="submit" 
                :disabled="form.processing"
                class="px-5 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white font-bold text-xs rounded-xl flex items-center gap-2 shadow-xs"
              >
                <Save class="w-4 h-4" />
                <span>{{ form.processing ? 'Saving...' : 'Save Template' }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Helpers & Test Email (1 col) -->
        <div class="space-y-5">
          <!-- Variables Quick Inserter -->
          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
            <h3 class="font-bold text-xs uppercase tracking-wider text-neutral-700 mb-2">Available Variables</h3>
            <p class="text-[11px] text-neutral-500 mb-3">Click on any tag to insert it into the message:</p>
            <div class="flex flex-wrap gap-1.5">
              <button 
                v-for="v in template.variables_hint.split(',').map(s => s.trim())" 
                :key="v"
                type="button"
                class="text-[11px] font-mono px-2 py-1 bg-neutral-100 hover:bg-[#fbeded] hover:text-[#7a2e2e] rounded-md transition"
                @click="insertVariable(v)"
              >
                {{ v }}
              </button>
            </div>
          </div>

          <!-- Test Dispatcher -->
          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
            <h3 class="font-bold text-xs uppercase tracking-wider text-neutral-700 mb-1">Test Send</h3>
            <p class="text-[11px] text-neutral-500 mb-3">Send a test copy with sample client details to verify formatting:</p>

            <form @submit.prevent="sendTestEmail" class="space-y-3">
              <div>
                <input 
                  v-model="testForm.test_email" 
                  type="email" 
                  required 
                  placeholder="your-email@example.com" 
                  class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                />
              </div>

              <button 
                type="submit" 
                :disabled="testForm.processing"
                class="w-full py-2 bg-neutral-800 hover:bg-neutral-900 text-white text-xs font-semibold rounded-xl transition flex items-center justify-center gap-1.5 disabled:opacity-50"
              >
                <Send class="w-3.5 h-3.5" />
                <span>{{ testForm.processing ? 'Sending...' : 'Send Test Mail' }}</span>
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
