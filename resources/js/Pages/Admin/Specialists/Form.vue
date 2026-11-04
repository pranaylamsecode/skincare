<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  specialist: { type: Object, default: null }
});

const isEditing = !!props.specialist;

const langInput = ref('');

const form = useForm({
  name: props.specialist?.name || '',
  qualification: props.specialist?.qualification || '',
  location: props.specialist?.location || '',
  pincode: props.specialist?.pincode || '',
  city: props.specialist?.city || 'ahmedabad',
  languages: props.specialist?.languages || ['Gujarati', 'Hindi', 'English'],
  badge: props.specialist?.badge || 'Verified Specialist',
  phone: props.specialist?.phone || '',
  email: props.specialist?.email || '',
  photo: props.specialist?.photo || '',
  is_active: props.specialist?.is_active ?? true,
  sort_order: props.specialist?.sort_order ?? 0,
});

const addLanguage = () => {
  if (langInput.value.trim() && !form.languages.includes(langInput.value.trim())) {
    form.languages.push(langInput.value.trim());
    langInput.value = '';
  }
};

const removeLanguage = (idx) => {
  form.languages.splice(idx, 1);
};

const submit = () => {
  if (isEditing) {
    form.put(`/admin/specialists/${props.specialist.id}`);
  } else {
    form.post('/admin/specialists');
  }
};
</script>

<template>
  <AdminLayout>
    <Head :title="`${isEditing ? 'Edit Specialist' : 'Add Specialist'} | Admin`" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <div class="flex items-center justify-between">
        <Link 
          href="/admin/specialists" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-[#7a2e2e] transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Specialists</span>
        </Link>
      </div>

      <div class="bg-white p-6 md:p-8 rounded-2xl border border-neutral-200 shadow-xs">
        <h1 class="text-xl font-bold text-neutral-900 mb-6 pb-3 border-b border-neutral-100">
          {{ isEditing ? `Edit Specialist: ${specialist.name}` : 'Add New Specialist' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Full Name</label>
              <input 
                v-model="form.name" 
                type="text" 
                required 
                placeholder="e.g. Dhruvi Jyani" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
              <span v-if="form.errors.name" class="text-xs text-red-600 mt-1 block">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Qualification / Background</label>
              <input 
                v-model="form.qualification" 
                type="text" 
                required 
                placeholder="e.g. B.Sc. in Biochemistry" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">City</label>
              <select 
                v-model="form.city" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-semibold"
              >
                <option value="ahmedabad">Ahmedabad</option>
                <option value="gandhinagar">Gandhinagar</option>
                <option value="surat">Surat</option>
                <option value="rajkot">Rajkot</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Location Description</label>
              <input 
                v-model="form.location" 
                type="text" 
                required 
                placeholder="e.g. Paldi & Central Ahmedabad (380007)" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Pincode</label>
              <input 
                v-model="form.pincode" 
                type="text" 
                placeholder="380007" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Badge Title</label>
              <input 
                v-model="form.badge" 
                type="text" 
                placeholder="Verified Specialist or Registered Pharmacist" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Phone Number (Internal)</label>
              <input 
                v-model="form.phone" 
                type="text" 
                placeholder="+91..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Photo Path / URL</label>
              <input 
                v-model="form.photo" 
                type="text" 
                placeholder="images/Dhruvi.png" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>
          </div>

          <!-- Languages Spoken -->
          <div class="border-t border-neutral-100 pt-5">
            <label class="block text-xs font-bold text-neutral-700 uppercase mb-2">Languages Spoken</label>
            <div class="flex gap-2 mb-3">
              <input 
                v-model="langInput" 
                type="text" 
                placeholder="e.g. Gujarati, Hindi, English" 
                class="flex-1 px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                @keyup.enter.prevent="addLanguage"
              />
              <button 
                type="button" 
                class="px-4 py-2 bg-neutral-800 text-white text-xs font-semibold rounded-xl hover:bg-neutral-900 flex items-center gap-1"
                @click="addLanguage"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Add</span>
              </button>
            </div>

            <div class="flex flex-wrap gap-2">
              <span 
                v-for="(lang, idx) in form.languages" 
                :key="idx" 
                class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#fbeded] text-[#7a2e2e] font-semibold text-xs rounded-full"
              >
                <span>{{ lang }}</span>
                <button type="button" @click="removeLanguage(idx)" class="hover:text-red-700 font-bold">&times;</button>
              </span>
            </div>
          </div>

          <div class="border-t border-neutral-100 pt-5 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700 select-none">
              <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a]" />
              <span>Visible on Website</span>
            </label>

            <div class="flex items-center gap-3">
              <Link 
                href="/admin/specialists" 
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
                <span>{{ form.processing ? 'Saving...' : 'Save Specialist' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
