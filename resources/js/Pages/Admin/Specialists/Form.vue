<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useForm, Head, Link } from '@inertiajs/vue3';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { ArrowLeft, Save, Plus, Trash2, MapPin, Search, Navigation, Compass, Layers, CheckCircle } from 'lucide-vue-next';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const props = defineProps({
  specialist: { type: Object, default: null },
  staffUser: { type: Object, default: null }
});

const isEditing = !!props.specialist;

const langInput = ref('');
const searchLocation = ref('');
const isSearching = ref(false);
const isCalculating = ref(false);
const detectedPincodes = ref([]);
const autoSyncPincodes = ref(true);
const mapContainer = ref(null);

let map = null;
let marker = null;
let circle = null;

const form = useForm({
  name: props.specialist?.name || '',
  qualification: props.specialist?.qualification || '',
  location: props.specialist?.location || '',
  pincode: props.specialist?.pincode || '',
  latitude: props.specialist?.latitude ?? 23.0304,
  longitude: props.specialist?.longitude ?? 72.5178,
  service_radius_km: props.specialist?.service_radius_km ?? 10,
  serviceable_pincodes: props.specialist?.serviceable_pincodes || '',
  city: props.specialist?.city || 'ahmedabad',
  languages: props.specialist?.languages || ['Gujarati', 'Hindi', 'English'],
  badge: props.specialist?.badge || 'Verified Specialist',
  phone: props.specialist?.phone || '',
  email: props.specialist?.email || '',
  photo: props.specialist?.photo || '',
  is_active: props.specialist?.is_active ?? true,
  sort_order: props.specialist?.sort_order ?? 0,
  staff_email: props.staffUser?.email || props.specialist?.email || '',
  staff_password: '',
});

// Custom Ruby-Gold map marker icon
const customMarker = L.divIcon({
  className: 'custom-map-pin',
  html: `
    <div style="position: relative; width: 34px; height: 34px;">
      <div style="background: linear-gradient(135deg, #7a2e2e 0%, #4a1515 100%); width: 32px; height: 32px; border-radius: 50% 50% 50% 0; transform: rotate(-45deg); display: flex; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(122, 46, 46, 0.45); border: 2.5px solid #ffffff;">
        <div style="width: 10px; height: 10px; background: #fbbf24; border-radius: 50%; transform: rotate(45deg);"></div>
      </div>
    </div>
  `,
  iconSize: [34, 34],
  iconAnchor: [17, 34],
  popupAnchor: [0, -34],
});

const initMap = () => {
  if (!mapContainer.value || map) return;

  const lat = parseFloat(form.latitude) || 23.0304;
  const lng = parseFloat(form.longitude) || 72.5178;

  map = L.map(mapContainer.value, {
    center: [lat, lng],
    zoom: 12,
    zoomControl: true,
  });

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 19,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  marker = L.marker([lat, lng], {
    draggable: true,
    icon: customMarker,
  }).addTo(map);

  marker.bindPopup(`<b>${form.name || 'Specialist Base Location'}</b><br>Drag pin or click map to move`).openPopup();

  circle = L.circle([lat, lng], {
    radius: (form.service_radius_km || 10) * 1000,
    color: '#7a2e2e',
    fillColor: '#7a2e2e',
    fillOpacity: 0.12,
    weight: 2,
    dashArray: '6, 6',
  }).addTo(map);

  marker.on('dragend', (e) => {
    const pos = e.target.getLatLng();
    updateLocation(pos.lat, pos.lng);
  });

  map.on('click', (e) => {
    updateLocation(e.latlng.lat, e.latlng.lng);
  });

  // Calculate initial radius pincodes
  fetchPincodesInRadius(lat, lng, form.service_radius_km);
};

