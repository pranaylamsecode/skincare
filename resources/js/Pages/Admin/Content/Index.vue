<script setup>
import { ref, computed, onMounted } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Plus, 
  Trash2, 
  Edit, 
  Save, 
  HelpCircle, 
  Package, 
  Smartphone, 
  Award, 
  Upload, 
  X, 
  Search, 
  Check, 
  Image as ImageIcon,
  CheckCircle2,
  XCircle,
  Eye,
  EyeOff
} from 'lucide-vue-next';

const props = defineProps({
  initialTab: { type: String, default: 'products' },
  plans: { type: Array, default: () => [] },
  devices: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  concerns: { type: Array, default: () => [] },
  reviews: { type: Array, default: () => [] },
  faqs: { type: Array, default: () => [] },
});

const activeTab = ref(props.initialTab || 'products');

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab && ['plans', 'faqs', 'products', 'devices'].includes(tab)) {
    activeTab.value = tab;
  }
});

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

// ==================== PRODUCT MANAGEMENT ====================
const isProductModalOpen = ref(false);
const editingProduct = ref(null);
const productSearch = ref('');
const productCategoryFilter = ref('all');
const imagePreview = ref(null);
const fileInputRef = ref(null);

const standardSteps = ['CLEANSE', 'TONE', 'TREAT', 'HYDRATE', 'PROTECT', 'SUPPLEMENT'];

const productForm = useForm({
  step_name: 'CLEANSE',
  name: '',
  category: 'daily',
  description: '',
  image: '',
  image_file: null,
  sort_order: 0,
  is_active: true,
});

const openAddProductModal = () => {
  editingProduct.value = null;
  imagePreview.value = null;
  productForm.reset();
  productForm.step_name = 'CLEANSE';
  productForm.category = 'daily';
  productForm.sort_order = props.products.length + 1;
  productForm.is_active = true;
  isProductModalOpen.value = true;
};

const openEditProductModal = (prod) => {
  editingProduct.value = prod;
  imagePreview.value = prod.image ? (prod.image.startsWith('http') ? prod.image : '/' + prod.image) : null;
  productForm.step_name = prod.step_name || '';
  productForm.name = prod.name || '';
  productForm.category = prod.category || 'daily';
  productForm.description = prod.description || '';
  productForm.image = prod.image || '';
  productForm.image_file = null;
  productForm.sort_order = prod.sort_order ?? 0;
  productForm.is_active = Boolean(prod.is_active);
  isProductModalOpen.value = true;
};

const closeProductModal = () => {
  isProductModalOpen.value = false;
  editingProduct.value = null;
  imagePreview.value = null;
  productForm.reset();
};

const onImageFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    productForm.image_file = file;
    const reader = new FileReader();
    reader.onload = (uploadEvent) => {
      imagePreview.value = uploadEvent.target.result;
    };
    reader.readAsDataURL(file);
  }
};

const removeSelectedImage = () => {
  productForm.image_file = null;
  productForm.image = '';
  imagePreview.value = null;
  if (fileInputRef.value) {
    fileInputRef.value.value = '';
  }
};

const saveProduct = () => {
  const endpoint = editingProduct.value 
    ? `/admin/content/products/${editingProduct.value.id}` 
    : '/admin/content/products';

  productForm.post(endpoint, {
    forceFormData: true,
    onSuccess: () => {
      closeProductModal();
    },
  });
};

const deleteProduct = (id) => {
  if (confirm('Are you sure you want to delete this product?')) {
    router.delete(`/admin/content/products/${id}`);
  }
};

const toggleProductActive = (prod) => {
  router.post(`/admin/content/products/${prod.id}`, {
    ...prod,
    is_active: !prod.is_active,
  });
};

// Filtered products list
const filteredProducts = computed(() => {
  return props.products.filter((p) => {
    const matchesCategory = productCategoryFilter.value === 'all' || p.category === productCategoryFilter.value;
    const searchLower = productSearch.value.toLowerCase().trim();
    const matchesSearch = !searchLower || 
      (p.name && p.name.toLowerCase().includes(searchLower)) ||
      (p.step_name && p.step_name.toLowerCase().includes(searchLower)) ||
      (p.description && p.description.toLowerCase().includes(searchLower));
    return matchesCategory && matchesSearch;
  });
});

