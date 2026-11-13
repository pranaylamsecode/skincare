<script setup>
import { ref, computed } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { 
  Users, 
  Search, 
  Filter, 
  Phone, 
  Mail, 
  CheckCircle2, 
  Clock, 
  AlertCircle, 
  Calendar, 
  MapPin, 
  Award, 
  Car, 
  ExternalLink, 
  MessageSquare, 
  Trash2, 
  Eye, 
  UserPlus, 
  Check, 
  X,
  FileText
} from 'lucide-vue-next';

const props = defineProps({
  applications: {
    type: Array,
    default: () => [],
  },
  counts: {
    type: Object,
    default: () => ({
      total: 0,
      pending: 0,
      reviewing: 0,
      interview_scheduled: 0,
      approved: 0,
      rejected: 0,
    }),
  },
  filters: {
    type: Object,
    default: () => ({
      status: 'all',
      programme: 'all',
      search: '',
    }),
  },
});

const currentStatus = ref(props.filters?.status || 'all');
const currentProgramme = ref(props.filters?.programme || 'all');
const searchQuery = ref(props.filters?.search || '');
const selectedApplication = ref(null);
const isDetailModalOpen = ref(false);

const updateForm = useForm({
  status: 'pending',
  admin_notes: '',
});

const filteredApplications = computed(() => {
  return props.applications.filter(app => {
    // Status filter
    if (currentStatus.value !== 'all' && app.status !== currentStatus.value) {
      return false;
    }
    // Programme filter
    if (currentProgramme.value !== 'all') {
      const progs = Array.isArray(app.programmes) ? app.programmes : [];
      if (!progs.includes(currentProgramme.value)) {
        return false;
      }
    }
    // Search filter
    if (searchQuery.value.trim()) {
      const q = searchQuery.value.toLowerCase().trim();
      const matchName = `${app.first_name || ''} ${app.last_name || ''}`.toLowerCase().includes(q);
      const matchId = (app.registration_id || '').toLowerCase().includes(q);
      const matchMobile = (app.mobile || '').includes(q);
      const matchCity = (app.city || '').toLowerCase().includes(q);
      const matchPin = (app.pincode || '').includes(q);
      const matchEmail = (app.email || '').toLowerCase().includes(q);
      return matchName || matchId || matchMobile || matchCity || matchPin || matchEmail;
    }
    return true;
  });
});

const openDetailModal = (app) => {
  selectedApplication.value = app;
  updateForm.status = app.status;
  updateForm.admin_notes = app.admin_notes || '';
  isDetailModalOpen.value = true;
};

const closeDetailModal = () => {
  isDetailModalOpen.value = false;
  selectedApplication.value = null;
};

const handleStatusSave = () => {
  if (!selectedApplication.value) return;

  updateForm.put(`/admin/cme-onboardings/${selectedApplication.value.id}`, {
    preserveScroll: true,
    onSuccess: () => {
      // Refresh local selected app
      if (selectedApplication.value) {
        selectedApplication.value.status = updateForm.status;
        selectedApplication.value.admin_notes = updateForm.admin_notes;
      }
    },
  });
};

const promoteToSpecialist = (app) => {
  if (confirm(`Promote ${app.first_name} ${app.last_name} directly to the active Specialists roster?`)) {
    router.post(`/admin/cme-onboardings/${app.id}/promote`);
  }
};

const deleteApplication = (id) => {
  if (confirm('Are you sure you want to delete this CME application? This action cannot be undone.')) {
    router.delete(`/admin/cme-onboardings/${id}`);
  }
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'approved':
      return 'bg-emerald-100 text-emerald-800 border-emerald-200';
    case 'interview_scheduled':
      return 'bg-indigo-100 text-indigo-800 border-indigo-200';
    case 'reviewing':
      return 'bg-blue-100 text-blue-800 border-blue-200';
    case 'rejected':
      return 'bg-rose-100 text-rose-800 border-rose-200';
    default:
      return 'bg-amber-100 text-amber-800 border-amber-200';
  }
};

const getStatusLabel = (status) => {
  switch (status) {
    case 'approved': return 'Approved';
    case 'interview_scheduled': return 'Interview Scheduled';
    case 'reviewing': return 'In Review';
    case 'rejected': return 'Rejected';
    default: return 'Pending';
  }
};

