<script setup>
import { ref, reactive, watch, onMounted } from 'vue';

const props = defineProps({
  services: {
    type: Array,
    default: () => []
  },
  specialists: {
    type: Array,
    default: () => []
  },
  timeSlots: {
    type: Array,
    default: () => []
  },
  preselectedService: {
    type: String,
    default: 'Nu Skin LumiSpa Glow Facial'
  },
  preselectedSpecialist: {
    type: Object,
    default: null
  }
});

const todayDate = new Date().toISOString().split('T')[0];

const form = reactive({
  name: '',
  phone: '',
  email: '',
  pin: '',
  service_name: 'Nu Skin LumiSpa Glow Facial',
  service_id: null,
  specialist_id: null,
  specialist_name: '',
  date: todayDate,
  time_slot_id: null,
  time_slot_text: '',
  msg: '',
  consent: true,
  payment_method: 'after_visit' // 'after_visit' or 'razorpay'
});

const availableSlots = ref([]);
const loadingSlots = ref(false);
const submitting = ref(false);
const successMessage = ref('');
const bookingResult = ref(null);
const errorMessage = ref('');

// Fetch slots from API based on date and specialist
const fetchSlots = async () => {
  if (!form.date) return;
  loadingSlots.value = true;
  try {
    const params = new URLSearchParams({
      date: form.date,
      ...(form.specialist_id ? { specialist_id: form.specialist_id } : {})
    });
    const res = await fetch(`/api/available-slots?${params.toString()}`);
    const data = await res.json();
    if (data.success && data.slots) {
      availableSlots.value = data.slots;
      // If currently selected slot is not available or empty, pick the first available
      if (!form.time_slot_id || !data.slots.some(s => s.id === form.time_slot_id && s.is_available)) {
        const firstAvail = data.slots.find(s => s.is_available);
        if (firstAvail) {
          form.time_slot_id = firstAvail.id;
          form.time_slot_text = firstAvail.display;
        } else {
          form.time_slot_id = null;
          form.time_slot_text = 'No preference';
        }
      }
    }
  } catch (e) {
    console.error('Error fetching slots:', e);
  } finally {
    loadingSlots.value = false;
  }
};

watch(() => form.date, fetchSlots);
watch(() => form.specialist_id, fetchSlots);

// Watch for preselected service
watch(() => props.preselectedService, (newVal) => {
  if (newVal) {
    form.service_name = newVal;
    const match = props.services.find(s => s.title === newVal);
    if (match) {
      form.service_id = match.id;
    }
  }
}, { immediate: true });

// Watch for preselected specialist
watch(() => props.preselectedSpecialist, (newVal) => {
  if (newVal) {
    form.specialist_id = newVal.id;
    form.specialist_name = newVal.name;
    form.msg = 'Preferred specialist: ' + newVal.name;
    fetchSlots();
  }
}, { immediate: true });

onMounted(() => {
  fetchSlots();
});

const selectSlot = (slot) => {
  if (!slot.is_available) return;
  form.time_slot_id = slot.id;
  form.time_slot_text = slot.display;
};

// Handle form submission
const submitBooking = async () => {
  submitting.value = true;
  errorMessage.value = '';
  successMessage.value = '';

  try {
    const res = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        name: form.name,
        phone: form.phone,
        email: form.email,
        pincode: form.pin,
        service_name: form.service_name,
        service_id: form.service_id,
        specialist_id: form.specialist_id,
        specialist_name: form.specialist_name,
        date: form.date,
        time_slot_id: form.time_slot_id,
        time_slot_text: form.time_slot_text,
        msg: form.msg,
        consent: form.consent,
        payment_method: form.payment_method
      })
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.errors) {
        const errorList = Object.values(data.errors).flat().join(' ');
        errorMessage.value = errorList || 'Please check your inputs.';
      } else {
        errorMessage.value = data.message || 'Something went wrong. Please try again.';
      }
      submitting.value = false;
      return;
    }

    bookingResult.value = data.booking;
    successMessage.value = data.message || 'Your booking request has been received!';

    // If online payment chosen (Razorpay)
    if (form.payment_method === 'razorpay' && data.booking && data.booking.id) {
      initiateRazorpayPayment(data.booking.id);
    }

  } catch (err) {
    console.error('Booking submission error:', err);
    errorMessage.value = 'Network error. Please try again or message us on WhatsApp.';
  } finally {
    submitting.value = false;
  }
};