const updateLocation = (lat, lng, panMap = false) => {
  form.latitude = parseFloat(lat.toFixed(6));
  form.longitude = parseFloat(lng.toFixed(6));

  if (marker) {
    marker.setLatLng([lat, lng]);
  }
  if (circle) {
    circle.setLatLng([lat, lng]);
  }
  if (panMap && map) {
    map.setView([lat, lng], 13);
  }

  fetchPincodesInRadius(form.latitude, form.longitude, form.service_radius_km);
};

const setRadius = (km) => {
  form.service_radius_km = km;
  if (circle) {
    circle.setRadius(km * 1000);
  }
  if (map && circle) {
    map.fitBounds(circle.getBounds(), { padding: [30, 30] });
  }
  fetchPincodesInRadius(form.latitude, form.longitude, km);
};

const fetchPincodesInRadius = async (lat, lng, radius) => {
  isCalculating.value = true;
  try {
    const res = await fetch(`/api/pincodes/in-radius?latitude=${lat}&longitude=${lng}&radius=${radius}`);
    const data = await res.json();
    if (data.success) {
      detectedPincodes.value = data.items || [];
      if (autoSyncPincodes.value && data.pincode_list) {
        form.serviceable_pincodes = data.pincode_list;
      }
    }
  } catch (err) {
    console.error('Failed to fetch radius pincodes', err);
  } finally {
    isCalculating.value = false;
  }
};

const geocodeLocation = async () => {
  if (!searchLocation.value.trim()) return;
  isSearching.value = true;
  try {
    const res = await fetch(`/api/pincodes/geocode?query=${encodeURIComponent(searchLocation.value.trim())}`);
    const data = await res.json();
    if (data.success) {
      updateLocation(data.latitude, data.longitude, true);
      if (data.pincode && !form.pincode) {
        form.pincode = data.pincode;
      }
      if (data.area_name && !form.location) {
        form.location = data.area_name;
      }
    } else {
      alert('Location not found. Try entering a 6-digit pincode or well-known neighborhood.');
    }
  } catch (err) {
    alert('Search error. Click directly on the map to set your location pin.');
  } finally {
    isSearching.value = false;
  }
};

const onCityChange = () => {
  const cityCoords = {
    ahmedabad: [23.0304, 72.5178],
    gandhinagar: [23.2156, 72.6369],
    surat: [21.1702, 72.8311],
    rajkot: [22.3039, 70.8022],
  };
  if (cityCoords[form.city]) {
    const [cLat, cLng] = cityCoords[form.city];
    updateLocation(cLat, cLng, true);
  }
};

const removePincodeFromList = (pin) => {
  const list = form.serviceable_pincodes.split(',').map(s => s.trim()).filter(Boolean);
  const filtered = list.filter(p => p !== pin);
  form.serviceable_pincodes = filtered.join(', ');
};

const applyDetectedPincodes = () => {
  const pins = detectedPincodes.value.map(p => p.pincode);
  form.serviceable_pincodes = pins.join(', ');
};

const addLanguage = () => {
  if (langInput.value.trim() && !form.languages.includes(langInput.value.trim())) {
    form.languages.push(langInput.value.trim());
    langInput.value = '';
  }
};

const removeLanguage = (idx) => {
  form.languages.splice(idx, 1);
};

const submit = () => {
  if (isEditing) {
    form.put(`/admin/specialists/${props.specialist.id}`);
  } else {
    form.post('/admin/specialists');
  }
};

onMounted(() => {
  nextTick(() => {
    initMap();
  });
});
</script>

