<script setup>
import { ref, reactive, watch, computed, onMounted } from 'vue';

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
  },
  preselectedConcern: {
    type: String,
    default: ''
  },
  settings: {
    type: Object,
    default: () => ({})
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
  payment_method: 'after_visit' // 'after_visit' or 'online'
});

const availableSlots = ref([]);
const loadingSlots = ref(false);
const submitting = ref(false);
const isRedirecting = ref(false);
const checkoutUrl = ref('');
const successMessage = ref('');
const bookingResult = ref(null);
const errorMessage = ref('');

const pendingBooking = ref(null);
const paymentFailed = ref(false);
const switchingToCash = ref(false);
const cancellingBooking = ref(false);

const activeGateway = computed(() => {
  return props.settings?.active_payment_gateway || 'zoho';
});

const isOnlinePaymentAvailable = computed(() => {
  return activeGateway.value !== 'none';
});

const onlinePaymentLabel = computed(() => {
  if (activeGateway.value === 'zoho') {
    return 'Pay Online Now (Cards / UPI / NetBanking)';
  } else if (activeGateway.value === 'razorpay') {
    return 'Pay Online Now (Razorpay / Cards / UPI)';
  }
  return 'Pay Online Now (Cards / UPI / NetBanking)';
});

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

// Watch for preselected concern
watch(() => props.preselectedConcern, (newVal) => {
  if (newVal && !form.msg) {
    form.msg = newVal;
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

    const isOnline = form.payment_method === 'online' || form.payment_method === 'razorpay' || data.is_online_payment;

    if (!isOnline) {
      bookingResult.value = data.booking;
      successMessage.value = data.message || 'Your booking request has been received!';
      pendingBooking.value = null;
      paymentFailed.value = false;
      submitting.value = false;
      return;
    }

    // For online payment: do NOT confirm appointment until payment is verified
    pendingBooking.value = data.booking;
    bookingResult.value = null;
    successMessage.value = '';
    paymentFailed.value = false;

    await initiateOnlinePayment(data.booking.id, data.booking);

  } catch (err) {
    console.error('Booking submission error:', err);
    errorMessage.value = 'Network error. Please try again or message us on WhatsApp.';
    submitting.value = false;
  }
};

const initiateOnlinePayment = async (bookingId, bookingData) => {
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
      // Test mode or unpriced consultation notice
      if (orderData.mock_allowed) {
        successMessage.value = orderData.message || 'Online payment not required; you can pay after your visit via UPI or Cash.';
        bookingResult.value = bookingData;
        paymentFailed.value = false;
      } else {
        paymentFailed.value = true;
        errorMessage.value = orderData.message || 'Online payment initialization failed.';
      }
      submitting.value = false;
      return;
    }

    // 1. Zoho Checkout: Redirect customer to Zoho checkout URL
    if (orderData.gateway === 'zoho' || orderData.checkout_url) {
      checkoutUrl.value = orderData.checkout_url;
      successMessage.value = 'Redirecting to secure payment checkout...';
      isRedirecting.value = true;
      setTimeout(() => {
        window.location.href = orderData.checkout_url;
      }, 700);
      return;
    }

    // 2. Razorpay Checkout
    if (orderData.gateway === 'razorpay' || orderData.key) {
      if (typeof window.Razorpay === 'undefined') {
        await new Promise((resolve, reject) => {
          const script = document.createElement('script');
          script.src = 'https://checkout.razorpay.com/v1/checkout.js';
          script.async = true;
          script.onload = resolve;
          script.onerror = () => reject(new Error('Failed to load Razorpay SDK'));
          document.head.appendChild(script);
        }).catch(err => {
          console.error('Failed to load Razorpay script:', err);
        });
      }

      if (window.Razorpay) {
        const options = {
          key: orderData.key,
          amount: orderData.amount,
          currency: orderData.currency || 'INR',
          name: 'FMZ Glow',
          description: `Booking #${orderData.booking_number}`,
          image: '/images/FMZ logo.jpg',
          prefill: {
            name: orderData.client_name,
            email: orderData.client_email,
            contact: orderData.client_phone
          },
          theme: {
            color: '#7a2e2e'
          },
          handler: async function (response) {
            await verifyPaymentOnServer(bookingId, response);
          },
          modal: {
            ondismiss: function () {
              handlePaymentDismissed();
            }
          }
        };
        if (orderData.order_id) {
          options.order_id = orderData.order_id;
        }
        const rzp = new window.Razorpay(options);
        rzp.on('payment.failed', function (resp) {
          handlePaymentFailed(resp?.error?.description);
        });
        rzp.open();
      } else {
        paymentFailed.value = true;
        errorMessage.value = 'Razorpay checkout script could not be loaded. You can pay after your visit via UPI or Cash.';
        submitting.value = false;
      }
    }
  } catch (e) {
    console.error('Payment init error:', e);
    paymentFailed.value = true;
    errorMessage.value = 'Could not open payment gateway. You can pay after your visit via UPI or Cash.';
    submitting.value = false;
  }
};

const handlePaymentDismissed = () => {
  submitting.value = false;
  paymentFailed.value = true;
  successMessage.value = '';
  errorMessage.value = 'Payment was closed before completion. Your appointment has NOT been confirmed yet.';
};

const handlePaymentFailed = (reason) => {
  submitting.value = false;
  paymentFailed.value = true;
  successMessage.value = '';
  errorMessage.value = `Payment failed (${reason || 'Transaction could not be processed'}). Your appointment has NOT been confirmed yet.`;
};

