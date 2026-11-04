<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Save, Phone, Mail, CreditCard, Bell, Sparkles } from 'lucide-vue-next';

const props = defineProps({
  settings: { type: Object, default: () => ({}) }
});

const form = useForm({
  site_name: props.settings.site_name || 'FMZ Glow',
  business_email: props.settings.business_email || 'fmz@fmzau.com.au',
  whatsapp_number: props.settings.whatsapp_number || '+917303706047',
  whatsapp_default_message: props.settings.whatsapp_default_message || "Hi FMZ Glow, I'd like to know more about your at-home skincare services.",
  topbar_text: props.settings.topbar_text || 'Launch offer: Nu Skin LumiSpa Glow Facial <b>₹999</b> <s>₹1,199</s> · limited period · T&C apply',
  hero_eyebrow: props.settings.hero_eyebrow || 'At-home skincare by FMZ India',
  hero_title: props.settings.hero_title || 'Skincare that<br><em>understands</em> you.',
  hero_lead: props.settings.hero_lead || 'We listen to your skin concerns first. Then a certified woman specialist brings professional Nu Skin care to your home, explains every step, and leaves you with a routine that fits your life.',
  hero_tagline: props.settings.hero_tagline || 'Your Glow. Your Home. Your Time.',
  razorpay_key: props.settings.razorpay_key || '',
  razorpay_secret: props.settings.razorpay_secret || '',
  razorpay_enabled: props.settings.razorpay_enabled || '0',
});

const submit = () => {
  form.post('/admin/settings');
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

        <!-- Razorpay Payment Gateway -->
        <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
          <div class="flex items-center justify-between pb-2 border-b border-neutral-100">
            <h2 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
              <CreditCard class="w-4 h-4 text-purple-600" />
              <span>Razorpay Payment Gateway</span>
            </h2>
            <div class="flex items-center gap-2">
              <input 
                type="checkbox" 
                id="rzpActive" 
                v-model="form.razorpay_enabled" 
                true-value="1" 
                false-value="0"
                class="rounded text-purple-600 focus:ring-purple-600 cursor-pointer"
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
                placeholder="rzp_test_..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Key Secret</label>
              <input 
                v-model="form.razorpay_secret" 
                type="password" 
                placeholder="••••••••••••••••••••••••" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-mono"
              />
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
