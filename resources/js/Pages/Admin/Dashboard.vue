<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  CalendarCheck, 
  Clock, 
  Users, 
  IndianRupee, 
  AlertCircle, 
  CheckCircle2, 
  ArrowUpRight,
  Eye 
} from 'lucide-vue-next';

defineProps({
  stats: { type: Object, required: true },
  recentBookings: { type: Array, default: () => [] },
  todayAppointments: { type: Array, default: () => [] },
});

const statusColor = (status) => {
  switch (status) {
    case 'confirmed': return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    case 'pending': return 'bg-amber-100 text-amber-800 border-amber-200';
    case 'completed': return 'bg-blue-100 text-blue-800 border-blue-200';
    case 'cancelled': return 'bg-red-100 text-red-800 border-red-200';
    default: return 'bg-neutral-100 text-neutral-800 border-neutral-200';
  }
};

const paymentStatusColor = (status) => {
  switch (status) {
    case 'paid': return 'text-emerald-700 bg-emerald-50 border-emerald-200';
    case 'unpaid': return 'text-amber-700 bg-amber-50 border-amber-200';
    case 'failed': return 'text-red-700 bg-red-50 border-red-200';
    default: return 'text-neutral-700 bg-neutral-50 border-neutral-200';
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Dashboard | Admin" />

    <div class="space-y-6">
      <!-- Welcome Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Dashboard</h1>
          <p class="text-xs text-neutral-500 mt-1">Real-time overview of bookings, appointments and revenue</p>
        </div>
        <div class="flex items-center gap-2">
          <Link 
            href="/admin/bookings" 
            class="px-4 py-2 bg-[#7a2e2e] text-white text-xs font-semibold rounded-xl hover:bg-[#52201f] transition shadow-xs flex items-center gap-1.5"
          >
            <span>View All Bookings</span>
            <ArrowUpRight class="w-3.5 h-3.5" />
          </Link>
        </div>
      </div>

      <!-- Stats Cards Grid -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
          <div class="flex items-center justify-between text-neutral-500 mb-2">
            <span class="text-xs font-medium uppercase tracking-wider">Total Bookings</span>
            <div class="w-8 h-8 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-600">
              <CalendarCheck class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-bold text-neutral-900">{{ stats.total_bookings }}</div>
          <div class="text-xs text-neutral-400 mt-1">{{ stats.today_bookings }} new today</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
          <div class="flex items-center justify-between text-amber-600 mb-2">
            <span class="text-xs font-medium uppercase tracking-wider">Pending Action</span>
            <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
              <AlertCircle class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-bold text-amber-700">{{ stats.pending_bookings }}</div>
          <div class="text-xs text-amber-600 mt-1">Requires specialist confirmation</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
          <div class="flex items-center justify-between text-emerald-600 mb-2">
            <span class="text-xs font-medium uppercase tracking-wider">Confirmed</span>
            <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
              <CheckCircle2 class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-bold text-emerald-700">{{ stats.confirmed_bookings }}</div>
          <div class="text-xs text-emerald-600 mt-1">Specialists assigned</div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
          <div class="flex items-center justify-between text-purple-600 mb-2">
            <span class="text-xs font-medium uppercase tracking-wider">Total Revenue</span>
            <div class="w-8 h-8 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600">
              <IndianRupee class="w-4 h-4" />
            </div>
          </div>
          <div class="text-2xl font-bold text-neutral-900">₹{{ Math.round(Number(stats.total_revenue)).toLocaleString('en-IN') }}</div>
          <div class="text-xs text-neutral-400 mt-1">From verified payments</div>
        </div>
      </div>

      <!-- Main Content Grid -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Recent Bookings Table (2 cols) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden">
          <div class="p-5 border-b border-neutral-100 flex items-center justify-between">
            <h2 class="font-bold text-neutral-900 text-sm">Recent Booking Inquiries</h2>
            <Link href="/admin/bookings" class="text-xs font-semibold text-[#b60a0a] hover:underline">View all →</Link>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider border-b border-neutral-200">
                <tr>
                  <th class="py-3 px-4">Booking ID / Client</th>
                  <th class="py-3 px-4">Service</th>
                  <th class="py-3 px-4">Date &amp; Slot</th>
                  <th class="py-3 px-4">Status</th>
                  <th class="py-3 px-4 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-neutral-100">
                <tr v-if="!recentBookings.length">
                  <td colspan="5" class="py-8 text-center text-neutral-400">No booking requests found yet.</td>
                </tr>
                <tr v-for="b in recentBookings" :key="b.id" class="hover:bg-neutral-50 transition">
                  <td class="py-3.5 px-4">
                    <div class="font-bold text-neutral-900">#{{ b.booking_number }}</div>
                    <div class="text-neutral-500">{{ b.name }} · {{ b.phone }}</div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="font-semibold text-neutral-800">{{ b.service_name }}</div>
                    <div class="text-neutral-400 text-[11px]">Pin: {{ b.pincode }}</div>
                  </td>
                  <td class="py-3.5 px-4">
                    <div class="text-neutral-800 font-medium">{{ b.booking_date ? new Date(b.booking_date).toLocaleDateString('en-IN', { day:'numeric', month:'short', year:'numeric' }) : 'N/A' }}</div>
                    <div class="text-neutral-500 text-[11px]">{{ b.time_slot_text || 'No preference' }}</div>
                  </td>
                  <td class="py-3.5 px-4">
                    <span 
                      class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border"
                      :class="statusColor(b.status)"
                    >
                      {{ b.status }}
                    </span>
                  </td>
                  <td class="py-3.5 px-4 text-right">
                    <Link 
                      :href="`/admin/bookings/${b.id}`" 
                      class="inline-flex items-center gap-1 text-xs font-semibold text-[#7a2e2e] hover:text-[#b60a0a] p-1.5 rounded-lg hover:bg-[#fbeded] transition"
                    >
                      <Eye class="w-3.5 h-3.5" />
                      <span>Details</span>
                    </Link>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Today's Schedule & Quick Stats (1 col) -->
        <div class="space-y-6">
          <!-- Today's Schedule -->
          <div class="bg-white rounded-2xl border border-neutral-200 shadow-xs p-5">
            <div class="flex items-center justify-between mb-4">
              <h2 class="font-bold text-neutral-900 text-sm flex items-center gap-2">
                <Clock class="w-4 h-4 text-[#7a2e2e]" />
                <span>Today's Appointments</span>
              </h2>
              <span class="text-xs bg-neutral-100 text-neutral-600 px-2 py-0.5 rounded-full font-semibold">
                {{ todayAppointments.length }}
              </span>
            </div>

            <div v-if="!todayAppointments.length" class="text-center py-6 text-neutral-400 text-xs">
              No visits scheduled for today.
            </div>

            <div v-else class="space-y-3">
              <div 
                v-for="apt in todayAppointments" 
                :key="apt.id" 
                class="p-3 rounded-xl bg-neutral-50 border border-neutral-100 flex items-start justify-between"
              >
                <div>
                  <div class="font-bold text-xs text-neutral-900">{{ apt.name }}</div>
                  <div class="text-[11px] text-neutral-500">{{ apt.service_name }}</div>
                  <div class="text-[11px] text-[#7a2e2e] font-medium mt-1">
                    Specialist: {{ apt.specialist_name || 'Unassigned' }}
                  </div>
                </div>
                <div class="text-right">
                  <span class="text-[11px] font-semibold text-neutral-700 bg-white px-2 py-0.5 rounded border border-neutral-200">
                    {{ apt.time_slot_text?.split('(')[0]?.trim() || 'Today' }}
                  </span>
                </div>
              </div>
            </div>
          </div>

          <!-- Quick Navigation Cards -->
          <div class="bg-gradient-to-br from-[#7a2e2e] to-[#52201f] text-white rounded-2xl p-5 shadow-xs">
            <h3 class="font-bold text-sm mb-1">Manage Availability</h3>
            <p class="text-xs text-neutral-200 mb-4">Add or modify morning, afternoon &amp; evening time slots for your specialists.</p>
            <div class="flex gap-2">
              <Link 
                href="/admin/time-slots" 
                class="px-3.5 py-2 bg-white text-[#7a2e2e] text-xs font-bold rounded-xl hover:bg-neutral-100 transition shadow-xs"
              >
                Configure Time Slots
              </Link>
              <Link 
                href="/admin/specialists" 
                class="px-3.5 py-2 bg-white/10 hover:bg-white/20 text-white text-xs font-semibold rounded-xl transition"
              >
                Specialists Team
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