const dailyCount = computed(() => props.products.filter(p => p.category === 'daily').length);
const wellnessCount = computed(() => props.products.filter(p => p.category === 'wellness').length);
</script>

<template>
  <AdminLayout>
    <Head title="Site Content | Admin" />

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Website Content Management</h1>
          <p class="text-xs text-neutral-500 mt-1">Manage Glow Plans, Devices, Skincare Products & FAQs dynamically</p>
        </div>

        <button 
          v-if="activeTab === 'products'"
          type="button"
          class="inline-flex items-center gap-2 px-4 py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition shadow-xs"
          @click="openAddProductModal"
        >
          <Plus class="w-4 h-4" />
          <span>Add New Product</span>
        </button>
      </div>

      <!-- Navigation Tabs -->
      <div class="flex gap-2 border-b border-neutral-200 pb-2 overflow-x-auto">
        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'products' ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'products'"
        >
          <Package class="w-4 h-4" />
          <span>Products ({{ products.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'plans' ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'plans'"
        >
          <Award class="w-4 h-4" />
          <span>Glow Plans ({{ plans.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'faqs' ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'faqs'"
        >
          <HelpCircle class="w-4 h-4" />
          <span>FAQs ({{ faqs.length }})</span>
        </button>

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'devices' ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'devices'"
        >
          <Smartphone class="w-4 h-4" />
          <span>Devices ({{ devices.length }})</span>
        </button>
      </div>

      <!-- ==================== TAB: PRODUCTS ==================== -->
      <div v-if="activeTab === 'products'" class="space-y-6">
        <!-- Filter & Search Bar -->
        <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs flex flex-col md:flex-row items-center justify-between gap-4">
          <!-- Category Chips -->
          <div class="flex items-center gap-2 w-full md:w-auto">
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition"
              :class="productCategoryFilter === 'all' ? 'bg-neutral-900 text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'"
              @click="productCategoryFilter = 'all'"
            >
              All ({{ products.length }})
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition"
              :class="productCategoryFilter === 'daily' ? 'bg-[#7a2e2e] text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'"
              @click="productCategoryFilter = 'daily'"
            >
              Daily Skincare ({{ dailyCount }})
            </button>
            <button
              type="button"
              class="px-3 py-1.5 rounded-lg text-xs font-bold transition"
              :class="productCategoryFilter === 'wellness' ? 'bg-[#b60a0a] text-white' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'"
              @click="productCategoryFilter = 'wellness'"
            >
              Wellness from Within ({{ wellnessCount }})
            </button>
          </div>

          <!-- Search Input -->
          <div class="relative w-full md:w-64">
            <Search class="w-4 h-4 text-neutral-400 absolute left-3 top-2.5" />
            <input 
              v-model="productSearch" 
              type="text" 
              placeholder="Search products..." 
              class="w-full pl-9 pr-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e]"
            />
          </div>
        </div>

        <!-- Products Grid -->
        <div v-if="filteredProducts.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
          <div 
            v-for="prod in filteredProducts" 
            :key="prod.id" 
            class="bg-white rounded-2xl border border-neutral-200 shadow-xs flex flex-col justify-between overflow-hidden hover:shadow-md transition duration-200"
          >
            <!-- Product Card Header & Image -->
            <div>
              <div class="relative h-40 bg-neutral-50 border-b border-neutral-100 flex items-center justify-center p-3 overflow-hidden group">
                <img 
                  v-if="prod.image" 
                  :src="prod.image.startsWith('http') ? prod.image : '/' + prod.image" 
                  :alt="prod.name" 
                  class="h-full w-full object-contain group-hover:scale-105 transition duration-300" 
                  onerror="this.style.display='none'"
                />
                <div v-else class="flex flex-col items-center justify-center text-neutral-300">
                  <ImageIcon class="w-10 h-10 mb-1" />
                  <span class="text-[10px] font-medium text-neutral-400">No Image</span>
                </div>

                <!-- Category Tag -->
                <span 
                  class="absolute top-2 left-2 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase tracking-wider"
                  :class="prod.category === 'daily' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-rose-100 text-rose-800 border border-rose-200'"
                >
                  {{ prod.category === 'daily' ? 'Daily' : 'Wellness' }}
                </span>

                <!-- Active Toggle Pill -->
                <button 
                  type="button"
                  class="absolute top-2 right-2 text-[10px] font-semibold px-2 py-0.5 rounded-full flex items-center gap-1 transition"
                  :class="prod.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-200 text-neutral-600'"
                  :title="prod.is_active ? 'Visible on website' : 'Hidden on website'"
                  @click="toggleProductActive(prod)"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="prod.is_active ? 'bg-emerald-500' : 'bg-neutral-400'"></span>
                  <span>{{ prod.is_active ? 'Active' : 'Draft' }}</span>
                </button>
              </div>

              <!-- Product Details -->
              <div class="p-4 space-y-1.5">
                <span v-if="prod.step_name" class="text-[10px] font-bold text-[#7a2e2e] uppercase tracking-wider block">
                  {{ prod.step_name }}
                </span>
                <h4 class="font-bold text-xs text-neutral-900 leading-snug line-clamp-2">
                  {{ prod.name }}
                </h4>
                <p v-if="prod.description" class="text-[11px] text-neutral-500 line-clamp-3 leading-relaxed pt-1">
                  {{ prod.description }}
                </p>
              </div>
            </div>

            <!-- Card Actions -->
            <div class="px-4 py-2.5 bg-neutral-50/70 border-t border-neutral-100 flex items-center justify-between">
              <span class="text-[10px] text-neutral-400 font-mono">Order: {{ prod.sort_order ?? 0 }}</span>
              <div class="flex items-center gap-1">
                <button 
                  type="button" 
                  class="p-1.5 text-neutral-500 hover:text-[#7a2e2e] hover:bg-neutral-100 rounded-lg transition"
                  title="Edit Product"
                  @click="openEditProductModal(prod)"
                >
                  <Edit class="w-3.5 h-3.5" />
                </button>
                <button 
                  type="button" 
                  class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-neutral-100 rounded-lg transition" 
                  title="Delete Product"
                  @click="deleteProduct(prod.id)"
                >
                  <Trash2 class="w-3.5 h-3.5" />
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="bg-white rounded-2xl border border-dashed border-neutral-300 p-12 text-center">
          <Package class="w-12 h-12 mx-auto text-neutral-300 mb-3" />
          <h3 class="font-bold text-sm text-neutral-700">No products found</h3>
          <p class="text-xs text-neutral-500 mt-1 max-w-sm mx-auto">
            {{ productSearch ? 'No products match your current search query.' : 'Get started by adding your first skincare or wellness product.' }}
          </p>
          <button 
            type="button" 
            class="mt-4 inline-flex items-center gap-1.5 px-4 py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition"
            @click="openAddProductModal"
          >
            <Plus class="w-4 h-4" />
            <span>Add New Product</span>
          </button>
        </div>
      </div>

      <!-- ==================== TAB: GLOW PLANS ==================== -->
      <div v-if="activeTab === 'plans'" class="space-y-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div 
            v-for="p in plans" 
            :key="p.id" 
            class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs flex flex-col justify-between"
          >
            <div>
              <div class="flex items-center justify-between mb-2">
                <span v-if="p.tag" class="text-[10px] font-bold bg-neutral-100 text-neutral-700 px-2 py-0.5 rounded-full uppercase tracking-wider">
                  {{ p.tag }}
                </span>
                <span v-if="p.is_featured" class="text-[10px] font-bold bg-[#7a2e2e] text-white px-2 py-0.5 rounded-full">Featured</span>
              </div>
              <h3 class="font-bold text-base text-neutral-900">{{ p.title }}</h3>
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

      <!-- ==================== TAB: FAQS ==================== -->
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

      <!-- ==================== TAB: DEVICES ==================== -->
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

    <!-- ==================== ADD / EDIT PRODUCT MODAL ==================== -->
    <div 
      v-if="isProductModalOpen" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="closeProductModal"
    >
      <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl border border-neutral-200 overflow-hidden my-8 transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/50">
          <div>
            <h3 class="font-bold text-base text-neutral-900">
              {{ editingProduct ? 'Edit Product' : 'Add New Product' }}
            </h3>
            <p class="text-xs text-neutral-500 mt-0.5">
              {{ editingProduct ? 'Update product details and image' : 'Add a product to your website catalogue' }}
            </p>
          </div>
          <button 
            type="button" 
            class="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100 transition"
            @click="closeProductModal"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Form -->
        <form @submit.prevent="saveProduct" class="p-6 space-y-4">
          <!-- Category Picker -->
          <div>
            <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
              Category <span class="text-red-500">*</span>
            </label>
            <div class="grid grid-cols-2 gap-2">
              <label 
                class="flex items-center gap-2 p-3 rounded-xl border text-xs font-semibold cursor-pointer transition"
                :class="productForm.category === 'daily' ? 'border-[#7a2e2e] bg-[#7a2e2e]/5 text-[#7a2e2e]' : 'border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50'"
              >
                <input type="radio" v-model="productForm.category" value="daily" class="sr-only" />
                <div class="w-4 h-4 rounded-full border flex items-center justify-center" :class="productForm.category === 'daily' ? 'border-[#7a2e2e] bg-[#7a2e2e]' : 'border-neutral-300'">
                  <div v-if="productForm.category === 'daily'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
                <span>Daily Skincare</span>
              </label>

              <label 
                class="flex items-center gap-2 p-3 rounded-xl border text-xs font-semibold cursor-pointer transition"
                :class="productForm.category === 'wellness' ? 'border-[#b60a0a] bg-[#b60a0a]/5 text-[#b60a0a]' : 'border-neutral-200 bg-white text-neutral-700 hover:bg-neutral-50'"
              >
                <input type="radio" v-model="productForm.category" value="wellness" class="sr-only" />
                <div class="w-4 h-4 rounded-full border flex items-center justify-center" :class="productForm.category === 'wellness' ? 'border-[#b60a0a] bg-[#b60a0a]' : 'border-neutral-300'">
                  <div v-if="productForm.category === 'wellness'" class="w-1.5 h-1.5 rounded-full bg-white"></div>
                </div>
                <span>Wellness from Within</span>
              </label>
            </div>
          </div>

          <!-- Product Name -->
          <div>
            <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
              Product Name <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="productForm.name" 
              type="text" 
              required 
              placeholder="e.g. ageLOC LumiSpa Activating Cleanser" 
              class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
            />
          </div>

          <!-- Routine Step (for Daily Skincare) -->
          <div>
            <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
              Routine Step (e.g. Cleanse, Tone, Treat)
            </label>
            <input 
              v-model="productForm.step_name" 
              type="text" 
              placeholder="e.g. CLEANSE, TONE, or MORNING BOOST" 
              class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
            />
            <!-- Quick Chips -->
            <div class="flex flex-wrap gap-1.5 mt-2">
              <button 
                v-for="step in standardSteps" 
                :key="step" 
                type="button" 
                class="px-2 py-0.5 text-[10px] font-semibold rounded-md border transition"
                :class="productForm.step_name === step ? 'border-[#7a2e2e] bg-[#7a2e2e] text-white' : 'border-neutral-200 bg-white text-neutral-600 hover:bg-neutral-100'"
                @click="productForm.step_name = step"
              >
                {{ step }}
              </button>
            </div>
          </div>

          <!-- Product Image Upload & Preview -->
          <div>
            <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5 flex items-center justify-between">
              <span>Product Image</span>
              <span v-if="productForm.image_file" class="text-[10px] text-emerald-600 font-bold lowercase">
                ✓ new image selected
              </span>
            </label>
            
            <div class="space-y-2">
              <!-- Image Preview Box -->
              <div 
                v-if="imagePreview" 
                class="relative h-36 w-full bg-neutral-50 rounded-2xl border border-neutral-200 p-2 flex items-center justify-center overflow-hidden group"
              >
                <img :src="imagePreview" alt="Preview" class="h-full object-contain" />
                
                <div class="absolute bottom-2 right-2 flex items-center gap-1.5">
                  <button 
                    type="button" 
                    class="px-3 py-1 bg-white/95 hover:bg-white text-neutral-800 text-[11px] font-bold rounded-lg shadow-sm border border-neutral-200 flex items-center gap-1 transition cursor-pointer"
                    @click="fileInputRef && fileInputRef.click()"
                  >
                    <Upload class="w-3 h-3 text-[#7a2e2e]" />
                    <span>Change</span>
                  </button>
                  <button 
                    type="button" 
                    class="p-1.5 bg-red-600/90 hover:bg-red-600 text-white rounded-lg shadow-sm transition cursor-pointer"
                    title="Remove Image"
                    @click="removeSelectedImage"
                  >
                    <X class="w-3.5 h-3.5" />
                  </button>
                </div>
              </div>

              <!-- Upload Area -->
              <div 
                v-else
                class="border-2 border-dashed border-neutral-300 hover:border-[#7a2e2e] rounded-2xl p-5 text-center transition cursor-pointer bg-neutral-50/50 hover:bg-[#7a2e2e]/5 group"
                @click="fileInputRef && fileInputRef.click()"
              >
                <Upload class="w-7 h-7 mx-auto text-[#7a2e2e] mb-1.5 group-hover:scale-110 transition" />
                <p class="text-xs font-bold text-neutral-800">Click to upload product photo</p>
                <p class="text-[10px] text-neutral-400 mt-0.5">PNG, JPG or WebP up to 5MB</p>
              </div>

              <input 
                ref="fileInputRef"
                type="file" 
                accept="image/*" 
                class="hidden" 
                @change="onImageFileChange"
              />

              <!-- Alternative Image URL Input -->
              <div class="pt-1">
                <input 
                  v-model="productForm.image" 
                  type="text" 
                  placeholder="Or enter image URL / relative path (e.g. /images/products/cleanser.jpg)" 
                  class="w-full px-3 py-1.5 text-[11px] bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e]"
                  @input="imagePreview = productForm.image ? (productForm.image.startsWith('http') ? productForm.image : (productForm.image.startsWith('/') ? productForm.image : '/' + productForm.image)) : null"
                />
              </div>
            </div>
          </div>

          <!-- Description -->
          <div>
            <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
              Description / Benefits
            </label>
            <textarea 
              v-model="productForm.description" 
              rows="3" 
              placeholder="Key ingredients, routine guidance, or wellness benefits..." 
              class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
            ></textarea>
          </div>

          <!-- Sort Order & Active Status -->
          <div class="grid grid-cols-2 gap-4 pt-1">
            <div>
              <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                Sort Order
              </label>
              <input 
                v-model="productForm.sort_order" 
                type="number" 
                placeholder="0" 
                class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e]"
              />
            </div>

            <div>
              <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                Status
              </label>
              <label class="flex items-center gap-2 p-2 bg-neutral-50 border border-neutral-200 rounded-xl cursor-pointer">
                <input 
                  v-model="productForm.is_active" 
                  type="checkbox" 
                  class="w-4 h-4 rounded text-[#7a2e2e] focus:ring-[#7a2e2e]"
                />
                <span class="text-xs font-semibold text-neutral-700">Active (Visible)</span>
              </label>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="pt-4 border-t border-neutral-100 flex items-center justify-end gap-3">
            <button 
              type="button" 
              class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl transition"
              @click="closeProductModal"
            >
              Cancel
            </button>
            <button 
              type="submit" 
              :disabled="productForm.processing"
              class="inline-flex items-center gap-1.5 px-5 py-2.5 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition shadow-xs disabled:opacity-50"
            >
              <Save class="w-4 h-4" />
              <span>{{ editingProduct ? 'Update Product' : 'Create Product' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
