<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  ArrowLeft, 
  User, 
  Phone, 
  Mail, 
  MapPin, 
  Calendar, 
  Clock, 
  Sparkles, 
  Save, 
  Send,
  MessageSquare
} from 'lucide-vue-next';

const props = defineProps({
  booking: { type: Object, required: true },
  specialists: { type: Array, default: () => [] },
  timeSlots: { type: Array, default: () => [] },
});

const form = useForm({
  status: props.booking.status,
  specialist_id: props.booking.specialist_id,
  time_slot_id: props.booking.time_slot_id,
  time_slot_text: props.booking.time_slot_text || '',
  payment_status: props.booking.payment_status,
  total_amount: props.booking.total_amount,
  admin_notes: props.booking.admin_notes || '',
  send_notification: false,
});

const submit = () => {
  form.put(`/admin/bookings/${props.booking.id}`);
};

const openWhatsApp = () => {
  const phone = props.booking.phone.replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(`Hi ${props.booking.name}, this is FMZ Glow regarding your booking #${props.booking.booking_number} for ${props.booking.service_name}.`);
  window.open(`https://wa.me/${phone}?text=${msg}`, '_blank');
};
</script>

<template>
  <AdminLayout>
    <Head :title="`Booking #${booking.booking_number} | Admin`" />

    <div class="space-y-6 max-w-5xl mx-auto">
      <div class="flex items-center justify-between">
        <Link 
          href="/admin/bookings" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-[#7a2e2e] transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Bookings</span>
        </Link>

        <div class="flex items-center gap-2">
          <button 
            type="button" 
            class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-semibold flex items-center gap-1.5 transition shadow-xs"
            @click="openWhatsApp"
          >
            <MessageSquare class="w-3.5 h-3.5" />
            <span>Chat on WhatsApp</span>
          </button>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer & Service Overview (1 col) -->
        <div class="space-y-5">
          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs">
            <div class="text-xs uppercase font-bold tracking-wider text-neutral-400 mb-2">Booking Reference</div>
            <div class="text-xl font-bold font-mono text-neutral-900">#{{ booking.booking_number }}</div>
            <div class="text-xs text-neutral-500 mt-1">
              Received on {{ new Date(booking.created_at).toLocaleString('en-IN') }}
            </div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
            <h3 class="font-bold text-sm text-neutral-900 border-b border-neutral-100 pb-2">Customer Details</h3>
            <div class="space-y-2.5 text-xs">
              <div class="flex items-center gap-2.5 text-neutral-700">
                <User class="w-4 h-4 text-neutral-400" />
                <span class="font-semibold text-sm">{{ booking.name }}</span>
              </div>
              <div class="flex items-center gap-2.5 text-neutral-700">
                <Phone class="w-4 h-4 text-neutral-400" />
                <span>{{ booking.phone }}</span>
              </div>
              <div v-if="booking.email" class="flex items-center gap-2.5 text-neutral-700">
                <Mail class="w-4 h-4 text-neutral-400" />
                <span>{{ booking.email }}</span>
              </div>
              <div class="flex items-center gap-2.5 text-neutral-700">
                <MapPin class="w-4 h-4 text-neutral-400" />
                <span>Pincode: {{ booking.pincode }}</span>
              </div>
            </div>
          </div>

          <div class="bg-white p-5 rounded-2xl border border-neutral-200 shadow-xs space-y-3">
            <h3 class="font-bold text-sm text-neutral-900 border-b border-neutral-100 pb-2">Service Request</h3>
            <div class="space-y-2 text-xs">
              <div class="flex items-center gap-2 text-neutral-700 font-semibold text-sm">
                <Sparkles class="w-4 h-4 text-[#7a2e2e]" />
                <span>{{ booking.service_name }}</span>
              </div>
              <div class="flex items-center gap-2 text-neutral-600">
                <Calendar class="w-4 h-4 text-neutral-400" />
                <span>{{ booking.booking_date ? new Date(booking.booking_date).toLocaleDateString('en-IN', { weekday:'short', day:'numeric', month:'short', year:'numeric' }) : 'N/A' }}</span>
              </div>
              <div class="flex items-center gap-2 text-neutral-600">
                <Clock class="w-4 h-4 text-neutral-400" />
                <span>{{ booking.time_slot_text || 'No preference' }}</span>
              </div>
            </div>

            <div v-if="booking.skin_concern" class="pt-3 border-t border-neutral-100">
              <span class="text-neutral-400 text-[11px] block uppercase font-bold">Skin Concern Mentioned</span>
              <p class="text-xs text-neutral-700 mt-1 bg-neutral-50 p-3 rounded-xl italic border border-neutral-100">
                “{{ booking.skin_concern }}”
              </p>
            </div>
          </div>
        </div>

        <!-- Management Form (2 cols) -->
        <div class="lg:col-span-2">
          <div class="bg-white p-6 rounded-2xl border border-neutral-200 shadow-xs">
            <h2 class="text-base font-bold text-neutral-900 mb-5 pb-3 border-b border-neutral-100">
              Update Appointment &amp; Assignment
            </h2>

            <form @submit.prevent="submit" class="space-y-5">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Status -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Appointment Status</label>
                  <select 
                    v-model="form.status" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-semibold"
                  >
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="in_progress">In Progress</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>

                <!-- Specialist -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Assign Specialist</label>
                  <select 
                    v-model="form.specialist_id" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
                  >
                    <option :value="null">Unassigned</option>
                    <option v-for="sp in specialists" :key="sp.id" :value="sp.id">
                      {{ sp.name }} ({{ sp.phone || 'No phone' }})
                    </option>
                  </select>
                </div>

                <!-- Time Slot -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Assign Time Slot</label>
                  <select 
                    v-model="form.time_slot_id" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
                    @change="() => {
                      const slot = timeSlots.find(s => s.id === form.time_slot_id);
                      if (slot) form.time_slot_text = `${slot.period} (${slot.start_time} - ${slot.end_time})`;
                    }"
                  >
                    <option :value="null">Keep Custom Slot ({{ form.time_slot_text }})</option>
                    <option v-for="slot in timeSlots" :key="slot.id" :value="slot.id">
                      {{ slot.period }}: {{ slot.start_time }} - {{ slot.end_time }}
                    </option>
                  </select>
                </div>

                <!-- Custom Time Slot String -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Slot Display Text</label>
                  <input 
                    v-model="form.time_slot_text" 
                    type="text" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
                  />
                </div>

                <!-- Total Amount -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Total Price (₹)</label>
                  <input 
                    v-model="form.total_amount" 
                    type="number" 
                    step="0.01" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-bold"
                  />
                </div>

                <!-- Payment Status -->
                <div>
                  <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Payment Status</label>
                  <select 
                    v-model="form.payment_status" 
                    class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-semibold"
                  >
                    <option value="unpaid">Unpaid</option>
                    <option value="pending">Pending</option>
                    <option value="paid">Paid</option>
                    <option value="failed">Failed</option>
                    <option value="refunded">Refunded</option>
                  </select>
                </div>
              </div>

              <!-- Admin Notes -->
              <div>
                <label class="block text-xs font-bold text-neutral-700 mb-1.5 uppercase">Internal Admin Notes</label>
                <textarea 
                  v-model="form.admin_notes" 
                  rows="3" 
                  placeholder="Notes for the specialist, parking info, medical allergies..." 
                  class="w-full px-3 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
                ></textarea>
              </div>

              <!-- Send Notification Checkbox -->
              <div class="p-3.5 rounded-xl bg-[#fbeded] border border-[#f1e0dc] flex items-center justify-between">
                <div>
                  <div class="text-xs font-bold text-[#7a2e2e]">Send Email Notification to Client</div>
                  <div class="text-[11px] text-neutral-600">Dispatches confirmation email using the backend email template</div>
                </div>
                <input 
                  type="checkbox" 
                  v-model="form.send_notification" 
                  class="rounded text-[#b60a0a] focus:ring-[#b60a0a] w-4 h-4 cursor-pointer"
                />
              </div>

              <div class="flex items-center justify-end gap-3 pt-3">
                <Link 
                  href="/admin/bookings" 
                  class="px-4 py-2.5 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl transition"
                >
                  Cancel
                </Link>
                <button 
                  type="submit" 
                  :disabled="form.processing"
                  class="px-5 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-xs disabled:opacity-50"
                >
                  <Save class="w-4 h-4" />
                  <span>{{ form.processing ? 'Saving...' : 'Save Changes' }}</span>
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
