<script setup>
import { ref } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Trash2, Edit, Save, HelpCircle, Package, Smartphone, Award, HeartHandshake } from 'lucide-vue-next';

const props = defineProps({
  plans: { type: Array, default: () => [] },
  devices: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  concerns: { type: Array, default: () => [] },
  reviews: { type: Array, default: () => [] },
  faqs: { type: Array, default: () => [] },
});

const activeTab = ref('plans');

// FAQ Form
const faqForm = useForm({
  question: '',
  answer: '',
  sort_order: props.faqs.length + 1,
  is_active: true,
});

const saveFaq = () => {
  faqForm.post('/admin/content/faqs', {
    onSuccess: () => faqForm.reset(),
  });
};

const deleteFaq = (id) => {
  if (confirm('Delete this FAQ?')) {
    router.delete(`/admin/content/faqs/${id}`);
  }
};

// Plan Form
const planForm = useForm({
  title: '',
  tag: '',
  price: '',
  price_subtext: '',
  description: '',
  is_featured: false,
  is_active: true,
});

const savePlan = () => {
  planForm.post('/admin/content/plans', {
    onSuccess: () => planForm.reset(),
  });
};

const deletePlan = (id) => {
  if (confirm('Delete this Plan?')) {
    router.delete(`/admin/content/plans/${id}`);
  }
};

// Product Form
const productForm = useForm({
  step_name: 'CLEANSE',
  name: '',
  category: 'daily',
  description: '',
  image: '',
  is_active: true,
});

const saveProduct = () => {
  productForm.post('/admin/content/products', {
    onSuccess: () => productForm.reset(),
  });
};