const verifyPaymentOnServer = async (bookingId, razorpayResponse) => {
  submitting.value = true;
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
      paymentFailed.value = false;
      errorMessage.value = '';
      bookingResult.value = verifyData.booking || pendingBooking.value;
      successMessage.value = verifyData.message || 'Payment successful! Your appointment is fully confirmed.';
      pendingBooking.value = null;
    } else {
      paymentFailed.value = true;
      successMessage.value = '';
      errorMessage.value = verifyData.message || 'Payment verification failed.';
    }
  } catch (e) {
    console.error('Payment verification error:', e);
    paymentFailed.value = true;
    successMessage.value = '';
    errorMessage.value = 'Payment verification encountered a network error. Please contact us on WhatsApp with your payment details.';
  } finally {
    submitting.value = false;
  }
};

const retryOnlinePayment = () => {
  if (!pendingBooking.value) return;
  errorMessage.value = '';
  paymentFailed.value = false;
  submitting.value = true;
  initiateOnlinePayment(pendingBooking.value.id, pendingBooking.value);
};

const switchToPayAfterVisit = async () => {
  if (!pendingBooking.value) return;
  switchingToCash.value = true;
  try {
    const res = await fetch(`/api/bookings/${pendingBooking.value.id}/switch-to-cash`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      }
    });
    const data = await res.json();
    if (data.success) {
      paymentFailed.value = false;
      errorMessage.value = '';
      bookingResult.value = data.booking || pendingBooking.value;
      successMessage.value = data.message || 'Appointment scheduled! You can pay after your visit via UPI or Cash.';
      pendingBooking.value = null;
    } else {
      errorMessage.value = data.message || 'Could not switch payment method.';
    }
  } catch (e) {
    console.error('Error switching payment method:', e);
    errorMessage.value = 'Network error. Please try again or contact us on WhatsApp.';
  } finally {
    switchingToCash.value = false;
  }
};

const cancelPendingBooking = async () => {
  if (!pendingBooking.value) return;
  cancellingBooking.value = true;
  try {
    const res = await fetch(`/api/bookings/${pendingBooking.value.id}/cancel`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({ reason: 'Customer cancelled after payment dismissal' })
    });
    const data = await res.json();
    if (data.success) {
      paymentFailed.value = false;
      pendingBooking.value = null;
      bookingResult.value = null;
      successMessage.value = '';
      errorMessage.value = 'Your booking request has been cancelled.';
    } else {
      errorMessage.value = data.message || 'Could not cancel booking.';
    }
  } catch (e) {
    console.error('Error cancelling booking:', e);
  } finally {
    cancellingBooking.value = false;
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
            <div v-if="checkoutUrl" style="margin-top:14px">
              <a :href="checkoutUrl" style="display:inline-block;padding:8px 18px;background:#fff;color:#7a2e2e !important;font-weight:700;border-radius:999px;text-decoration:none;font-size:0.85rem">
                Pay Online Now &rarr;
              </a>
            </div>
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
                <label v-if="isOnlinePaymentAvailable" style="display:flex;align-items:center;gap:6px;font-weight:500;font-size:0.82rem;cursor:pointer">
                  <input type="radio" v-model="form.payment_method" value="online" style="width:auto">
                  <span>{{ onlinePaymentLabel }}</span>
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
            <span v-if="submitting">{{ isRedirecting ? 'Opening payment gateway...' : (form.payment_method === 'online' || form.payment_method === 'razorpay' ? 'Opening payment checkout...' : 'Processing your booking...') }}</span>
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
            <div v-if="checkoutUrl" style="margin-top:12px">
              <a :href="checkoutUrl" class="btn" style="display:inline-block;padding:8px 18px;background:#7a2e2e;color:#fff !important;font-weight:700;border-radius:999px;text-decoration:none;font-size:0.85rem">
                Proceed to Payment &rarr;
              </a>
            </div>
          </div>

          <div v-if="errorMessage" class="error-box" role="alert">
            {{ errorMessage }}
          </div>

          <!-- Actions if online payment was closed or failed -->
          <div v-if="paymentFailed && pendingBooking" style="margin-top:14px;padding:14px 16px;background:#fff5f5;border:1px solid #fecaca;border-radius:12px">
            <div style="font-size:0.86rem;font-weight:700;color:#991b1b;margin-bottom:6px">
              Booking #{{ pendingBooking.booking_number }} is unpaid & unconfirmed.
            </div>
            <p style="font-size:0.78rem;color:#7f1d1d;margin-bottom:12px;line-height:1.4">
              Your appointment is not scheduled yet. You can retry paying online, or confirm this booking right now as <b>Pay After Visit (UPI / Cash)</b>.
            </p>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
              <button 
                type="button" 
                @click="retryOnlinePayment" 
                :disabled="submitting"
                style="padding:8px 16px;font-size:0.82rem;background:#7a2e2e;color:#fff !important;border-radius:999px;border:none;cursor:pointer;font-weight:700"
              >
                {{ submitting ? 'Opening Checkout...' : 'Retry Online Payment' }}
              </button>
              <button 
                type="button" 
                @click="switchToPayAfterVisit" 
                :disabled="switchingToCash"
                style="padding:8px 16px;font-size:0.82rem;background:#fff;color:#7a2e2e !important;border:1px solid #7a2e2e;border-radius:999px;cursor:pointer;font-weight:700"
              >
                {{ switchingToCash ? 'Confirming...' : 'Confirm as Pay After Visit' }}
              </button>
              <button 
                type="button" 
                @click="cancelPendingBooking" 
                :disabled="cancellingBooking"
                style="padding:8px 12px;font-size:0.82rem;background:transparent;color:#991b1b;border:none;cursor:pointer;text-decoration:underline"
              >
                {{ cancellingBooking ? 'Cancelling...' : 'Cancel Request' }}
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </section>
</template>
