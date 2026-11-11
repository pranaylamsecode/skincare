<script setup>
import { ref, watch } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Save, Phone, Mail, CreditCard, Bell, Sparkles, Image, Upload } from 'lucide-vue-next';

const props = defineProps({
  settings: { type: Object, default: () => ({}) }
});

const form = useForm({
  site_name: props.settings.site_name || 'FMZ Glow',
  site_favicon: props.settings.site_favicon || 'images/FMZ Glow logo.jpg',
  site_logo: props.settings.site_logo || 'images/FMZ Glow logo.jpg',
  favicon_file: null,
  logo_file: null,
  business_email: props.settings.business_email || 'fmz@fmzau.com.au',
  whatsapp_number: props.settings.whatsapp_number || '+917303706047',
  whatsapp_default_message: props.settings.whatsapp_default_message || "Hi FMZ Glow, I'd like to know more about your at-home skincare services.",
  topbar_text: props.settings.topbar_text || 'Launch offer: Nu Skin LumiSpa Glow Facial <b>₹999</b> <s>₹1,199</s> · limited period · T&C apply',
  hero_eyebrow: props.settings.hero_eyebrow || 'At-home skincare by FMZ India',
  hero_title: props.settings.hero_title || 'Skincare that<br><em>understands</em> you.',
  hero_lead: props.settings.hero_lead || 'We listen to your skin concerns first. Then a certified woman specialist brings professional Nu Skin care to your home, explains every step, and leaves you with a routine that fits your life.',
  hero_tagline: props.settings.hero_tagline || 'Your Glow. Your Home. Your Time.',
  active_payment_gateway: props.settings.active_payment_gateway || 'razorpay',
  razorpay_key: props.settings.razorpay_key || '',
  razorpay_secret: props.settings.razorpay_secret || '',
  razorpay_enabled: props.settings.razorpay_enabled || '0',
  zoho_enabled: props.settings.zoho_enabled || '0',
  zoho_client_id: props.settings.zoho_client_id || '',
  zoho_client_secret: props.settings.zoho_client_secret || '',
  zoho_checkout_url: props.settings.zoho_checkout_url || '',
  zoho_merchant_id: props.settings.zoho_merchant_id || props.settings.zoho_account_id || '',
  zoho_refresh_token: props.settings.zoho_refresh_token || '',
  zoho_access_token: props.settings.zoho_access_token || '',
});

// Keep active_payment_gateway and toggle checkboxes synchronized
watch(() => form.active_payment_gateway, (val) => {
  if (val === 'razorpay') {
    form.razorpay_enabled = '1';
    form.zoho_enabled = '0';
  } else if (val === 'zoho') {
    form.zoho_enabled = '1';
    form.razorpay_enabled = '0';
  } else if (val === 'none') {
    form.razorpay_enabled = '0';
    form.zoho_enabled = '0';
  }
});

watch(() => form.razorpay_enabled, (val) => {
  if (val === '1') {
    form.active_payment_gateway = 'razorpay';
    form.zoho_enabled = '0';
  } else if (form.active_payment_gateway === 'razorpay') {
    form.active_payment_gateway = 'none';
  }
});

watch(() => form.zoho_enabled, (val) => {
  if (val === '1') {
    form.active_payment_gateway = 'zoho';
    form.razorpay_enabled = '0';
  } else if (form.active_payment_gateway === 'zoho') {
    form.active_payment_gateway = 'none';
  }
});

const faviconPreview = ref(props.settings.site_favicon ? '/' + props.settings.site_favicon.replace(/^\/+/, '') : null);
const logoPreview = ref(props.settings.site_logo ? '/' + props.settings.site_logo.replace(/^\/+/, '') : null);

const onFaviconSelected = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.favicon_file = file;
    faviconPreview.value = URL.createObjectURL(file);
  }
};

const onLogoSelected = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.logo_file = file;
    logoPreview.value = URL.createObjectURL(file);
  }
};

