<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { Lock, Mail, ArrowRight } from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const submit = () => {
  form.post('/admin/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Admin Login | FMZ Glow" />

  <div class="min-h-screen bg-[#fff8f5] flex items-center justify-center p-4">
    <div class="w-full max-w-md bg-white rounded-3xl shadow-xl border border-[#f1e0dc] p-8 md:p-10">
      <div class="text-center mb-8">
        <h1 class="text-3xl font-serif text-[#7a2e2e] font-bold tracking-tight">FMZ GLOW</h1>
        <p class="text-xs uppercase tracking-widest text-[#b60a0a] font-bold mt-1">Management Portal</p>
        <p class="text-sm text-neutral-500 mt-2">Sign in to manage bookings, services, and time slots</p>
      </div>

      <form @submit.prevent="submit" class="space-y-5">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 mb-2">Email Address</label>
          <div class="relative flex items-center">
            <div class="absolute left-3.5 flex items-center pointer-events-none text-neutral-400 z-10">
              <Mail class="w-4 h-4" />
            </div>
            <input 
              v-model="form.email" 
              type="email" 
              autocomplete="username"
              required
              class="w-full !pl-11 pr-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:bg-white focus:border-[#b60a0a] focus:ring-1 focus:ring-[#b60a0a] transition outline-none"
              placeholder="Enter your email"
            />
          </div>
          <span v-if="form.errors.email" class="text-xs text-red-600 mt-1 block">{{ form.errors.email }}</span>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-neutral-600 mb-2">Password</label>
          <div class="relative flex items-center">
            <div class="absolute left-3.5 flex items-center pointer-events-none text-neutral-400 z-10">
              <Lock class="w-4 h-4" />
            </div>
            <input 
              v-model="form.password" 
              type="password" 
              autocomplete="current-password"
              required
              class="w-full !pl-11 pr-4 py-3 bg-neutral-50 border border-neutral-200 rounded-xl text-sm focus:bg-white focus:border-[#b60a0a] focus:ring-1 focus:ring-[#b60a0a] transition outline-none"
              placeholder="••••••••"
            />
          </div>
          <span v-if="form.errors.password" class="text-xs text-red-600 mt-1 block">{{ form.errors.password }}</span>
        </div>

        <div class="flex items-center justify-between text-xs">
          <label class="flex items-center gap-2 cursor-pointer text-neutral-600 select-none">
            <input 
              type="checkbox" 
              v-model="form.remember" 
              class="w-4 h-4 rounded text-[#b60a0a] focus:ring-[#b60a0a] shrink-0 cursor-pointer accent-[#b60a0a]" 
            />
            <span>Remember me</span>
          </label>
        </div>

        <button 
          type="submit" 
          :disabled="form.processing"
          class="w-full py-3.5 px-4 bg-[#7a2e2e] hover:bg-[#52201f] text-white font-bold text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md hover:shadow-lg disabled:opacity-50"
        >
          <span>{{ form.processing ? 'Signing in...' : 'Sign In to Dashboard' }}</span>
          <ArrowRight class="w-4 h-4" />
        </button>
      </form>

      <div class="mt-8 text-center border-t border-neutral-100 pt-6">
        <a href="/" class="text-xs font-semibold text-[#b60a0a] hover:underline">← Back to FMZ Glow Website</a>
      </div>
    </div>
  </div>
</template>