const initiateRazorpayPayment = async (bookingId) => {
  try {
    const orderRes = await fetch('/api/payment/create-order', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ booking_id: bookingId })
    });
    const orderData = await orderRes.json();

    if (!orderData.success) {
      // Test mode notice
      if (orderData.mock_allowed) {
        successMessage.value += ' (Online payment gateway is in test mode; you can pay after your visit via UPI or cash).';
      } else {
        errorMessage.value = orderData.message;
      }
      return;
    }

    if (window.Razorpay) {
      const options = {
        key: orderData.key,
        amount: orderData.amount,
        currency: orderData.currency,
        name: 'FMZ Glow',
        description: `Booking #${orderData.booking_number}`,
        image: '/images/FMZ logo.jpg',
        order_id: orderData.order_id,
        prefill: {
          name: orderData.client_name,
          email: orderData.client_email,
          contact: orderData.client_phone
        },
        theme: {
          color: '#7a2e2e'
        },
        handler: async function (response) {
          // Verify on backend
          await verifyPaymentOnServer(bookingId, response);
        }
      };
      const rzp = new window.Razorpay(options);
      rzp.open();
    }
  } catch (e) {
    console.error('Razorpay init error:', e);
  }
};

const verifyPaymentOnServer = async (bookingId, razorpayResponse) => {
  try {
    const verifyRes = await fetch('/api/payment/verify', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        booking_id: bookingId,
        razorpay_order_id: razorpayResponse.razorpay_order_id,
        razorpay_payment_id: razorpayResponse.razorpay_payment_id,
        razorpay_signature: razorpayResponse.razorpay_signature
      })
    });
    const verifyData = await verifyRes.json();
    if (verifyData.success) {
      successMessage.value = 'Payment successful! Your booking is fully confirmed. An email receipt has been sent.';
    }
  } catch (e) {
    console.error('Payment verification error:', e);
  }
};
</script>

