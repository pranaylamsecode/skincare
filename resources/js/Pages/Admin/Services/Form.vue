<script setup>
import { ref } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save, Plus, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  service: { type: Object, default: null }
});

const isEditing = !!props.service;

const featureInput = ref('');

const form = useForm({
  title: props.service?.title || '',
  tag: props.service?.tag || '',
  tag_style: props.service?.tag_style || 'normal',
  description: props.service?.description || '',
  features: props.service?.features || [],
  price: props.service?.price || '',
  original_price: props.service?.original_price || '',
  launch_label: props.service?.launch_label || '',
  image: props.service?.image || '',
  image_file: null,
  is_featured: props.service?.is_featured ?? false,
  is_active: props.service?.is_active ?? true,
  sort_order: props.service?.sort_order ?? 0,
});

const addFeature = () => {
  if (featureInput.value.trim()) {
    form.features.push(featureInput.value.trim());
    featureInput.value = '';
  }
};

const removeFeature = (index) => {
  form.features.splice(index, 1);
};

const submit = () => {
  if (isEditing) {
    form.post(`/admin/services/${props.service.id}`, {
      _method: 'PUT',
    });
  } else {
    form.post('/admin/services');
  }
};
</script>

<template>
  <AdminLayout>
    <Head :title="`${isEditing ? 'Edit Service' : 'New Service'} | Admin`" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <div class="flex items-center justify-between">
        <Link 
          href="/admin/services" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-[#7a2e2e] transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Services</span>
        </Link>
      </div>

      <div class="bg-white p-6 md:p-8 rounded-2xl border border-neutral-200 shadow-xs">
        <h1 class="text-xl font-bold text-neutral-900 mb-6 pb-3 border-b border-neutral-100">
          {{ isEditing ? `Edit Service: ${service.title}` : 'Create New Service' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Service Title</label>
              <input 
                v-model="form.title" 
                type="text" 
                required 
                placeholder="e.g. FMZ Galvanic Glow Facial" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
              <span v-if="form.errors.title" class="text-xs text-red-600 mt-1 block">{{ form.errors.title }}</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Badge / Tag Text</label>
              <input 
                v-model="form.tag" 
                type="text" 
                placeholder="e.g. Signature · Launch offer" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Tag Style</label>
              <select 
                v-model="form.tag_style" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              >
                <option value="normal">Normal (Blush Pink)</option>
                <option value="tan">Tan (Warm Gold)</option>
                <option value="dark">Dark (Deep Maroon)</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Launch Label</label>
              <input 
                v-model="form.launch_label" 
                type="text" 
                placeholder="e.g. launch price" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Selling Price (₹)</label>
              <input 
                v-model="form.price" 
                type="number" 
                required 
                placeholder="999" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-bold"
              />
              <span v-if="form.errors.price" class="text-xs text-red-600 mt-1 block">{{ form.errors.price }}</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Original / Strikethrough Price (₹)</label>
              <input 
                v-model="form.original_price" 
                type="number" 
                placeholder="1199" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Service Description</label>
              <textarea 
                v-model="form.description" 
                rows="3" 
                placeholder="A premium facial experience using the Nu Skin Galvanic Spa device..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              ></textarea>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Image Path / URL</label>
              <input 
                v-model="form.image" 
                type="text" 
                placeholder="images/fmz-glow-facial.png or https://..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>
          </div>

          <!-- Bullet Points Features -->
          <div class="border-t border-neutral-100 pt-5">
            <label class="block text-xs font-bold text-neutral-700 uppercase mb-2">Bullet Features List</label>
            <div class="flex gap-2 mb-3">
              <input 
                v-model="featureInput" 
                type="text" 
                placeholder="Add a treatment step or feature..." 
                class="flex-1 px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                @keyup.enter.prevent="addFeature"
              />
              <button 
                type="button" 
                class="px-4 py-2 bg-neutral-800 text-white text-xs font-semibold rounded-xl hover:bg-neutral-900 flex items-center gap-1"
                @click="addFeature"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Add</span>
              </button>
            </div>

            <div class="space-y-1.5">
              <div 
                v-for="(feat, idx) in form.features" 
                :key="idx" 
                class="flex items-center justify-between p-2.5 bg-neutral-50 rounded-lg text-xs border border-neutral-100"
              >
                <span>{{ feat }}</span>
                <button 
                  type="button" 
                  class="text-neutral-400 hover:text-red-600 transition"
                  @click="removeFeature(idx)"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>

          <!-- Toggles -->
          <div class="border-t border-neutral-100 pt-5 flex items-center gap-6">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700 select-none">
              <input type="checkbox" v-model="form.is_featured" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a]" />
              <span>Highlight as Featured Card</span>
            </label>

            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700 select-none">
              <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a]" />
              <span>Visible on Website</span>
            </label>
          </div>

          <div class="flex items-center justify-end gap-3 pt-4 border-t border-neutral-100">
            <Link 
              href="/admin/services" 
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
              <span>{{ form.processing ? 'Saving...' : 'Save Service' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
