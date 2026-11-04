<script setup>
import { ref } from 'vue';
import { useForm, Head, router } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Plus, Edit, Trash2, Clock, CheckCircle2, User, X } from 'lucide-vue-next';

const props = defineProps({
  timeSlots: { type: Array, default: () => [] },
  specialists: { type: Array, default: () => [] },
});

const isModalOpen = ref(false);
const editingSlot = ref(null);

const form = useForm({
  period: 'Morning',
  start_time: '10:00 AM',
  end_time: '11:30 AM',
  specialist_id: null,
  day_of_week: '',
  specific_date: '',
  max_bookings: 1,
  is_active: true,
});

const openCreateModal = () => {
  editingSlot.value = null;
  form.reset();
  form.period = 'Morning';
  form.start_time = '10:00 AM';
  form.end_time = '11:30 AM';
  form.max_bookings = 1;
  form.is_active = true;
  isModalOpen.value = true;
};

const openEditModal = (slot) => {
  editingSlot.value = slot;
  form.period = slot.period;
  form.start_time = slot.start_time;
  form.end_time = slot.end_time;
  form.specialist_id = slot.specialist_id;
  form.day_of_week = slot.day_of_week || '';
  form.specific_date = slot.specific_date || '';
  form.max_bookings = slot.max_bookings;
  form.is_active = Boolean(slot.is_active);
  isModalOpen.value = true;
};

const closeModal = () => {
  isModalOpen.value = false;
  editingSlot.value = null;
  form.reset();
};

const saveSlot = () => {
  if (editingSlot.value) {
    form.put(`/admin/time-slots/${editingSlot.value.id}`, {
      onSuccess: () => closeModal(),
    });
  } else {
    form.post('/admin/time-slots', {
      onSuccess: () => closeModal(),
    });
  }
};