<template>
  <section class="booking" id="book">
    <div class="wrap book-grid">
      <div>
        <div class="eyebrow">Book a visit</div>
        <h2>Ready for your glow?</h2>
        <p>Send us your details and preferred service. Our team will contact you on WhatsApp to confirm your specialist, time and total price in writing.</p>
        
        <div v-if="bookingResult" style="margin-top:24px;background:rgba(255,255,255,0.08);border-radius:16px;padding:20px;border:1px solid rgba(255,255,255,0.15)">
          <div style="font-size:0.78rem;text-transform:uppercase;letter-spacing:0.1em;color:var(--tan);font-weight:700">Latest Booking</div>
          <div style="font-size:1.3rem;font-weight:700;margin:6px 0;color:#fff">#{{ bookingResult.booking_number }}</div>
          <div style="font-size:0.84rem;color:#f3e4e0">
            <div><b>Service:</b> {{ bookingResult.service }}</div>
            <div><b>Date:</b> {{ bookingResult.date }}</div>
            <div><b>Time:</b> {{ bookingResult.time }}</div>
            <div v-if="Number(bookingResult.total_amount) > 0"><b>Price:</b> ₹{{ Math.round(Number(bookingResult.total_amount)).toLocaleString('en-IN') }}</div>
          </div>
        </div>
      </div>

      <div class="form">
        <form @submit.prevent="submitBooking">
          <div class="form-grid">
            <div class="field">
              <label for="name">Your name</label>
              <input
                id="name"
                v-model="form.name"
                type="text"
                autocomplete="name"
                placeholder="e.g. Mansi Joshi"
                required
              >
            </div>

            <div class="field">
              <label for="phone">WhatsApp number</label>
              <input
                id="phone"
                v-model="form.phone"
                type="tel"
                autocomplete="tel"
                placeholder="+91"
                required
                pattern="[0-9+\s()-]{8,18}"
              >
            </div>

            <div class="field">
              <label for="email">Email address (optional for confirmation)</label>
              <input
                id="email"
                v-model="form.email"
                type="email"
                placeholder="you@example.com"
              >
            </div>

            <div class="field">
              <label for="pin">Pincode</label>
              <input
                id="pin"
                v-model="form.pin"
                inputmode="numeric"
                placeholder="380007"
                required
                pattern="[0-9]{6}"
                maxlength="6"
              >
            </div>

            <div class="field">
              <label for="service">Service</label>
              <select 
                id="service" 
                v-model="form.service_name" 
                required
                @change="() => {
                  const s = services.find(x => x.title === form.service_name);
                  form.service_id = s ? s.id : null;
                }"
              >
                <option v-for="svc in services" :key="svc.id" :value="svc.title">
                  {{ svc.title }} (₹{{ Math.round(Number(svc.price)).toLocaleString('en-IN') }})
                </option>
                <option value="Glow 3-Visit Plan">Glow 3-Visit Plan (₹3,299)</option>
                <option value="Not sure, help me choose">Not sure, help me choose</option>
              </select>
            </div>

            <div class="field">
              <label for="specialist">Preferred Specialist (optional)</label>
              <select 
                id="specialist" 
                v-model="form.specialist_id"
                @change="() => {
                  const spec = specialists.find(s => s.id === form.specialist_id);
                  form.specialist_name = spec ? spec.name : '';
                }"
              >
                <option :value="null">Any available specialist</option>
                <option v-for="sp in specialists" :key="sp.id" :value="sp.id">
                  {{ sp.name }} ({{ sp.qualification }} - {{ sp.city }})
                </option>
              </select>
            </div>

            <div class="field">
              <label for="date">Preferred date</label>
              <input 
                id="date" 
                v-model="form.date" 
                type="date" 
                :min="todayDate"
                required
              >
            </div>

            <div class="field">
              <label>Select Time Slot</label>
              <div v-if="loadingSlots" style="font-size:0.75rem;color:var(--muted)">Checking availability...</div>
              <div v-else class="slot-pills">
                <button
                  v-for="slot in availableSlots"
                  :key="slot.id"
                  type="button"
                  class="slot-pill"
                  :class="{ active: form.time_slot_id === slot.id }"
                  :disabled="!slot.is_available"
                  @click="selectSlot(slot)"
                >
                  {{ slot.start_time }} - {{ slot.end_time }}
                  <span v-if="!slot.is_available" style="font-size:0.65rem"> (Full)</span>
                </button>
              </div>
            </div>

            <div class="field full">
              <label for="msg">Your skin concern (optional)</label>
              <textarea 
                id="msg" 
                v-model="form.msg" 
                rows="3" 
                placeholder="Tell us what you'd like your specialist to focus on..."
              ></textarea>
            </div>

            <div class="field full">
              <label>Payment Option</label>
              <div style="display:flex;gap:16px;flex-wrap:wrap;margin-top:6px">
                <label style="display:flex;align-items:center;gap:6px;font-weight:500;font-size:0.82rem;cursor:pointer">
                  <input type="radio" v-model="form.payment_method" value="after_visit" style="width:auto">
                  <span>Pay After Visit (UPI / Cash)</span>
                </label>
                <label style="display:flex;align-items:center;gap:6px;font-weight:500;font-size:0.82rem;cursor:pointer">
                  <input type="radio" v-model="form.payment_method" value="razorpay" style="width:auto">
                  <span>Pay Online Now (Razorpay / Cards / UPI)</span>
                </label>
              </div>
            </div>
          </div>

          <label class="consent" for="consent">
            <input
              id="consent"
              v-model="form.consent"
              type="checkbox"
              required
            >
            <span>I agree that FMZ India may contact me on WhatsApp about this request.</span>
          </label>

          <button class="btn" type="submit" :disabled="submitting">
            <span v-if="submitting">Processing your booking...</span>
            <span v-else>Book your glow</span>
          </button>

          <div class="form-note">
            <svg class="form-note-icon" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
              <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
            </svg>
            <div>
              <strong>Please note:</strong> This request does not confirm an appointment. Price, specialist and any travel charges are confirmed in writing before your visit. You pay after your visit.
            </div>
          </div>

          <div v-if="successMessage" class="success-box" role="status">
            <b>Thank you!</b> {{ successMessage }}
          </div>

          <div v-if="errorMessage" class="error-box" role="alert">
            {{ errorMessage }}
          </div>
        </form>
      </div>
    </div>
  </section>
</template>
