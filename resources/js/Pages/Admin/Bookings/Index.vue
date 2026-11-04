<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Search, Filter, Eye, Trash2, Calendar, Phone, CheckCircle, Clock } from 'lucide-vue-next';

const props = defineProps({
  bookings: { type: Object, required: true },
  specialists: { type: Array, default: () => [] },
  timeSlots: { type: Array, default: () => [] },
  filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search || '');
const status = ref(props.filters.status || '');
const paymentStatus = ref(props.filters.payment_status || '');
const specialistId = ref(props.filters.specialist_id || '');
const date = ref(props.filters.date || '');

const applyFilters = () => {
  router.get('/admin/bookings', {
    search: search.value || undefined,
    status: status.value || undefined,
    payment_status: paymentStatus.value || undefined,
    specialist_id: specialistId.value || undefined,
    date: date.value || undefined,
  }, { preserveState: true, replace: true });
};

const resetFilters = () => {
  search.value = '';
  status.value = '';
  paymentStatus.value = '';
  specialistId.value = '';
  date.value = '';
  applyFilters();
};

const deleteBooking = (id, number) => {
  if (confirm(`Are you sure you want to delete booking #${number}?`)) {
    router.delete(`/admin/bookings/${id}`);
  }
};

const statusBadge = (s) => {
  switch (s) {
    case 'confirmed': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    case 'pending': return 'bg-amber-100 text-amber-800 border-amber-200';
    case 'in_progress': return 'bg-sky-100 text-sky-800 border-sky-200';
    case 'completed': return 'bg-blue-100 text-blue-800 border-blue-200';
    case 'cancelled': return 'bg-rose-100 text-rose-800 border-rose-200';
    default: return 'bg-neutral-100 text-neutral-800 border-neutral-200';
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Bookings | Admin" />

    <div class="space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Bookings Management</h1>
          <p class="text-xs text-neutral-500 mt-1">Review, assign specialists, update appointment status and track payments</p>
        </div>
      </div>

      <!-- Filters Bar -->
      <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <!-- Search Input -->
        <div class="relative">
          <Search class="w-4 h-4 absolute left-3 top-3 text-neutral-400" />
          <input 
            v-model="search" 
            type="text" 
            placeholder="Search client, phone, ID..." 
            class="w-full pl-9 pr-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#b60a0a] outline-none"
            @keyup.enter="applyFilters"
          />
        </div>

        <!-- Status Filter -->
        <div>
          <select 
            v-model="status" 
            class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#b60a0a] outline-none"
            @change="applyFilters"
          >
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="in_progress">In Progress</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>

        <!-- Payment Status Filter -->
        <div>
          <select 
            v-model="paymentStatus" 
            class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#b60a0a] outline-none"
            @change="applyFilters"
          >
            <option value="">All Payment Statuses</option>
            <option value="unpaid">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
            <option value="refunded">Refunded</option>
          </select>
        </div>

        <!-- Specialist Filter -->
        <div>
          <select 
            v-model="specialistId" 
            class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#b60a0a] outline-none"
            @change="applyFilters"
          >
            <option value="">All Specialists</option>
            <option v-for="sp in specialists" :key="sp.id" :value="sp.id">{{ sp.name }}</option>
          </select>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-2">
          <input 
            v-model="date" 
            type="date" 
            class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
            @change="applyFilters"
          />
          <button 
            type="button" 
            class="px-3 py-2 text-xs font-semibold text-neutral-600 hover:text-neutral-900 border border-neutral-200 rounded-xl bg-neutral-50 hover:bg-neutral-100 whitespace-nowrap"
            @click="resetFilters"
          >
            Reset
          </button>
        </div>
      </div>

      <!-- Bookings Table -->
      <div class="bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider border-b border-neutral-200">
              <tr>
                <th class="py-3 px-4">Booking ID</th>
                <th class="py-3 px-4">Customer</th>
                <th class="py-3 px-4">Service</th>
                <th class="py-3 px-4">Date &amp; Time Slot</th>
                <th class="py-3 px-4">Specialist</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4">Payment</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
              <tr v-if="!bookings.data || !bookings.data.length">
                <td colspan="8" class="py-12 text-center text-neutral-400">
                  No bookings found matching your filters.
                </td>
              </tr>
              <tr v-for="b in bookings.data" :key="b.id" class="hover:bg-neutral-50/70 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-neutral-900">
                  #{{ b.booking_number }}
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-neutral-900">{{ b.name }}</div>
                  <div class="text-neutral-500 flex items-center gap-1 mt-0.5">
                    <Phone class="w-3 h-3 text-neutral-400" />
                    <span>{{ b.phone }}</span>
                  </div>
                  <div v-if="b.email" class="text-neutral-400 text-[11px]">{{ b.email }}</div>
                  <div class="text-neutral-400 text-[11px]">Pin: {{ b.pincode }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-semibold text-neutral-800">{{ b.service_name }}</div>
                  <div class="text-[#7a2e2e] font-bold text-xs mt-0.5">₹{{ Math.round(Number(b.total_amount)).toLocaleString('en-IN') }}</div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-medium text-neutral-800 flex items-center gap-1">
                    <Calendar class="w-3 h-3 text-neutral-400" />
                    <span>{{ b.booking_date ? new Date(b.booking_date).toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' }) : 'N/A' }}</span>
                  </div>
                  <div class="text-neutral-500 text-[11px] mt-0.5 flex items-center gap-1">
                    <Clock class="w-3 h-3 text-neutral-400" />
                    <span>{{ b.time_slot_text || 'No preference' }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <span v-if="b.specialist_name" class="font-semibold text-neutral-800 bg-neutral-100 px-2 py-0.5 rounded text-[11px]">
                    {{ b.specialist_name }}
                  </span>
                  <span v-else class="text-neutral-400 italic">Unassigned</span>
                </td>
                <td class="py-3.5 px-4">
                  <span 
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border"
                    :class="statusBadge(b.status)"
                  >
                    {{ b.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <div class="font-bold text-[11px] uppercase tracking-wider" :class="b.payment_status === 'paid' ? 'text-emerald-700' : 'text-amber-700'">
                    {{ b.payment_status }}
                  </div>
                  <div class="text-neutral-400 text-[10px]">{{ b.payment_method }}</div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <Link 
                      :href="`/admin/bookings/${b.id}`" 
                      class="p-1.5 rounded-lg text-[#7a2e2e] hover:bg-[#fbeded] transition"
                      title="View & Edit Booking"
                    >
                      <Eye class="w-4 h-4" />
                    </Link>
                    <button 
                      type="button" 
                      class="p-1.5 rounded-lg text-neutral-400 hover:text-red-600 hover:bg-neutral-100 transition"
                      title="Delete"
                      @click="deleteBooking(b.id, b.booking_number)"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
        <div v-if="bookings.links && bookings.links.length > 3" class="p-4 border-t border-neutral-100 flex items-center justify-between">
          <div class="text-xs text-neutral-500">
            Showing {{ bookings.from }} to {{ bookings.to }} of {{ bookings.total }} bookings
          </div>
          <div class="flex gap-1">
            <Link 
              v-for="(link, i) in bookings.links" 
              :key="i"
              :href="link.url || '#'"
              class="px-3 py-1.5 text-xs rounded-lg border"
              :class="link.active 
                ? 'bg-[#7a2e2e] text-white border-[#7a2e2e] font-bold' 
                : 'bg-white text-neutral-700 border-neutral-200 hover:bg-neutral-50'"
              v-html="link.label"
            />
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
