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
  EyeOff,
  FileText,
  ExternalLink
} from 'lucide-vue-next';

const props = defineProps({
  initialTab: { type: String, default: 'products' },
  plans: { type: Array, default: () => [] },
  devices: { type: Array, default: () => [] },
  products: { type: Array, default: () => [] },
  concerns: { type: Array, default: () => [] },
  reviews: { type: Array, default: () => [] },
  faqs: { type: Array, default: () => [] },
  pages: { type: Array, default: () => [] },
});

const activeTab = ref(props.initialTab || 'products');

onMounted(() => {
  const urlParams = new URLSearchParams(window.location.search);
  const tab = urlParams.get('tab');
  if (tab && ['plans', 'faqs', 'products', 'devices', 'policies'].includes(tab)) {
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

// ==================== POLICY PAGES MANAGEMENT ====================
const defaultPolicies = [
  {
    id: null,
    title: 'Privacy Policy',
    slug: 'privacy-policy',
    meta_description: 'Privacy Policy for FMZ Glow at-home skincare services. How we protect your data and personal information.',
    sort_order: 1,
    is_active: true,
    content: `### 1. Introduction
Welcome to FMZ Glow ("FMZ India", "we", "our", or "us"). We provide certified at-home skincare and wellness services delivered by trained women specialists in Ahmedabad, Gandhinagar, Surat, Rajkot, and surrounding areas. We respect your privacy and are committed to protecting your personal data.

### 2. Information We Collect
We collect only the information necessary to provide, confirm, and deliver our at-home skincare visits safely:
- **Contact Details:** Your full name, mobile / WhatsApp number, and email address.
- **Visit Address:** Your residential address, locality, and postal pincode so our certified specialist can navigate to your home.
- **Skin & Health Information:** Optional skin concerns, allergies, or conditions shared during booking to help your specialist prepare the right Nu Skin products and ensure treatment safety (e.g., pregnancy or medical implants for Galvanic Spa).
- **Communication History:** Booking requests, confirmations, and customer support messages exchanged via WhatsApp or email.

### 3. How We Use Your Information
Your information is used exclusively for:
- Scheduling appointments and assigning a verified woman specialist to your home.
- Sending written booking confirmations, specialist identity, exact arrival time, and total price via WhatsApp and email.
- Calculating travel distance (free within 8 km; transparent nominal travel confirmed in writing for farther distances).
- Providing aftercare tips and voluntary customer service follow-ups.

### 4. Specialist Verification & Confidentiality
All FMZ Glow specialists are verified women professionals with science, pharmacy, or nursing backgrounds. Your residential address and phone number are shared solely with the assigned specialist for the purpose of completing your visit. We never sell, rent, or trade your personal information to third-party marketers or advertisers.

### 5. Secure Payments
We offer **Pay After Your Visit** via UPI or Cash directly to your specialist once you are fully satisfied with your treatment. For customers choosing to pay online, payment transactions are encrypted and processed through RBI-approved gateways (Razorpay / Zoho Payments). We do not store your credit card numbers, debit card details, or UPI PINs.

### 6. Data Security & Retention
We maintain administrative and technical safeguards to keep your personal data secure. Booking records are retained only as long as necessary to satisfy operational, accounting, and legal requirements.

### 7. Contact Us
If you have any questions about this Privacy Policy or wish to access, correct, or delete your personal details, please contact us:
- **WhatsApp / Phone:** +91 73037 06047
- **Email:** fmz@fmzau.com.au
- **Operating Hours:** 9:00 AM – 8:00 PM (Monday – Sunday)`
  },
  {
    id: null,
    title: 'Terms & Conditions',
    slug: 'terms-and-conditions',
    meta_description: 'Terms and Conditions for FMZ Glow at-home skincare bookings and appointments.',
    sort_order: 2,
    is_active: true,
    content: `### 1. Overview & Agreement
By booking an appointment through FMZ Glow (myfmz.com), WhatsApp, or telephone, you agree to these Terms and Conditions. Please review them carefully before scheduling a visit.

### 2. Women Specialists Guarantee
- **Exclusively Certified Women Specialists:** All at-home treatments are performed exclusively by certified women specialists with pharmacy, nursing, or science credentials.
- **Zero Tolerance Policy:** We maintain a strict zero-tolerance policy against any inappropriate behavior, harassment, or unsafe conditions directed toward our specialists. The specialist reserves the unconditional right to leave immediately if safety or dignity is compromised, and legal action may be pursued where warranted.

### 3. Genuine Nu Skin Products & Safety Protocols
- **Authentic & Sealed:** We exclusively use authentic, genuine Nu Skin devices (LumiSpa iO, Galvanic Spa, Prysm iO) and professional skincare formulations. Products are unsealed in front of you.
- **Pre-Visit Skin & Health Consultation:** Every visit begins with a brief consultation. Before using the Galvanic Spa, your specialist will verify contraindications (such as pregnancy, pacemakers, metal dental/facial implants, or acute skin infections). If contraindicated, she will skip or adapt that step for your safety.

### 4. Appointment Booking & Confirmation
- **Written Confirmation on WhatsApp:** Once you submit a booking request, our coordination team confirms your specialist's name, phone number, confirmed date, time slot, and total price in writing on WhatsApp within 30 minutes during operating hours (9:00 AM to 8:00 PM).
- **Service Availability:** Services are offered across Ahmedabad, Gandhinagar, Surat, and Rajkot within serviceable pincodes.

### 5. Pricing, Travel & Payment
- **Transparent Pricing:** The price confirmed in writing is the final price. There are no hidden setup fees or surprise charges.
- **Travel Policy:** Travel is completely **free within 8 km** of the assigned specialist. For locations beyond 8 km, any modest travel surcharge will be calculated transparently and confirmed with you in writing before dispatching the specialist.
- **Pay After Your Visit:** You may pay by UPI or Cash after your visit once you are satisfied with your treatment. If you choose online prepayment, receipts are delivered electronically to your email.

### 6. Modifications to Service
FMZ Glow reserves the right to modify service offerings, pricing, or terms with prior notice published on this website. Confirmed bookings will always be honored at the price confirmed at the time of scheduling.`
  },
  {
    id: null,
    title: 'Cancellation and Refunds',
    slug: 'cancellation-and-refunds',
    meta_description: 'Cancellation and refund policies for FMZ Glow appointments and multi-visit plans.',
    sort_order: 3,
    is_active: true,
    content: `### 1. Free Cancellation & Rescheduling
We understand that plans can change. We offer complete flexibility:
- **Free Cancellation:** You can cancel or reschedule your appointment with **no fee or penalty** up to **4 hours** before your scheduled appointment time.
- **How to Reschedule or Cancel:** Simply message our team on WhatsApp at **+91 73037 06047** or call us with your booking ID.

### 2. Late Cancellations & Doorstep No-Shows
- If an appointment is cancelled with less than 2 hours' notice after the specialist has already commenced travel or arrived at your doorstep, a nominal travel convenience fee of **₹150** may be requested to compensate the specialist for her travel time.
- If you are unavailable or unresponsive at the scheduled address for more than 20 minutes past the scheduled time slot, the visit may be marked as a no-show.

### 3. Refund Policy for Online Prepayments
- If you prepaid online and cancel at least 4 hours before the appointment, a **100% full refund** will be automatically initiated to your original payment method (Bank Account, UPI, or Credit/Debit Card).
- Refunds typically reflect in your account within **5 to 7 business days**, depending on your issuing bank.

### 4. Multi-Visit Plans (e.g. Glow 3-Visit Plan)
- **Validity:** The Glow 3-Visit Plan (₹2,799 / ₹933 per visit) is valid for **90 days** from the date of the first visit.
- **Transferability:** Visits can be shared with immediate family members (e.g., mother, sister, or daughter) residing at the same address.
- **Plan Cancellation & Pro-Rata Refunds:** If you wish to discontinue a 3-visit plan after completing 1 visit, the completed session will be billed at the standard individual visit rate (₹999), and the entire remaining balance (₹1,800) will be promptly refunded to you.

### 5. Satisfaction Guarantee
Your comfort, safety, and glow are our highest priorities. If you are not completely satisfied with your specialist's service or if any product causes unexpected discomfort, please alert us within 24 hours of your visit. Our care coordinator will arrange a complimentary follow-up or provide an appropriate resolution.

### 6. Support Contact
For all cancellation, rescheduling, and refund requests:
- **WhatsApp Support:** +91 73037 06047
- **Email:** fmz@fmzau.com.au
- **Response Time:** Typically within 30 minutes (9:00 AM – 8:00 PM)`
  }
];

const allPolicies = computed(() => {
  if (props.pages && props.pages.length > 0) {
    return props.pages;
  }
  return defaultPolicies;
});

const selectedPolicySlug = ref('privacy-policy');

const currentPolicy = computed(() => {
  return allPolicies.value.find(p => p.slug === selectedPolicySlug.value) || allPolicies.value[0];
});

const policyForm = useForm({
  id: null,
  title: '',
  slug: '',
  content: '',
  meta_description: '',
  is_active: true,
  sort_order: 1,
});

const selectPolicy = (p) => {
  selectedPolicySlug.value = p.slug;
  policyForm.id = p.id || null;
  policyForm.title = p.title || '';
  policyForm.slug = p.slug || '';
  policyForm.content = p.content || '';
  policyForm.meta_description = p.meta_description || '';
  policyForm.is_active = p.is_active !== undefined ? Boolean(p.is_active) : true;
  policyForm.sort_order = p.sort_order || 1;
};

onMounted(() => {
  if (currentPolicy.value) {
    selectPolicy(currentPolicy.value);
  }
});

const savePolicy = () => {
  const url = policyForm.id ? `/admin/content/pages/${policyForm.id}` : '/admin/content/pages';
  policyForm.post(url, {
    preserveScroll: true,
  });
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

const formatImageUrl = (img, updatedAt = null) => {
  if (!img) return '';
  let url = img;
  if (!url.startsWith('http://') && !url.startsWith('https://')) {
    url = url.startsWith('/') ? url : '/' + url;
  }
  if (updatedAt) {
    const ts = new Date(updatedAt).getTime() || updatedAt;
    url += (url.includes('?') ? '&' : '?') + 't=' + ts;
  }
  return url;
};

const saveProduct = () => {
  const endpoint = editingProduct.value 
    ? `/admin/content/products/${editingProduct.value.id}` 
    : '/admin/content/products';

  productForm.post(endpoint, {
    forceFormData: true,
    preserveScroll: true,
    onSuccess: () => {
      closeProductModal();
      router.reload({ only: ['products'] });
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

        <button 
          type="button" 
          class="px-4 py-2 text-xs font-bold rounded-xl transition flex items-center gap-1.5 whitespace-nowrap"
          :class="activeTab === 'policies' ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-white text-neutral-600 hover:bg-neutral-100'"
          @click="activeTab = 'policies'"
        >
          <FileText class="w-4 h-4" />
          <span>Policies &amp; Legal ({{ allPolicies.length }})</span>
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
                  :key="prod.id + '_' + prod.image + '_' + (prod.updated_at || '')"
                  :src="formatImageUrl(prod.image, prod.updated_at)" 
                  :alt="prod.name" 
                  class="h-full w-full object-contain group-hover:scale-105 transition duration-300" 
                  @error="(e) => e.target.style.display = 'none'"
                  @load="(e) => e.target.style.display = 'block'"
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

      <!-- ==================== TAB: POLICIES ==================== -->
      <div v-if="activeTab === 'policies'" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
          <!-- Left: Policy Selector Cards -->
          <div class="lg:col-span-4 space-y-3">
            <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs">
              <h3 class="text-xs font-bold uppercase tracking-wider text-neutral-500 mb-3">Pages &amp; Policies</h3>
              <div class="space-y-2">
                <div
                  v-for="p in allPolicies"
                  :key="p.slug"
                  class="p-3.5 rounded-xl border transition cursor-pointer flex flex-col gap-1.5"
                  :class="selectedPolicySlug === p.slug ? 'border-[#7a2e2e] bg-[#7a2e2e]/5 shadow-xs' : 'border-neutral-200 bg-white hover:bg-neutral-50'"
                  @click="selectPolicy(p)"
                >
                  <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-neutral-900">{{ p.title }}</span>
                    <span 
                      class="text-[10px] font-bold px-2 py-0.5 rounded-full"
                      :class="p.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-neutral-100 text-neutral-500'"
                    >
                      {{ p.is_active ? 'Active' : 'Draft' }}
                    </span>
                  </div>
                  <div class="flex items-center justify-between text-[11px] text-neutral-500">
                    <span class="font-mono">/{{ p.slug }}</span>
                    <a 
                      :href="`/${p.slug}`" 
                      target="_blank" 
                      rel="noopener noreferrer"
                      class="inline-flex items-center gap-1 text-[#7a2e2e] hover:underline font-semibold"
                      @click.stop
                    >
                      <span>View</span>
                      <ExternalLink class="w-3 h-3" />
                    </a>
                  </div>
                </div>
              </div>
            </div>

            <!-- Helpful Guidelines Box -->
            <div class="bg-neutral-50 p-4 rounded-2xl border border-neutral-200 text-xs text-neutral-600 space-y-2">
              <p class="font-bold text-neutral-800 flex items-center gap-1.5">
                <FileText class="w-4 h-4 text-[#7a2e2e]" />
                <span>Legal Pages Information</span>
              </p>
              <p class="text-[11px] leading-relaxed">
                These legal pages are accessible directly from your website footer and comply with customer protection requirements. You can edit any page content at any time; changes take effect immediately on the live website.
              </p>
              <div class="pt-2 border-t border-neutral-200 text-[11px] space-y-1 text-neutral-500">
                <p>• <strong>### Title</strong> for numbered section headers</p>
                <p>• <strong>- Bullet</strong> for listed terms or guarantees</p>
                <p>• <strong>**Bold**</strong> for highlighted terms</p>
              </div>
            </div>
          </div>

          <!-- Right: Edit Policy Form -->
          <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs">
            <div class="flex items-center justify-between pb-4 mb-5 border-b border-neutral-100">
              <div>
                <h2 class="text-base font-bold text-neutral-900">
                  Edit: {{ policyForm.title || 'Page Content' }}
                </h2>
                <p class="text-xs text-neutral-500 mt-0.5">
                  Live URL: <a :href="`/${policyForm.slug}`" target="_blank" class="text-[#7a2e2e] underline font-mono">/{{ policyForm.slug }}</a>
                </p>
              </div>
              <a 
                :href="`/${policyForm.slug}`" 
                target="_blank" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 text-xs font-semibold rounded-xl transition"
              >
                <span>Preview Live</span>
                <ExternalLink class="w-3.5 h-3.5" />
              </a>
            </div>

            <form @submit.prevent="savePolicy" class="space-y-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                    Page Title <span class="text-red-500">*</span>
                  </label>
                  <input 
                    v-model="policyForm.title" 
                    type="text" 
                    required 
                    placeholder="e.g. Privacy Policy" 
                    class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
                  />
                </div>

                <div>
                  <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                    URL Slug <span class="text-red-500">*</span>
                  </label>
                  <input 
                    v-model="policyForm.slug" 
                    type="text" 
                    required 
                    placeholder="e.g. privacy-policy" 
                    class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition font-mono"
                  />
                </div>
              </div>

              <div>
                <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                  Meta Description (for SEO)
                </label>
                <input 
                  v-model="policyForm.meta_description" 
                  type="text" 
                  placeholder="Short description for search engines and social sharing..." 
                  class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
                />
              </div>

              <div>
                <div class="flex items-center justify-between mb-1.5">
                  <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider">
                    Page Content (Markdown &amp; Text) <span class="text-red-500">*</span>
                  </label>
                  <span class="text-[10px] text-neutral-400 font-mono">
                    {{ policyForm.content ? policyForm.content.length : 0 }} characters
                  </span>
                </div>
                <textarea 
                  v-model="policyForm.content" 
                  rows="16" 
                  required 
                  placeholder="Enter page text, sections, and terms..." 
                  class="w-full px-3.5 py-3 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition font-mono leading-relaxed"
                ></textarea>
              </div>

              <div class="flex items-center justify-between pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                  <input 
                    v-model="policyForm.is_active" 
                    type="checkbox" 
                    class="w-4 h-4 rounded text-[#7a2e2e] focus:ring-[#7a2e2e]"
                  />
                  <span class="text-xs font-semibold text-neutral-700">Page Published &amp; Active</span>
                </label>

                <button 
                  type="submit" 
                  :disabled="policyForm.processing"
                  class="inline-flex items-center gap-2 px-6 py-2.5 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition shadow-xs disabled:opacity-50"
                >
                  <Save class="w-4 h-4" />
                  <span>{{ policyForm.processing ? 'Saving...' : 'Save Page Content' }}</span>
                </button>
              </div>
            </form>
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