const getWaLink = (app) => {
  const number = (app.is_whatsapp_same ? app.mobile : (app.whatsapp_number || app.mobile)).replace(/[^0-9]/g, '');
  const name = app.first_name || 'Candidate';
  const text = encodeURIComponent(`Hi ${name}, this is from FMZ Glow regarding your CME registration (${app.registration_id}). We would like to invite you for an information session.`);
  return `https://wa.me/91${number}?text=${text}`;
};
</script>

<template>
  <AdminLayout>
    <Head title="CME Onboarding Applications | Admin" />

    <div class="space-y-6">
      <!-- Page Title & Header Actions -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-bold tracking-tight text-neutral-900">
            CME Onboarding Applications
          </h1>
          <p class="text-xs text-neutral-500 mt-1">
            Review and onboard Certified Micro Entrepreneurs for Beauty &amp; Wellness, Nursing, and Healthcare.
          </p>
        </div>

        <div class="flex items-center gap-2">
          <a
            href="/become-a-cme"
            target="_blank"
            class="inline-flex items-center gap-1.5 px-4 py-2 bg-white border border-neutral-200 text-neutral-700 text-xs font-semibold rounded-xl hover:bg-neutral-50 transition shadow-xs"
          >
            <span>View Registration Form</span>
            <ExternalLink class="w-3.5 h-3.5 text-neutral-500" />
          </a>
        </div>
      </div>

      <!-- Stat Counter Cards -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
        <!-- Total -->
        <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs">
          <span class="text-[11px] font-bold uppercase tracking-wider text-neutral-400">Total Applicants</span>
          <p class="text-2xl font-bold text-neutral-900 mt-1">{{ counts.total || applications.length }}</p>
          <span class="text-[10px] text-neutral-500">First intake (150 places)</span>
        </div>

        <!-- Pending -->
        <div class="bg-white p-4 rounded-2xl border border-amber-200 shadow-xs bg-amber-50/20">
          <span class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Pending Review</span>
          <p class="text-2xl font-bold text-amber-900 mt-1">{{ counts.pending }}</p>
          <span class="text-[10px] text-amber-600 font-semibold">Needs initial screening</span>
        </div>

        <!-- Reviewing -->
        <div class="bg-white p-4 rounded-2xl border border-blue-200 shadow-xs bg-blue-50/20">
          <span class="text-[11px] font-bold uppercase tracking-wider text-blue-700">In Review</span>
          <p class="text-2xl font-bold text-blue-900 mt-1">{{ counts.reviewing }}</p>
          <span class="text-[10px] text-blue-600">Verification in progress</span>
        </div>

        <!-- Interview Scheduled -->
        <div class="bg-white p-4 rounded-2xl border border-indigo-200 shadow-xs bg-indigo-50/20">
          <span class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Interview Scheduled</span>
          <p class="text-2xl font-bold text-indigo-900 mt-1">{{ counts.interview_scheduled }}</p>
          <span class="text-[10px] text-indigo-600">WhatsApp meeting booked</span>
        </div>

        <!-- Approved -->
        <div class="bg-white p-4 rounded-2xl border border-emerald-200 shadow-xs bg-emerald-50/20">
          <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Approved CMEs</span>
          <p class="text-2xl font-bold text-emerald-900 mt-1">{{ counts.approved }}</p>
          <span class="text-[10px] text-emerald-600">Ready for onboarding</span>
        </div>
      </div>

      <!-- Filter Bar & Search -->
      <div class="bg-white p-4 rounded-2xl border border-neutral-200 shadow-xs space-y-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1.5 overflow-x-auto pb-1">
          <button
            v-for="st in [
              { key: 'all', label: 'All Applicants' },
              { key: 'pending', label: 'Pending' },
              { key: 'reviewing', label: 'In Review' },
              { key: 'interview_scheduled', label: 'Interview Scheduled' },
              { key: 'approved', label: 'Approved' },
              { key: 'rejected', label: 'Rejected' },
            ]"
            :key="st.key"
            type="button"
            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition whitespace-nowrap"
            :class="currentStatus === st.key ? 'bg-[#7a2e2e] text-white shadow-xs' : 'bg-neutral-100 text-neutral-600 hover:bg-neutral-200'"
            @click="currentStatus = st.key"
          >
            {{ st.label }}
          </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
          <!-- Search input -->
          <div class="md:col-span-8 relative">
            <Search class="w-4 h-4 text-neutral-400 absolute left-3.5 top-1/2 -translate-y-1/2" />
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by candidate name, mobile, registration ID, city, or PIN code..."
              class="w-full pl-10 pr-4 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
            />
          </div>

          <!-- Programme select filter -->
          <div class="md:col-span-4">
            <select
              v-model="currentProgramme"
              class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
            >
              <option value="all">All Programmes</option>
              <option value="Beauty & Wellness">Beauty &amp; Wellness</option>
              <option value="Nursing & Healthcare">Nursing &amp; Healthcare</option>
              <option value="Carer & Aged Care">Carer &amp; Aged Care</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Applications Table -->
      <div class="bg-white rounded-2xl border border-neutral-200 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs">
            <thead>
              <tr class="bg-neutral-50 border-b border-neutral-200 text-neutral-500 uppercase tracking-wider text-[10px] font-bold">
                <th class="py-3 px-4">Reg ID &amp; Date</th>
                <th class="py-3 px-4">Candidate</th>
                <th class="py-3 px-4">Programmes</th>
                <th class="py-3 px-4">Location</th>
                <th class="py-3 px-4">Contact</th>
                <th class="py-3 px-4">Qualification &amp; Vehicle</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-neutral-100">
              <tr 
                v-for="app in filteredApplications" 
                :key="app.id"
                class="hover:bg-neutral-50/60 transition cursor-pointer"
                @click="openDetailModal(app)"
              >
                <!-- Reg ID -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="font-mono font-bold text-neutral-900">{{ app.registration_id }}</div>
                  <div class="text-[10px] text-neutral-400 mt-0.5">
                    {{ app.created_at ? new Date(app.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'short', year: 'numeric' }) : 'Recent' }}
                  </div>
                </td>

                <!-- Candidate -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="font-bold text-neutral-900">{{ app.first_name }} {{ app.last_name }}</div>
                  <div class="text-[11px] text-neutral-500">
                    {{ app.gender || 'Female' }} · {{ app.age_group || 'Age not specified' }}
                  </div>
                </td>

                <!-- Programmes -->
                <td class="py-3.5 px-4">
                  <div class="flex flex-wrap gap-1">
                    <span 
                      v-for="(prog, idx) in (Array.isArray(app.programmes) ? app.programmes : [app.programmes])" 
                      :key="idx"
                      class="px-2 py-0.5 rounded-full text-[10px] font-semibold"
                      :class="prog === 'Beauty & Wellness' ? 'bg-[#fbeeed] text-[#7a2e2e]' : (prog === 'Nursing & Healthcare' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700')"
                    >
                      {{ prog }}
                    </span>
                  </div>
                </td>

                <!-- Location -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="font-semibold text-neutral-800">{{ app.city }}</div>
                  <div class="text-[10px] text-neutral-400 font-mono">{{ app.pincode }}, {{ app.state || 'Gujarat' }}</div>
                </td>

                <!-- Contact -->
                <td class="py-3.5 px-4 whitespace-nowrap" @click.stop>
                  <div class="flex items-center gap-1.5">
                    <span class="font-mono font-semibold text-neutral-800">+91 {{ app.mobile }}</span>
                    <a 
                      :href="getWaLink(app)" 
                      target="_blank" 
                      class="p-1 rounded-md bg-emerald-50 text-emerald-600 hover:bg-emerald-100 transition" 
                      title="Open WhatsApp chat"
                    >
                      <MessageSquare class="w-3.5 h-3.5" />
                    </a>
                  </div>
                  <div class="text-[10px] text-neutral-400 truncate max-w-[150px]">{{ app.email || 'No email provided' }}</div>
                </td>

                <!-- Qualification & Vehicle -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <div class="font-semibold text-neutral-700">
                    {{ app.nursing_qualification || app.highest_qualification || 'Graduate' }}
                  </div>
                  <div class="text-[10px] text-neutral-500 flex items-center gap-1">
                    <Car class="w-3 h-3 text-neutral-400" />
                    <span>{{ app.vehicle }} (Licence: {{ app.driving_licence }})</span>
                  </div>
                </td>

                <!-- Status Badge -->
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <span 
                    class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                    :class="getStatusBadgeClass(app.status)"
                  >
                    {{ getStatusLabel(app.status) }}
                  </span>
                </td>

                <!-- Actions -->
                <td class="py-3.5 px-4 text-right whitespace-nowrap" @click.stop>
                  <div class="flex items-center justify-end gap-1.5">
                    <button
                      type="button"
                      class="px-2.5 py-1 bg-neutral-100 hover:bg-neutral-200 text-neutral-700 rounded-lg text-[11px] font-semibold transition"
                      @click="openDetailModal(app)"
                    >
                      Details
                    </button>

                    <button
                      v-if="app.status === 'approved'"
                      type="button"
                      class="px-2.5 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-[11px] font-semibold transition flex items-center gap-1"
                      title="Add to Specialists Roster"
                      @click="promoteToSpecialist(app)"
                    >
                      <UserPlus class="w-3 h-3" />
                      <span>Promote</span>
                    </button>

                    <button
                      type="button"
                      class="p-1 text-neutral-300 hover:text-red-600 rounded transition"
                      title="Delete Application"
                      @click="deleteApplication(app.id)"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>

              <!-- Empty state -->
              <tr v-if="filteredApplications.length === 0">
                <td colspan="8" class="py-12 text-center text-neutral-400">
                  <Users class="w-8 h-8 mx-auto text-neutral-300 mb-2" />
                  <p class="font-semibold text-xs">No CME applications match your search criteria.</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Application Details & Status Modal -->
    <div 
      v-if="isDetailModalOpen && selectedApplication" 
      class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs overflow-y-auto"
      @click.self="closeDetailModal"
    >
      <div class="bg-white rounded-3xl max-w-2xl w-full shadow-2xl border border-neutral-200 overflow-hidden my-8 transform transition-all">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-neutral-100 flex items-center justify-between bg-neutral-50/60">
          <div>
            <div class="flex items-center gap-2">
              <h3 class="font-bold text-base text-neutral-900">
                {{ selectedApplication.first_name }} {{ selectedApplication.last_name }}
              </h3>
              <span 
                class="px-2 py-0.5 rounded-full text-[10px] font-bold border"
                :class="getStatusBadgeClass(selectedApplication.status)"
              >
                {{ getStatusLabel(selectedApplication.status) }}
              </span>
            </div>
            <p class="text-xs font-mono text-neutral-500 mt-0.5">
              ID: {{ selectedApplication.registration_id }} · Registered {{ selectedApplication.created_at ? new Date(selectedApplication.created_at).toLocaleDateString('en-IN', { day: 'numeric', month: 'long', year: 'numeric' }) : 'Recently' }}
            </p>
          </div>
          <button 
            type="button" 
            class="p-1.5 text-neutral-400 hover:text-neutral-700 rounded-lg hover:bg-neutral-100 transition"
            @click="closeDetailModal"
          >
            <X class="w-5 h-5" />
          </button>
        </div>

        <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
          <!-- 1. Programmes Applied -->
          <div class="bg-neutral-50 p-4 rounded-2xl border border-neutral-200">
            <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-500">Programmes of Interest</span>
            <div class="flex flex-wrap gap-1.5 mt-2">
              <span 
                v-for="(p, i) in (Array.isArray(selectedApplication.programmes) ? selectedApplication.programmes : [selectedApplication.programmes])" 
                :key="i"
                class="px-3 py-1 rounded-xl text-xs font-bold bg-[#7a2e2e] text-white"
              >
                {{ p }}
              </span>
            </div>
            <div v-if="selectedApplication.nursing_qualification" class="mt-2 text-xs text-neutral-700">
              Nursing / Health Qualification: <b>{{ selectedApplication.nursing_qualification }}</b>
            </div>
          </div>

          <!-- 2. Candidate Overview Grid -->
          <div class="grid grid-cols-2 md:grid-cols-3 gap-3 text-xs">
            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">Age &amp; Gender</span>
              <p class="font-semibold text-neutral-800 mt-0.5">{{ selectedApplication.age_group }} · {{ selectedApplication.gender }}</p>
            </div>

            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">Highest Qualification</span>
              <p class="font-semibold text-neutral-800 mt-0.5">{{ selectedApplication.highest_qualification }}</p>
            </div>

            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">Vehicle &amp; Licence</span>
              <p class="font-semibold text-neutral-800 mt-0.5">{{ selectedApplication.vehicle }} (Licence: {{ selectedApplication.driving_licence }})</p>
            </div>

            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">City &amp; PIN Code</span>
              <p class="font-semibold text-neutral-800 mt-0.5">{{ selectedApplication.city }} - {{ selectedApplication.pincode }}</p>
            </div>

            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">Mobile Phone</span>
              <p class="font-semibold font-mono text-neutral-800 mt-0.5">+91 {{ selectedApplication.mobile }}</p>
            </div>

            <div class="p-3 bg-neutral-50 rounded-xl border border-neutral-100">
              <span class="text-[10px] text-neutral-400 uppercase font-bold">WhatsApp Number</span>
              <p class="font-semibold font-mono text-neutral-800 mt-0.5">
                +91 {{ selectedApplication.is_whatsapp_same ? selectedApplication.mobile : selectedApplication.whatsapp_number }}
              </p>
            </div>
          </div>

          <!-- Referral Info -->
          <div v-if="selectedApplication.referral_source" class="p-3 bg-neutral-50 rounded-xl border border-neutral-100 text-xs">
            <span class="text-[10px] text-neutral-400 uppercase font-bold">Referral Source</span>
            <p class="font-semibold text-neutral-800 mt-0.5">
              {{ selectedApplication.referral_source }}
              <span v-if="selectedApplication.referral_name">· Referrer: {{ selectedApplication.referral_name }} ({{ selectedApplication.referral_contact }})</span>
              <span v-if="selectedApplication.referral_org">· Organisation: {{ selectedApplication.referral_org }}</span>
            </p>
          </div>

          <!-- Quick WhatsApp CTA -->
          <div class="flex items-center justify-between p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl">
            <div class="flex items-center gap-2.5">
              <MessageSquare class="w-5 h-5 text-emerald-600" />
              <div>
                <p class="text-xs font-bold text-emerald-900">Message on WhatsApp</p>
                <p class="text-[10px] text-emerald-700">Connect directly with candidate to arrange the information meeting</p>
              </div>
            </div>
            <a 
              :href="getWaLink(selectedApplication)" 
              target="_blank" 
              class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-bold transition flex items-center gap-1.5"
            >
              <span>Chat Now</span>
              <ExternalLink class="w-3.5 h-3.5" />
            </a>
          </div>

          <!-- 3. Update Status & Admin Notes Form -->
          <form @submit.prevent="handleStatusSave" class="space-y-4 pt-2 border-t border-neutral-100">
            <div>
              <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                Application Status
              </label>
              <select 
                v-model="updateForm.status" 
                class="w-full px-3 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
              >
                <option value="pending">Pending Review</option>
                <option value="reviewing">In Review (Verifying ID / Credentials)</option>
                <option value="interview_scheduled">Interview Scheduled (WhatsApp session booked)</option>
                <option value="approved">Approved / Selected for Intake</option>
                <option value="rejected">Rejected / Ineligible</option>
              </select>
            </div>

            <div>
              <label class="block text-[11px] font-bold text-neutral-500 uppercase tracking-wider mb-1.5">
                Internal Admin Notes
              </label>
              <textarea 
                v-model="updateForm.admin_notes" 
                rows="3" 
                placeholder="Add screening comments, interview feedback, or verification records..." 
                class="w-full px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none focus:border-[#7a2e2e] focus:bg-white transition"
              ></textarea>
            </div>

            <div class="flex items-center justify-between pt-2">
              <button
                v-if="selectedApplication.status === 'approved'"
                type="button"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5"
                @click="promoteToSpecialist(selectedApplication)"
              >
                <UserPlus class="w-4 h-4" />
                <span>Add to Specialist Roster</span>
              </button>
              <div v-else></div>

              <div class="flex items-center gap-2">
                <button 
                  type="button" 
                  class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl transition"
                  @click="closeDetailModal"
                >
                  Close
                </button>
                <button 
                  type="submit" 
                  :disabled="updateForm.processing"
                  class="px-5 py-2 bg-[#7a2e2e] text-white text-xs font-bold rounded-xl hover:bg-[#52201f] transition shadow-xs disabled:opacity-50"
                >
                  {{ updateForm.processing ? 'Saving...' : 'Update Application' }}
                </button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