<template>
  <AdminLayout>
    <Head :title="`${isEditing ? 'Edit Specialist' : 'Add Specialist'} | Admin`" />

    <div class="space-y-6 max-w-4xl mx-auto pb-12">
      <div class="flex items-center justify-between">
        <Link 
          href="/admin/specialists" 
          class="inline-flex items-center gap-2 text-xs font-semibold text-neutral-600 hover:text-[#7a2e2e] transition"
        >
          <ArrowLeft class="w-4 h-4" />
          <span>Back to Specialists</span>
        </Link>
      </div>

      <div class="bg-white p-6 md:p-8 rounded-2xl border border-neutral-200 shadow-xs">
        <h1 class="text-xl font-bold text-neutral-900 mb-6 pb-3 border-b border-neutral-100">
          {{ isEditing ? `Edit Specialist: ${specialist.name}` : 'Add New Specialist' }}
        </h1>

        <form @submit.prevent="submit" class="space-y-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Full Name</label>
              <input 
                v-model="form.name" 
                type="text" 
                required 
                placeholder="e.g. Dhruvi Jyani" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
              <span v-if="form.errors.name" class="text-xs text-red-600 mt-1 block">{{ form.errors.name }}</span>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Qualification / Background</label>
              <input 
                v-model="form.qualification" 
                type="text" 
                required 
                placeholder="e.g. B.Sc. in Biochemistry" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">City</label>
              <select 
                v-model="form.city" 
                @change="onCityChange"
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none font-semibold"
              >
                <option value="ahmedabad">Ahmedabad</option>
                <option value="gandhinagar">Gandhinagar</option>
                <option value="surat">Surat</option>
                <option value="rajkot">Rajkot</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Location Description</label>
              <input 
                v-model="form.location" 
                type="text" 
                required 
                placeholder="e.g. Satellite & SG Highway Area" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Primary Home Base Pincode</label>
              <input 
                v-model="form.pincode" 
                type="text" 
                placeholder="380015" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Phone Number (Internal)</label>
              <input 
                v-model="form.phone" 
                type="text" 
                placeholder="+91..." 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>
          </div>

          <!-- MAP & RADIUS SERVICE AREA PICKER -->
          <div class="border border-neutral-200 rounded-2xl p-5 bg-gradient-to-b from-neutral-50 to-white space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
              <div>
                <h3 class="text-sm font-bold text-neutral-900 flex items-center gap-2">
                  <MapPin class="w-4 h-4 text-[#7a2e2e]" />
                  <span>Interactive Service Area & Radius Map</span>
                </h3>
                <p class="text-xs text-neutral-500 mt-0.5">
                  Pick the employee's base location and set their coverage radius. All matching pincodes are calculated automatically.
                </p>
              </div>

              <!-- Radius Selector Buttons -->
              <div class="flex items-center gap-1.5 bg-neutral-200/70 p-1 rounded-xl self-start sm:self-auto">
                <button 
                  v-for="r in [5, 10, 15, 20, 25]" 
                  :key="r"
                  type="button"
                  @click="setRadius(r)"
                  :class="[
                    'px-2.5 py-1 text-xs font-bold rounded-lg transition-all',
                    form.service_radius_km === r 
                      ? 'bg-[#7a2e2e] text-white shadow-xs' 
                      : 'text-neutral-700 hover:text-neutral-900 hover:bg-neutral-100'
                  ]"
                >
                  {{ r }} km
                </button>
              </div>
            </div>

            <!-- Search Area / Jump on Map -->
            <div class="flex gap-2">
              <div class="relative flex-1">
                <Search class="w-3.5 h-3.5 text-neutral-400 absolute left-3.5 top-3" />
                <input 
                  v-model="searchLocation" 
                  type="text" 
                  placeholder="Jump to area or pincode (e.g. Vastrapur, Bopal, 380015)..."
                  @keyup.enter.prevent="geocodeLocation"
                  class="w-full pl-9 pr-3.5 py-2 text-xs bg-white border border-neutral-200 rounded-xl focus:border-[#7a2e2e] outline-none"
                />
              </div>
              <button 
                type="button" 
                @click="geocodeLocation" 
                :disabled="isSearching"
                class="px-4 py-2 bg-neutral-800 hover:bg-neutral-900 text-white text-xs font-semibold rounded-xl flex items-center gap-1.5 shrink-0"
              >
                <Compass class="w-3.5 h-3.5" />
                <span>{{ isSearching ? 'Searching...' : 'Find on Map' }}</span>
              </button>
            </div>

            <!-- Map Container -->
            <div class="relative rounded-xl overflow-hidden border border-neutral-200 shadow-inner">
              <div ref="mapContainer" class="w-full h-[360px] z-0"></div>
              
              <!-- Map Floating Overlay -->
              <div class="absolute bottom-3 left-3 z-[400] bg-white/95 backdrop-blur-xs px-3 py-1.5 rounded-lg shadow-sm border border-neutral-200 text-[11px] text-neutral-700 flex items-center gap-2">
                <Navigation class="w-3 h-3 text-[#7a2e2e]" />
                <span>Lat: <strong>{{ form.latitude }}</strong>, Lng: <strong>{{ form.longitude }}</strong> | Radius: <strong>{{ form.service_radius_km }} km</strong></span>
              </div>
            </div>

            <!-- Calculated Pincodes Feedback -->
            <div class="p-4 bg-emerald-50/70 border border-emerald-200/80 rounded-xl space-y-2.5">
              <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                  <CheckCircle class="w-4 h-4 text-emerald-700" />
                  <span class="text-xs font-bold text-emerald-950">
                    {{ detectedPincodes.length }} Pincodes Automatically Detected in {{ form.service_radius_km }} km Radius
                  </span>
                  <span v-if="isCalculating" class="text-[10px] text-emerald-600 animate-pulse">Calculating...</span>
                </div>

                <button 
                  type="button" 
                  @click="applyDetectedPincodes" 
                  class="text-[11px] font-bold text-emerald-800 hover:underline flex items-center gap-1"
                >
                  Sync to Serviceable List
                </button>
              </div>

              <!-- Pincode Chips -->
              <div class="flex flex-wrap gap-1.5 max-h-24 overflow-y-auto pr-1">
                <div 
                  v-for="item in detectedPincodes" 
                  :key="item.pincode"
                  class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-white border border-emerald-200 text-emerald-900 rounded-lg text-xs shadow-2xs"
                >
                  <span class="font-mono font-bold">{{ item.pincode }}</span>
                  <span class="text-[10px] text-emerald-700">({{ item.area_name.split('/')[0] }} &bull; {{ item.distance_km }}km)</span>
                  <button 
                    type="button" 
                    @click="removePincodeFromList(item.pincode)" 
                    title="Remove this pincode"
                    class="text-neutral-400 hover:text-red-600 font-bold ml-1 text-sm leading-none"
                  >
                    &times;
                  </button>
                </div>
              </div>
            </div>

            <!-- Raw Serviceable Pincodes Input -->
            <div>
              <div class="flex items-center justify-between mb-1.5">
                <label class="block text-xs font-bold text-neutral-700 uppercase">
                  Final Serviceable Pincodes (Saved for Customer Bookings)
                </label>
                <label class="flex items-center gap-1.5 text-[11px] text-neutral-600 cursor-pointer">
                  <input type="checkbox" v-model="autoSyncPincodes" class="rounded text-[#7a2e2e] accent-[#7a2e2e]" />
                  <span>Keep auto-synced with map radius</span>
                </label>
              </div>
              <input 
                v-model="form.serviceable_pincodes" 
                type="text" 
                placeholder="380015, 380054, 380058..." 
                class="w-full px-3.5 py-2.5 text-xs bg-white border border-neutral-200 rounded-xl focus:border-[#7a2e2e] outline-none font-mono"
              />
              <p class="text-[11px] text-neutral-500 mt-1">
                You can also manually add or remove any custom 6-digit pincodes separated by comma.
              </p>
            </div>
          </div>

          <!-- Other Details -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 pt-2">
            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Badge Title</label>
              <input 
                v-model="form.badge" 
                type="text" 
                placeholder="Verified Specialist or Senior Aesthetician" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>

            <div>
              <label class="block text-xs font-bold text-neutral-700 uppercase mb-1.5">Photo Path / URL</label>
              <input 
                v-model="form.photo" 
                type="text" 
                placeholder="images/Dhruvi.png" 
                class="w-full px-3.5 py-2.5 text-xs bg-neutral-50 border border-neutral-200 rounded-xl focus:bg-white focus:border-[#7a2e2e] outline-none"
              />
            </div>
          </div>

          <!-- Languages Spoken -->
          <div class="border-t border-neutral-100 pt-5">
            <label class="block text-xs font-bold text-neutral-700 uppercase mb-2">Languages Spoken</label>
            <div class="flex gap-2 mb-3">
              <input 
                v-model="langInput" 
                type="text" 
                placeholder="e.g. Gujarati, Hindi, English" 
                class="flex-1 px-3.5 py-2 text-xs bg-neutral-50 border border-neutral-200 rounded-xl outline-none"
                @keyup.enter.prevent="addLanguage"
              />
              <button 
                type="button" 
                class="px-4 py-2 bg-neutral-800 text-white text-xs font-semibold rounded-xl hover:bg-neutral-900 flex items-center gap-1"
                @click="addLanguage"
              >
                <Plus class="w-3.5 h-3.5" />
                <span>Add</span>
              </button>
            </div>

            <div class="flex flex-wrap gap-2">
              <span 
                v-for="(lang, idx) in form.languages" 
                :key="idx" 
                class="inline-flex items-center gap-1.5 px-3 py-1 bg-[#fbeded] text-[#7a2e2e] font-semibold text-xs rounded-full"
              >
                <span>{{ lang }}</span>
                <button type="button" @click="removeLanguage(idx)" class="hover:text-red-700 font-bold">&times;</button>
              </span>
            </div>
          </div>

          <!-- Staff Employee Login Account -->
          <div class="p-5 bg-amber-50/60 rounded-2xl border border-amber-200/80 space-y-4">
            <div>
              <h3 class="text-xs font-bold text-amber-950 uppercase flex items-center gap-2">
                <span>Staff Portal Login Account</span>
              </h3>
              <p class="text-[11px] text-amber-800/80 mt-0.5">
                Allows this specialist to log in to the backend staff portal, view their assigned bookings, and add/manage their own time slots.
              </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">Staff Login Email</label>
                <input 
                  v-model="form.staff_email" 
                  type="email" 
                  placeholder="specialist@fmzglow.com" 
                  class="w-full px-3.5 py-2.5 text-xs bg-white border border-neutral-200 rounded-xl focus:border-[#7a2e2e] outline-none"
                />
              </div>

              <div>
                <label class="block text-xs font-bold text-neutral-700 uppercase mb-1">
                  {{ staffUser ? 'Change Password (Leave blank to keep)' : 'Login Password (Default: staff123)' }}
                </label>
                <input 
                  v-model="form.staff_password" 
                  type="password" 
                  placeholder="••••••••" 
                  class="w-full px-3.5 py-2.5 text-xs bg-white border border-neutral-200 rounded-xl focus:border-[#7a2e2e] outline-none font-mono"
                />
              </div>
            </div>
          </div>

          <div class="border-t border-neutral-100 pt-5 flex items-center justify-between">
            <label class="flex items-center gap-2 cursor-pointer text-xs font-semibold text-neutral-700 select-none">
              <input type="checkbox" v-model="form.is_active" class="w-4 h-4 rounded text-[#b60a0a] shrink-0 accent-[#b60a0a]" />
              <span>Visible on Website</span>
            </label>

            <div class="flex items-center gap-3">
              <Link 
                href="/admin/specialists" 
                class="px-4 py-2 text-xs font-semibold text-neutral-600 hover:bg-neutral-100 rounded-xl"
              >
                Cancel
              </Link>
              <button 
                type="submit" 
                :disabled="form.processing"
                class="px-5 py-2.5 bg-[#7a2e2e] hover:bg-[#52201f] text-white font-bold text-xs rounded-xl flex items-center gap-2 shadow-xs"
              >
                <Save class="w-4 h-4" />
                <span>{{ form.processing ? 'Saving...' : 'Save Specialist' }}</span>
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AdminLayout>
</template>