const deleteSlot = (id) => {
  if (confirm('Delete this time slot?')) {
    router.delete(`/admin/time-slots/${id}`);
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Time Slots | Admin" />

    <div class="space-y-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">Appointment Time Slots</h1>
          <p class="text-xs text-neutral-500 mt-1">Configure appointment slots by period (Morning, Afternoon, Evening) and assign specialist capacities</p>
        </div>
        <button 
          type="button" 
          class="px-4 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white text-xs font-bold rounded-xl transition flex items-center gap-2 shadow-xs"
          @click="openCreateModal"
        >
          <Plus class="w-4 h-4" />
          <span>Add New Time Slot</span>
        </button>
      </div>

      <!-- Slots Table -->
      <div class="bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-neutral-50 text-neutral-500 uppercase tracking-wider border-b border-neutral-200">
              <tr>
                <th class="py-3 px-4">Period</th>
                <th class="py-3 px-4">Time Window</th>
                <th class="py-3 px-4">Assigned Specialist / Employee</th>
                <th class="py-3 px-4">Max Capacity</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
              <tr v-if="!timeSlots.length">
                <td colspan="6" class="py-10 text-center text-neutral-400">
                  No time slots configured yet. Click "Add New Time Slot" to start.
                </td>
              </tr>
              <tr v-for="slot in timeSlots" :key="slot.id" class="hover:bg-neutral-50 transition">
                <td class="py-3.5 px-4">
                  <span 
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider"
                    :class="{
                      'bg-amber-100 text-amber-800': slot.period === 'Morning',
                      'bg-sky-100 text-sky-800': slot.period === 'Afternoon',
                      'bg-purple-100 text-purple-800': slot.period === 'Evening',
                    }"
                  >
                    {{ slot.period }}
                  </span>
                </td>
                <td class="py-3.5 px-4 font-semibold text-neutral-800 flex items-center gap-1.5">
                  <Clock class="w-3.5 h-3.5 text-neutral-400" />
                  <span>{{ slot.start_time }} - {{ slot.end_time }}</span>
                </td>
                <td class="py-3.5 px-4">
                  <span v-if="slot.specialist" class="font-semibold text-neutral-800 flex items-center gap-1">
                    <User class="w-3.5 h-3.5 text-[#7a2e2e]" />
                    <span>{{ slot.specialist.name }}</span>
                  </span>
                  <span v-else class="text-neutral-400 italic">General (Any available specialist)</span>
                </td>
                <td class="py-3.5 px-4">
                  <span class="font-bold text-neutral-800">{{ slot.max_bookings }}</span>
                  <span class="text-neutral-400"> booking{{ slot.max_bookings > 1 ? 's' : '' }} max</span>
                </td>
                <td class="py-3.5 px-4">
                  <span 
                    class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                    :class="slot.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-neutral-100 text-neutral-500'"
                  >
                    {{ slot.is_active ? 'Active' : 'Disabled' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-1">
                    <button 
                      type="button" 
                      class="p-1.5 text-neutral-600 hover:text-[#7a2e2e] hover:bg-neutral-100 rounded-lg transition"
                      @click="openEditModal(slot)"
                    >
                      <Edit class="w-3.5 h-3.5" />
                    </button>
                    <button 
                      type="button" 
                      class="p-1.5 text-neutral-400 hover:text-red-600 hover:bg-neutral-100 rounded-lg transition"
                      @click="deleteSlot(slot.id)"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <!-- Create / Edit Modal -->
      <div 
        v-if="isModalOpen" 
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-xs"
      >
        <div class="bg-white rounded-2xl border border-neutral-200 shadow-xl max-w-md w-full p-6 space-y-4">
          <div class="flex items-center justify-between pb-3 border-b border-neutral-100">
            <h3 class="font-bold text-base text-neutral-900">
              {{ editingSlot ? 'Edit Time Slot' : 'Create Time Slot' }}
            </h3>
            <button type="button" class="text-neutral-400 hover:text-neutral-700" @click="closeModal">
              <X class="w-5 h-5" />
            </button>
          </div>

          <form @submit.prevent="saveSlot" class="space-y-4">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Period</label>
              <select 
                v-model="form.period" 
                class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              >
                <option value="Morning">Morning (e.g. 09:00 AM - 12:00 PM)</option>
                <option value="Afternoon">Afternoon (e.g. 01:00 PM - 04:00 PM)</option>
                <option value="Evening">Evening (e.g. 04:30 PM - 07:30 PM)</option>
              </select>
            </div>

            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Start Time</label>
                <input 
                  v-model="form.start_time" 
                  type="text" 
                  required 
                  placeholder="10:00 AM" 
                  class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                />
              </div>
              <div>
                <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">End Time</label>
                <input 
                  v-model="form.end_time" 
                  type="text" 
                  required 
                  placeholder="11:30 AM" 
                  class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                />
              </div>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Assigned Specialist (Optional)</label>
              <select 
                v-model="form.specialist_id" 
                class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
              >
                <option :value="null">All Specialists (General Slot)</option>
                <option v-for="sp in specialists" :key="sp.id" :value="sp.id">
                  {{ sp.name }}
                </option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Max Bookings Allowed</label>
              <input 
                v-model="form.max_bookings" 
                type="number" 
                min="1" 
                max="50" 
                required 
                class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none font-bold"
              />
            </div>

            <div class="flex items-center gap-2 pt-1 select-none">
              <input type="checkbox" v-model="form.is_active" id="slotActive" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a] cursor-pointer" />
              <label for="slotActive" class="text-xs font-semibold text-neutral-700 cursor-pointer">Active and open for booking</label>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-neutral-100">
              <button 
                type="button" 
                class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl"
                @click="closeModal"
              >
                Cancel
              </button>
              <button 
                type="submit" 
                :disabled="form.processing"
                class="px-4 py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition shadow-xs"
              >
                {{ form.processing ? 'Saving...' : 'Save Slot' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