const submit = () => {
  form.post('/admin/settings', {
    forceFormData: true,
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="System Settings | Admin" />

    <div class="space-y-6 max-w-4xl mx-auto">
      <div>
        <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Website &amp; Gateway Settings</h1>
        <p class="text-xs text-neutral-500 mt-1">Configure business contacts, WhatsApp integration, announcement topbar and Razorpay payments</p>
      </div>

      <form @submit.prevent="submit" class="space-y-6">
        <!-- Website Brand, Favicon & Logo -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-5">
          <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2 pb-2 border-b border-neutral-100">
            <Image class="w-4 h-4 text-[#7a2e2e]" />
            <span>Website Branding, Favicon &amp; Logo</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Site Brand Name</label>
              <input 
                v-model="form.site_name" 
                type="text" 
                placeholder="FMZ Glow"
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-semibold"
              />
            </div>

            <!-- Favicon Upload -->
            <div class="p-4 bg-neutral-50 rounded-xl border border-neutral-200 space-y-3">
              <div class="flex items-center justify-between">
                <div>
                  <label class="block text-xs font-bold text-neutral-800 uppercase">Website Favicon</label>
                  <p class="text-[11px] text-neutral-500">Browser tab icon (.ico, .png, .svg)</p>
                </div>
                <div v-if="faviconPreview" class="w-8 h-8 rounded-lg bg-white border border-neutral-300 p-1 flex items-center justify-center shrink-0">
                  <img :src="faviconPreview" alt="Favicon Preview" class="w-full h-full object-contain" />
                </div>
              </div>

              <div>
                <input 
                  type="file" 
                  accept=".ico,.png,.svg,.jpg,.jpeg" 
                  @change="onFaviconSelected" 
                  class="block w-full text-xs text-neutral-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#7a2e2e] file:text-white hover:file:bg-[#52201f] cursor-pointer"
                />
              </div>
            </div>

            <!-- Website & App Logo Upload -->
            <div class="md:col-span-2 p-4 bg-neutral-50 rounded-xl border border-neutral-200 space-y-3">
              <div class="flex items-center justify-between">
                <div>
                  <label class="block text-xs font-bold text-neutral-800 uppercase">Brand / Header Logo</label>
                  <p class="text-[11px] text-neutral-500">Main header emblem and mobile app logo (.png, .jpg, .svg, .webp)</p>
                </div>
                <div v-if="logoPreview" class="h-12 w-28 rounded-lg bg-white border border-neutral-300 p-1 flex items-center justify-center shrink-0">
                  <img :src="logoPreview" alt="Logo Preview" class="max-h-full max-w-full object-contain" />
                </div>
              </div>

              <div>
                <input 
                  type="file" 
                  accept=".png,.jpg,.jpeg,.svg,.webp" 
                  @change="onLogoSelected" 
                  class="block w-full text-xs text-neutral-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-[#7a2e2e] file:text-white hover:file:bg-[#52201f] cursor-pointer"
                />
              </div>
            </div>
          </div>
        </div>

        <!-- WhatsApp & Contact -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2 pb-2 border-b border-neutral-100">
            <Phone class="w-4 h-4 text-emerald-600" />
            <span>WhatsApp &amp; Notifications Contact</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">WhatsApp Number (with country code)</label>
              <input 
                v-model="form.whatsapp_number" 
                type="text" 
                placeholder="+917303706047"
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Admin Notification Email</label>
              <input 
                v-model="form.business_email" 
                type="email" 
                placeholder="fmz@fmzau.com.au"
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Default WhatsApp Message</label>
              <input 
                v-model="form.whatsapp_default_message" 
                type="text" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>
          </div>
        </div>

        <!-- Announcement Topbar -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2 pb-2 border-b border-neutral-100">
            <Bell class="w-4 h-4 text-[#7a2e2e]" />
            <span>Homepage Topbar Announcement</span>
          </h2>

          <div>
            <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Topbar Text (Supports HTML bold and strikethrough)</label>
            <input 
              v-model="form.topbar_text" 
              type="text" 
              class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-semibold text-[#7a2e2e]"
            />
            <div class="mt-2 text-xs p-2.5 bg-[#fff8f5] rounded-lg border border-[#f1e0dc] text-neutral-600">
              <b>Live Preview:</b> <span v-html="form.topbar_text"></span>
            </div>
          </div>
        </div>

        <!-- Hero Section Copy -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2 pb-2 border-b border-neutral-100">
            <Sparkles class="w-4 h-4 text-[#b60a0a]" />
            <span>Hero Headline &amp; Tagline</span>
          </h2>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Hero Eyebrow</label>
              <input 
                v-model="form.hero_eyebrow" 
                type="text" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Tagline</label>
              <input 
                v-model="form.hero_tagline" 
                type="text" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Hero Headline (HTML)</label>
              <input 
                v-model="form.hero_title" 
                type="text" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-semibold"
              />
            </div>
            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Hero Lead Paragraph</label>
              <textarea 
                v-model="form.hero_lead" 
                rows="2"
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Payment Gateway Selector -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-emerald-600" />
              <span>Active Online Payment Gateway</span>
            </h2>
          </div>

          <p class="text-xs text-neutral-500">
            Select which payment gateway is active for customer online checkout. You can toggle between <b>Zoho Checkout</b>, <b>Razorpay</b>, or keep it <b>Cash / UPI After Visit only</b>.
          </p>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <label 
              :class="[
                'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none',
                form.active_payment_gateway === 'zoho' 
                  ? 'border-emerald-600 bg-emerald-50/50 text-emerald-950 font-bold' 
                  : 'border-neutral-200 hover:border-neutral-300 text-neutral-700'
              ]"
            >
              <input 
                type="radio" 
                value="zoho" 
                v-model="form.active_payment_gateway" 
                class="accent-emerald-600 w-4 h-4"
              />
              <div>
                <div class="text-xs font-bold">Zoho Checkout</div>
                <div class="text-[11px] text-neutral-500">Zoho Payments Link / Page</div>
              </div>
            </label>

            <label 
              :class="[
                'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none',
                form.active_payment_gateway === 'razorpay' 
                  ? 'border-purple-600 bg-purple-50/50 text-purple-950 font-bold' 
                  : 'border-neutral-200 hover:border-neutral-300 text-neutral-700'
              ]"
            >
              <input 
                type="radio" 
                value="razorpay" 
                v-model="form.active_payment_gateway" 
                class="accent-purple-600 w-4 h-4"
              />
              <div>
                <div class="text-xs font-bold">Razorpay</div>
                <div class="text-[11px] text-neutral-500">UPI, Cards & NetBanking</div>
              </div>
            </label>

            <label 
              :class="[
                'flex items-center gap-3 p-3.5 rounded-xl border cursor-pointer transition select-none',
                form.active_payment_gateway === 'none' 
                  ? 'border-[#7a2e2e] bg-[#fbf5f4] text-[#7a2e2e] font-bold' 
                  : 'border-neutral-200 hover:border-neutral-300 text-neutral-700'
              ]"
            >
              <input 
                type="radio" 
                value="none" 
                v-model="form.active_payment_gateway" 
                class="accent-[#7a2e2e] w-4 h-4"
              />
              <div>
                <div class="text-xs font-bold">Pay After Visit Only</div>
                <div class="text-[11px] text-neutral-500">No online checkout step</div>
              </div>
            </label>
          </div>
        </div>

        <!-- Zoho Checkout Settings -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-emerald-600" />
              <span>Zoho Checkout Configuration</span>
            </h2>
            <div class="flex items-center gap-2 select-none">
              <input 
                type="checkbox" 
                id="zohoActive" 
                v-model="form.zoho_enabled" 
                true-value="1" 
                false-value="0"
                class="w-4 h-4 rounded text-emerald-600 focus:ring-emerald-600 cursor-pointer shrink-0 accent-emerald-600"
              />
              <label for="zohoActive" class="text-xs font-bold text-neutral-700 cursor-pointer">Enable Zoho</label>
            </div>
          </div>

          <p class="text-xs text-neutral-500">
            Provide your Zoho Payments / Zoho Checkout Client ID, Secret, and Checkout Page URL.
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Zoho Client ID</label>
              <input 
                v-model="form.zoho_client_id" 
                type="text" 
                placeholder="1000.XXXXXX..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Zoho Client Secret</label>
              <input 
                v-model="form.zoho_client_secret" 
                type="password" 
                placeholder="••••••••••••••••••••••••" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Zoho Payments Account ID</label>
              <input 
                v-model="form.zoho_merchant_id" 
                type="text" 
                placeholder="e.g. 6002XXXXXX" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
              <p class="text-[11px] text-neutral-500 mt-1">Found in Zoho Payments Dashboard &rarr; Settings &rarr; Account Details.</p>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Zoho OAuth Refresh Token</label>
              <input 
                v-model="form.zoho_refresh_token" 
                type="password" 
                placeholder="1000.XXXXXX..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
              <p class="text-[11px] text-neutral-500 mt-1">Generated in Zoho API Console with scope <code class="bg-neutral-100 px-1 py-0.5 rounded text-neutral-700">ZohoPay.payments.CREATE</code>.</p>
            </div>

            <div class="md:col-span-2">
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Optional: Custom Payment Page URL Override</label>
              <input 
                v-model="form.zoho_checkout_url" 
                type="text" 
                placeholder="https://checkout.zoho.in/v2/... (leave blank to use dynamic API sessions)" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
              <p class="text-[11px] text-neutral-500 mt-1">
                Leave blank if using the official Zoho Payments Session API above. Or paste an explicit Zoho Checkout hosted link.
              </p>
            </div>
          </div>
        </div>

        <!-- Razorpay Payment Gateway -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-purple-600" />
              <span>Razorpay Payment Gateway</span>
            </h2>
            <div class="flex items-center gap-2 select-none">
              <input 
                type="checkbox" 
                id="rzpActive" 
                v-model="form.razorpay_enabled" 
                true-value="1" 
                false-value="0"
                class="w-4 h-4 rounded text-purple-600 focus:ring-purple-600 cursor-pointer shrink-0 accent-purple-600"
              />
              <label for="rzpActive" class="text-xs font-bold text-neutral-700 cursor-pointer">Enable Razorpay</label>
            </div>
          </div>

          <p class="text-xs text-neutral-500">
            When enabled, users who select "Pay Online Now" during booking can pay via UPI, Credit/Debit Cards, or NetBanking.
          </p>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Key ID (rzp_test_... or rzp_live_...)</label>
              <input 
                v-model="form.razorpay_key" 
                type="text" 
                placeholder="rzp_test_... or rzp_live_..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Key Secret</label>
              <input 
                v-model="form.razorpay_secret" 
                type="password" 
                placeholder="Enter secret from Razorpay dashboard" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
              <p class="text-[11px] text-amber-600 mt-1 font-medium">
                Note: Key Secret is generated in Razorpay Dashboard > Settings > API Keys. It is private and different from the Key ID.
              </p>
            </div>
          </div>
        </div>

        <div class="flex justify-end pt-2">
          <button 
            type="submit" 
            :disabled="form.processing"
            class="px-6 py-3 bg-[#7a2e2e] hover:bg-[#52201f] text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-xs disabled:opacity-50"
          >
            <Save class="w-4 h-4" />
            <span>{{ form.processing ? 'Saving Settings...' : 'Save All Settings' }}</span>
          </button>
        </div>
      </form>
    </div>
  </AdminLayout>
</template>