const deleteProduct = (id) => {
  if (confirm('Delete this product?')) {
    router.delete(`/admin/content/products/${id}`);
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Site Content | Admin" />

    <div class="space-y-6">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Website Content Management</h1>
        <p class="text-xs text-neutral-500 mt-1">Manage Glow Plans, Devices, Products and Frequently Asked Questions dynamically</p>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex gap-2 border-b border-neutral-200 pb-2 overflow-x-auto">
        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'plans' ? 'bg-[#7a2e2e] text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'plans'"
        >
          <Award class="w-4 h-4" />
          <span>Glow Plans ({{ plans.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'faqs' ? 'bg-[#7a2e2e] text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'faqs'"
        >
          <HelpCircle class="w-4 h-4" />
          <span>FAQs ({{ faqs.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'products' ? 'bg-[#7a2e2e] text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'products'"
        >
          <Package class="w-4 h-4" />
          <span>Products ({{ products.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'devices' ? 'bg-[#7a2e2e] text-white' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'devices'"
        >
          <Smartphone class="w-4 h-4" />
          <span>Devices ({{ devices.length }})</span>
        </button>
      </div>

      <!-- Tab: Plans -->
      <div v-if="activeTab === 'plans'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div 
            v-for="p in plans" 
            :key="p.id" 
            class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs flex flex-col justify-between"
          >
            <div>
              <span v-if="p.tag" class="text-[10px] font-bold uppercase tracking-wider bg-neutral-100 px-2 py-0.5 rounded-full">
                {{ p.tag }}
              </span>
              <h3 class="font-bold text-base text-neutral-900 mt-2">{{ p.title }}</h3>
              <div class="text-xl font-bold text-[#7a2e2e] my-1">
                {{ Number(p.price) > 0 ? '₹' + Math.round(Number(p.price)).toLocaleString('en-IN') : 'Custom' }}
                <small v-if="p.price_subtext" class="text-xs font-normal text-neutral-500">({{ p.price_subtext }})</small>
              </div>
              <p class="text-xs text-neutral-600 mt-2">{{ p.description }}</p>
            </div>
            <div class="pt-4 border-t border-neutral-100 flex justify-end mt-4">
              <button 
                type="button" 
                class="text-neutral-400 hover:text-red-600 text-xs flex items-center gap-1"
                @click="deletePlan(p.id)"
              >
                <Trash2 class="w-3.5 h-3.5" />
                <span>Delete</span>
              </button>
            </div>
          </div>

          <!-- Add Plan Card -->
          <div class="bg-neutral-50 p-5 rounded-2xl border border-dashed border-neutral-300">
            <h3 class="font-bold text-xs uppercase tracking-wider text-neutral-700 mb-3">Add New Glow Plan</h3>
            <form @submit.prevent="savePlan" class="space-y-3">
              <input 
                v-model="planForm.title" 
                type="text" 
                required 
                placeholder="Plan Title (e.g. 5-Visit Bridal Plan)" 
                class="w-full px-3 py-2 text-xs bg-white border border-neutral-200 rounded-xl outline-none"
              />
              <div class="grid grid-cols-2 gap-2">
                <input 
                  v-model="planForm.tag" 
                  type="text" 
                  placeholder="Tag (e.g. Popular)" 
                  class="w-full px-3 py-2 text-xs bg-white border border-neutral-200 rounded-xl outline-none"
                />
                <input 
                  v-model="planForm.price" 
                  type="number" 
                  required 
                  placeholder="Price (₹)" 
                  class="w-full px-3 py-2 text-xs bg-white border border-neutral-200 rounded-xl outline-none"
                />
              </div>
              <input 
                v-model="planForm.price_subtext" 
                type="text" 
                placeholder="Price subtext (e.g. ₹900 per visit)" 
                class="w-full px-3 py-2 text-xs bg-white border border-neutral-200 rounded-xl outline-none"
              />
              <textarea 
                v-model="planForm.description" 
                rows="2" 
                placeholder="Short description of the plan..." 
                class="w-full px-3 py-2 text-xs bg-white border border-neutral-200 rounded-xl outline-none"
              ></textarea>
              <button 
                type="submit" 
                :disabled="planForm.processing"
                class="w-full py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition"
              >
                Save Plan
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Tab: FAQs -->
      <div v-if="activeTab === 'faqs'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 space-y-3">
            <div 
              v-for="faq in faqs" 
              :key="faq.id" 
              class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs flex items-start justify-between gap-4"
            >
              <div>
                <h3 class="font-bold text-xs text-neutral-900">{{ faq.question }}</h3>
                <p class="text-xs text-neutral-600 mt-1 leading-relaxed">{{ faq.answer }}</p>
              </div>
              <button 
                type="button" 
                class="text-neutral-400 hover:text-red-600 p-1"
                @click="deleteFaq(faq.id)"
              >
                <Trash2 class="w-4 h-4" />
              </button>
            </div>
          </div>

          <!-- Add FAQ -->
          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs h-fit space-y-3">
            <h3 class="font-bold text-xs uppercase tracking-wider text-neutral-700">Add New FAQ</h3>
            <form @submit.prevent="saveFaq" class="space-y-3">
              <div>
                <label class="block text-[11px] font-bold text-neutral-500 uppercase mb-1">Question</label>
                <input 
                  v-model="faqForm.question" 
                  type="text" 
                  required 
                  placeholder="e.g. How long does a facial take?" 
                  class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                />
              </div>
              <div>
                <label class="block text-[11px] font-bold text-neutral-500 uppercase mb-1">Answer</label>
                <textarea 
                  v-model="faqForm.answer" 
                  rows="4" 
                  required 
                  placeholder="Detailed answer for customers..." 
                  class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                ></textarea>
              </div>
              <button 
                type="submit" 
                :disabled="faqForm.processing"
                class="w-full py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition"
              >
                Add Question
              </button>
            </form>
          </div>
        </div>
      </div>

      <!-- Tab: Products -->
      <div v-if="activeTab === 'products'" class="space-y-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <div 
            v-for="prod in products" 
            :key="prod.id" 
            class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs flex flex-col justify-between"
          >
            <div>
              <div v-if="prod.image" class="h-24 bg-neutral-50 rounded-xl overflow-hidden mb-2 flex items-center justify-center">
                <img :src="prod.image.startsWith('http') ? prod.image : '/' + prod.image" :alt="prod.name" class="h-full object-contain" />
              </div>
              <small v-if="prod.step_name" class="text-[10px] font-bold text-[#7a2e2e] block">{{ prod.step_name }}</small>
              <h4 class="font-bold text-xs text-neutral-900">{{ prod.name }}</h4>
              <p v-if="prod.description" class="text-[11px] text-neutral-500 mt-1 line-clamp-2">{{ prod.description }}</p>
            </div>
            <div class="pt-2 mt-2 border-t border-neutral-100 flex justify-end">
              <button type="button" class="text-neutral-400 hover:text-red-600 text-xs" @click="deleteProduct(prod.id)">
                <Trash2 class="w-3.5 h-3.5" />
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Tab: Devices -->
      <div v-if="activeTab === 'devices'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div 
            v-for="d in devices" 
            :key="d.id" 
            class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs space-y-3"
          >
            <div class="h-36 bg-neutral-50 rounded-xl overflow-hidden flex items-center justify-center p-2">
              <img v-if="d.image" :src="'/' + d.image" :alt="d.name" class="h-full object-contain" />
            </div>
            <div class="flex items-center justify-between">
              <h3 class="font-bold text-base text-neutral-900">{{ d.name }}</h3>
              <span class="text-[10px] font-bold bg-[#7a2e2e] text-white px-2 py-0.5 rounded-full">{{ d.pill }}</span>
            </div>
            <p class="text-xs text-neutral-600">{{ d.description }}</p>
            <ul v-if="d.features && d.features.length" class="text-[11px] space-y-1 text-neutral-500 pt-2 border-t border-neutral-100">
              <li v-for="(f, i) in d.features" :key="i">✓ {{ f }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
