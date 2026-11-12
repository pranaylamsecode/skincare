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
    default: ''
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

// Wizard Step: 1, 2, 3, 4 (Done)
const currentStep = ref(1);

// Client reference service catalog / dynamic services
const defaultServices = [
  { key: 'cleanse', name: 'FMZ Glow Cleanse', dur: 'About 45 min', price: '₹799', rawPrice: 799 },
  { key: 'facial', name: 'FMZ Glow Facial', dur: 'About 60 min · launch offer', price: '₹999', rawPrice: 999 },
  { key: 'ritual', name: 'FMZ Radiance Ritual', dur: 'About 75 min · includes Prysm iO scan', price: '₹1,699', rawPrice: 1699 },
  { key: 'plan', name: 'Glow 3-Visit Plan', dur: '3 Glow Facials · valid 90 days', price: '₹2,799', rawPrice: 2799 }
];

const serviceOptions = computed(() => {
  if (props.services && props.services.length) {
    const list = props.services.map(s => {
      let key = 'cleanse';
      const t = (s.title || '').toLowerCase();
      if (t.includes('cleanse')) key = 'cleanse';
      else if (t.includes('facial') || t.includes('lumispa')) key = 'facial';
      else if (t.includes('ritual') || t.includes('radiance')) key = 'ritual';
      else if (t.includes('plan')) key = 'plan';
      else key = 'svc-' + s.id;

      const priceNum = Math.round(Number(s.price || 0));
      return {
        id: s.id,
        key: key,
        name: s.title,
        dur: s.short_description || (s.duration_minutes ? `About ${s.duration_minutes} min` : 'Custom duration'),
        price: '₹' + priceNum.toLocaleString('en-IN'),
        rawPrice: priceNum
      };
    });

    // Ensure 3-Visit plan is present
    if (!list.some(x => x.name.toLowerCase().includes('3-visit') || x.name.toLowerCase().includes('plan'))) {
      list.push({
        id: null,
        key: 'plan',
        name: 'Glow 3-Visit Plan',
        dur: '3 Glow Facials · valid 90 days',
        price: '₹2,799',
        rawPrice: 2799
      });
    }
    return list;
  }
  return defaultServices;
});

const selectedServiceKey = ref('facial');
const selectedSpecialistId = ref(null);
const selectedSpecialistName = ref('');

const currentService = computed(() => {
  return serviceOptions.value.find(s => s.key === selectedServiceKey.value) || serviceOptions.value[0];
});

// Days generation (next 7 days)
const WINDOWS = [
  { id: 'm', label: 'Morning', sub: '9am – 12pm', end: 12, slotId: 1, slotText: 'Morning (09:00 AM - 12:00 PM)' },
  { id: 'a', label: 'Afternoon', sub: '12pm – 4pm', end: 16, slotId: 3, slotText: 'Afternoon (12:00 PM - 04:00 PM)' },
  { id: 'e', label: 'Evening', sub: '4pm – 7:30pm', end: 19.5, slotId: 5, slotText: 'Evening (04:30 PM - 07:30 PM)' }
];

const hoursNow = () => {
  const n = new Date();
  return n.getHours() + n.getMinutes() / 60;
};

const windowOpen = (dayIdx, w) => {
  if (dayIdx > 0) return true;
  return hoursNow() + 2 <= w.end;
};

const days = computed(() => {
  const now = new Date();
  const list = [];
  for (let i = 0; i < 7; i++) {
    const d = new Date(now);
    d.setDate(now.getDate() + i);
    // If today is fully passed, skip today
    if (i === 0 && !WINDOWS.some(w => windowOpen(0, w))) {
      continue;
    }
    const dayName = i === 0 ? 'Today' : (i === 1 ? 'Tomorrow' : d.toLocaleDateString('en-IN', { weekday: 'short' }));
    const sub = d.toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
    const iso = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    list.push({ dayIdx: i, dateObj: d, label: dayName, sub: sub, iso: iso });
  }
  return list;
});

const selectedDayIdx = ref(0);
const selectedWindow = ref(null);

// Form Details (Step 3)
const form = reactive({
  name: '',
  phone: '',
  email: '',
  pin: '',
  msg: '',
  consent: true,
  payment_method: 'after_visit'
});

const PIN_PREFIX = {
  '380': 'Ahmedabad',
  '382': 'Gandhinagar / Ahmedabad',
  '394': 'Surat',
  '395': 'Surat',
  '360': 'Rajkot'
};

const pinCity = ref(null);
const pinHint = ref('');
const phoneHint = ref('');
const phoneHintErr = ref(false);
const submitting = ref(false);
const isRedirecting = ref(false);
const errorMessage = ref('');
const bookingResult = ref(null);
const pendingBooking = ref(null);
const paymentFailed = ref(false);
const switchingToCash = ref(false);

const activeGateway = computed(() => {
  return props.settings?.active_payment_gateway || 'razorpay';
});

const isOnlinePaymentAvailable = computed(() => {
  return activeGateway.value !== 'none';
});

const waNumber = computed(() => {
  return (props.settings?.whatsapp_number || '+917303706047').replace(/[^0-9]/g, '');
});

const waBookingLink = computed(() => {
  return `https://wa.me/${waNumber.value}?text=${encodeURIComponent('Hi FMZ Glow, I would like to book a visit.')}`;
});

// Selection methods
const pickSvc = (key) => {
  selectedServiceKey.value = key;
};

const pickDay = (idx) => {
  selectedDayIdx.value = idx;
  selectedWindow.value = null;
};

const pickWindow = (w) => {
  selectedWindow.value = w;
};

const goToStep = (step) => {
  currentStep.value = step;
  if (step === 3) {
    validate();
  }
};

const whenText = computed(() => {
  const d = days.value.find(x => x.dayIdx === selectedDayIdx.value);
  if (!d || !selectedWindow.value) return '';
  return `${d.label}, ${d.sub} · ${selectedWindow.value.label} (${selectedWindow.value.sub})`;
});

// Validation for Step 3
const validate = () => {
  form.phone = form.phone.replace(/\D/g, '');
  form.pin = form.pin.replace(/\D/g, '');

  const phoneOk = /^[6-9]\d{9}$/.test(form.phone);
  if (form.phone.length === 10 && !phoneOk) {
    phoneHint.value = 'Enter a valid 10-digit mobile number.';
    phoneHintErr.value = true;
  } else {
    phoneHint.value = '';
    phoneHintErr.value = false;
  }

  pinCity.value = null;
  if (form.pin.length === 6) {
    const prefix = form.pin.slice(0, 3);
    pinCity.value = PIN_PREFIX[prefix] || null;
    if (pinCity.value) {
      pinHint.value = '✓ We cover ' + pinCity.value;
    } else {
      pinHint.value = 'We don’t cover this pincode yet. You can still send the request and we’ll tell you when we arrive.';
    }
  } else {
    pinHint.value = '';
  }
};

// Submit booking
const submitBooking = async () => {
  errorMessage.value = '';

  if (!form.name.trim()) {
    errorMessage.value = 'Please enter your name.';
    return;
  }

  const phoneClean = form.phone.replace(/\D/g, '');
  if (phoneClean.length < 10) {
    errorMessage.value = 'Please enter a valid 10-digit WhatsApp number.';
    return;
  }

  if (form.pin.trim().length !== 6) {
    errorMessage.value = 'Please enter a valid 6-digit pincode.';
    return;
  }

  if (!form.consent) {
    errorMessage.value = 'Please check the box to allow FMZ India to message you on WhatsApp about this booking.';
    return;
  }

  if (submitting.value) return;

  submitting.value = true;
  paymentFailed.value = false;

  const d = days.value.find(x => x.dayIdx === selectedDayIdx.value) || days.value[0];
  const svc = currentService.value;
  const isOnline = form.payment_method === 'online';

  let notePayload = form.msg.trim();
  if (selectedSpecialistName.value && !notePayload.includes('Preferred specialist:')) {
    notePayload = (notePayload ? notePayload + '\n' : '') + 'Preferred specialist: ' + selectedSpecialistName.value;
  }

  try {
    const res = await fetch('/api/bookings', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body: JSON.stringify({
        name: form.name.trim(),
        phone: form.phone.trim(),
        email: form.email.trim() || null,
        pincode: form.pin.trim(),
        service_name: svc.name,
        service_id: svc.id || null,
        specialist_id: selectedSpecialistId.value || null,
        specialist_name: selectedSpecialistName.value || null,
        date: d.iso,
        time_slot_id: selectedWindow.value?.slotId || null,
        time_slot_text: selectedWindow.value?.slotText || '',
        msg: notePayload,
        consent: form.consent,
        payment_method: form.payment_method
      })
    });

    const data = await res.json();

    if (!res.ok) {
      if (data.errors) {
        errorMessage.value = Object.values(data.errors).flat().join(' ');
      } else {
        errorMessage.value = data.message || 'Something went wrong. Please check your inputs.';
      }
      submitting.value = false;
      return;
    }

    if (!isOnline) {
      // Pay after visit confirmed
      bookingResult.value = data.booking;
      pendingBooking.value = null;
      submitting.value = false;
      currentStep.value = 4;
      return;
    }

    // Online payment requested: initiate gateway checkout
    pendingBooking.value = data.booking;
    bookingResult.value = null;
    await initiateOnlinePayment(data.booking.id, data.booking);

  } catch (err) {
    console.error('Booking submission error:', err);
    errorMessage.value = 'Network error. Please try again or tap the WhatsApp button.';
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
      if (orderData.mock_allowed) {
        bookingResult.value = bookingData;
        currentStep.value = 4;
      } else {
        paymentFailed.value = true;
        errorMessage.value = orderData.message || 'Online payment initialization failed.';
      }
      submitting.value = false;
      return;
    }

    // Zoho Checkout Redirect
    if (orderData.gateway === 'zoho' || orderData.checkout_url) {
      isRedirecting.value = true;
      setTimeout(() => {
        window.location.href = orderData.checkout_url;
      }, 500);
      return;
    }

    // Razorpay Checkout Modal
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
            email: orderData.client_email || '',
            contact: orderData.client_phone
          },
          theme: { color: '#7a2e2e' },
          handler: async function (response) {
            await verifyPaymentOnServer(bookingId, response);
          },
          modal: {
            ondismiss: function () {
              submitting.value = false;
              paymentFailed.value = true;
              errorMessage.value = 'Payment was closed before completion. Your appointment has NOT been confirmed yet.';
            }
          }
        };
        if (orderData.order_id) {
          options.order_id = orderData.order_id;
        }
        const rzp = new window.Razorpay(options);
        rzp.on('payment.failed', function (resp) {
          submitting.value = false;
          paymentFailed.value = true;
          errorMessage.value = `Payment failed (${resp?.error?.description || 'Transaction could not be processed'}).`;
        });
        rzp.open();
      } else {
        paymentFailed.value = true;
        errorMessage.value = 'Razorpay checkout script could not be loaded.';
        submitting.value = false;
      }
    }
  } catch (e) {
    console.error('Payment init error:', e);
    paymentFailed.value = true;
    errorMessage.value = 'Could not open payment gateway. You can confirm as Pay After Visit.';
    submitting.value = false;
  }
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
        razorpay_payment_id: razorpayResponse.razorpay_payment_id,
        razorpay_order_id: razorpayResponse.razorpay_order_id,
        razorpay_signature: razorpayResponse.razorpay_signature
      })
    });
    const verifyData = await verifyRes.json();
    if (verifyData.success) {
      bookingResult.value = verifyData.booking || pendingBooking.value;
      pendingBooking.value = null;
      paymentFailed.value = false;
      submitting.value = false;
      currentStep.value = 4;
    } else {
      paymentFailed.value = true;
      errorMessage.value = verifyData.message || 'Payment verification failed.';
      submitting.value = false;
    }
  } catch (err) {
    console.error('Verify error:', err);
    paymentFailed.value = true;
    errorMessage.value = 'Payment verification request failed. Please check WhatsApp.';
    submitting.value = false;
  }
};

const retryOnlinePayment = () => {
  if (!pendingBooking.value) return;
  submitting.value = true;
  errorMessage.value = '';
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
      bookingResult.value = data.booking || pendingBooking.value;
      pendingBooking.value = null;
      paymentFailed.value = false;
      currentStep.value = 4;
    } else {
      errorMessage.value = data.message || 'Could not update payment method.';
    }
  } catch (e) {
    errorMessage.value = 'Network error while confirming payment method.';
  } finally {
    switchingToCash.value = false;
  }
};

// WhatsApp Link for Step 4
const doneWaLink = computed(() => {
  const svc = currentService.value;
  const specPart = (bookingResult.value?.specialist_name || selectedSpecialistName.value)
    ? `\nSpecialist: ${bookingResult.value?.specialist_name || selectedSpecialistName.value}`
    : '';
  const notePart = form.msg.trim() ? `\nNote: ${form.msg.trim()}` : '';
  const payPart = form.payment_method === 'online' ? 'online' : 'after visit';
  const msg = `Hi FMZ Glow, I’d like to book:\n${svc?.name} (${svc?.price})\n${whenText.value}\nName: ${form.name.trim()}\nPincode: ${form.pin}${specPart}${notePart}\nPayment: ${payPart}`;
  return `https://wa.me/${waNumber.value}?text=${encodeURIComponent(msg)}`;
});

const doneTitle = computed(() => {
  return pinCity.value ? 'Booking requested' : 'Request received';
});

const doneSubtitle = computed(() => {
  return pinCity.value
    ? 'We’ll confirm your specialist, exact time and total price on WhatsApp within 30 minutes.'
    : 'We don’t cover your pincode yet. We’ll message you on WhatsApp as soon as a specialist is available near you.';
});

const restartWizard = () => {
  currentStep.value = 1;
  selectedDayIdx.value = 0;
  selectedWindow.value = null;
  selectedSpecialistId.value = null;
  selectedSpecialistName.value = '';
  form.name = '';
  form.phone = '';
  form.email = '';
  form.pin = '';
  form.msg = '';
  form.consent = true;
  form.payment_method = 'after_visit';
  bookingResult.value = null;
  pendingBooking.value = null;
  paymentFailed.value = false;
  errorMessage.value = '';
};

// Listen to preselected service or specialist from outside clicks
watch(() => props.preselectedService, (newVal) => {
  if (newVal) {
    const match = serviceOptions.value.find(s => 
      s.name.toLowerCase().includes(newVal.toLowerCase()) || 
      newVal.toLowerCase().includes(s.name.toLowerCase()) ||
      newVal.toLowerCase().includes(s.key)
    );
    if (match) {
      selectedServiceKey.value = match.key;
    }
  }
}, { immediate: true });

watch(() => props.preselectedSpecialist, (newVal) => {
  if (newVal) {
    selectedSpecialistId.value = newVal.id;
    selectedSpecialistName.value = newVal.name;
    // Jump straight to Step 2 so user can immediately choose day & time
    if (currentStep.value < 2) {
      currentStep.value = 2;
    }
  }
}, { immediate: true });

watch(() => props.preselectedConcern, (newVal) => {
  if (newVal && !form.msg) {
    form.msg = newVal;
  }
}, { immediate: true });

onMounted(() => {
  // Select first available window for today or tomorrow if available
  if (days.value.length > 0) {
    const openWin = WINDOWS.find(w => windowOpen(days.value[0].dayIdx, w));
    if (openWin) {
      selectedWindow.value = openWin;
    } else if (days.value.length > 1) {
      selectedDayIdx.value = days.value[1].dayIdx;
      selectedWindow.value = WINDOWS[0];
    }
  }
});
</script>

<template>
  <section class="booking" id="book">
    <div class="wrap bk-grid">
      <!-- Left sidebar -->
      <div class="bk-side">
        <h2>Ready for your glow?</h2>
        <p>Three quick steps. We confirm your specialist, time and total price on WhatsApp before anyone comes to your home.</p>
        <ul>
          <li>Certified women specialists only</li>
          <li>No payment now; pay after your visit</li>
          <li>Free rescheduling up to 4 hours before</li>
        </ul>
        <a class="btn wa" :href="waBookingLink" target="_blank" rel="noopener">Prefer WhatsApp? Chat now</a>
      </div>

      <!-- Right Wizard Container -->
      <div class="bk" aria-live="polite">
        <!-- 3-Step Progress Indicator -->
        <div class="progress" aria-hidden="true" v-if="currentStep <= 3">
          <span id="p1" :class="{ on: currentStep >= 1 }"></span>
          <span id="p2" :class="{ on: currentStep >= 2 }"></span>
          <span id="p3" :class="{ on: currentStep >= 3 }"></span>
        </div>

        <!-- Step 1: Choose your visit -->
        <div id="s1" v-if="currentStep === 1">
          <h3>Choose your visit</h3>
          <p class="sub">Step 1 of 3</p>
          <div class="opts" id="svcOpts">
            <button
              v-for="svc in serviceOptions"
              :key="svc.key"
              type="button"
              class="opt"
              :aria-pressed="selectedServiceKey === svc.key"
              @click="pickSvc(svc.key)"
            >
              <span>
                <b>{{ svc.name }}</b>
                <small>{{ svc.dur }}</small>
              </span>
              <span class="p">{{ svc.price }}</span>
            </button>
          </div>
          <div class="nav-row">
            <button class="btn" id="to2" type="button" :disabled="!selectedServiceKey" @click="goToStep(2)">
              Continue
            </button>
          </div>
        </div>

        <!-- Step 2: Pick a day and time -->
        <div id="s2" v-if="currentStep === 2">
          <h3>Pick a day and time</h3>
          <p class="sub">Step 2 of 3 · we’ll confirm the exact time on WhatsApp</p>
          
          <span class="lbl">Day</span>
          <div class="chips" id="dayChips">
            <button
              v-for="day in days"
              :key="day.dayIdx"
              type="button"
              class="chip"
              :aria-pressed="selectedDayIdx === day.dayIdx"
              @click="pickDay(day.dayIdx)"
            >
              {{ day.label }}
              <small>{{ day.sub }}</small>
            </button>
          </div>

          <span class="lbl">Time</span>
          <div class="chips wide" id="timeChips">
            <button
              v-for="win in WINDOWS"
              :key="win.id"
              type="button"
              class="chip"
              :aria-pressed="selectedWindow?.id === win.id"
              :disabled="!windowOpen(selectedDayIdx, win)"
              @click="pickWindow(win)"
            >
              {{ win.label }}
              <small>{{ win.sub }}</small>
            </button>
          </div>

          <p class="hint" id="timeHint">
            {{ selectedDayIdx === 0 ? "Same-day visits need at least 2 hours’ notice." : "" }}
          </p>

          <div class="nav-row">
            <button class="btn ghost" type="button" @click="goToStep(1)">Back</button>
            <button class="btn" id="to3" type="button" :disabled="selectedDayIdx === null || !selectedWindow" @click="goToStep(3)">
              Continue
            </button>
          </div>
        </div>

        <!-- Step 3: Your details -->
        <div id="s3" v-if="currentStep === 3">
          <h3>Your details</h3>
          <p class="sub">Step 3 of 3</p>

          <div class="summary" id="sum3">
            <div><b>{{ currentService?.name }}</b> · {{ currentService?.price }}</div>
            <div>{{ whenText }}</div>
            <div v-if="selectedSpecialistName">Preferred specialist: {{ selectedSpecialistName }}</div>
          </div>

          <label class="lbl" for="fName">Your name</label>
          <input 
            class="field" 
            id="fName" 
            v-model="form.name" 
            autocomplete="name" 
            placeholder="e.g. Mansi Joshi" 
            @input="validate"
          >

          <label class="lbl" for="fEmail">Email address (for booking confirmation & receipt)</label>
          <input 
            class="field" 
            id="fEmail" 
            type="email"
            v-model="form.email" 
            autocomplete="email" 
            placeholder="e.g. mansi@example.com"
          >

          <div class="row2">
            <div>
              <label class="lbl" for="fPhone">WhatsApp number</label>
              <div class="prefix">
                <span>+91</span>
                <input 
                  class="field" 
                  id="fPhone" 
                  v-model="form.phone" 
                  inputmode="numeric" 
                  autocomplete="tel-national" 
                  maxlength="10" 
                  placeholder="98765 43210" 
                  @input="validate"
                >
              </div>
              <p class="hint" :class="{ err: phoneHintErr }">{{ phoneHint }}</p>
            </div>

            <div>
              <label class="lbl" for="fPin">Pincode</label>
              <input 
                class="field" 
                id="fPin" 
                v-model="form.pin" 
                inputmode="numeric" 
                autocomplete="postal-code" 
                maxlength="6" 
                placeholder="380007" 
                @input="validate"
              >
              <p class="hint" :class="{ ok: pinCity, err: !pinCity && form.pin.length === 6 }">{{ pinHint }}</p>
            </div>
          </div>

          <label class="lbl" for="fNote">Anything your specialist should know? (optional)</label>
          <input 
            class="field" 
            id="fNote" 
            v-model="form.msg" 
            placeholder="e.g. sensitive skin, pregnant, 2nd floor"
          >

          <span class="lbl">Payment</span>
          <div class="pay">
            <button 
              type="button"
              class="chip" 
              :aria-pressed="form.payment_method === 'after_visit'"
              @click="form.payment_method = 'after_visit'"
            >
              Pay after visit<small>UPI or cash</small>
            </button>
            <button 
              v-if="isOnlinePaymentAvailable"
              type="button"
              class="chip" 
              :aria-pressed="form.payment_method === 'online'"
              @click="form.payment_method = 'online'"
            >
              Pay online now<small>UPI or card</small>
            </button>
          </div>

          <p class="hint" style="margin-top:6px;font-size:12.5px;color:var(--muted)">
            <span v-if="form.payment_method === 'after_visit'">
              ✓ <b>Pay after visit selected:</b> No payment taken now. You pay directly after your treatment via UPI or cash.
            </span>
            <span v-else>
              ✓ <b>Pay online selected:</b> Fast &amp; secure checkout with Razorpay / UPI / Cards.
            </span>
          </p>

          <label class="check">
            <input type="checkbox" id="fConsent" v-model="form.consent" @change="validate">
            <span>FMZ India can message me on WhatsApp about this booking.</span>
          </label>

          <div v-if="errorMessage" class="hint err" style="margin-top:12px;font-size:0.9rem">
            {{ errorMessage }}
          </div>

          <div class="nav-row">
            <button class="btn ghost" type="button" @click="goToStep(2)">Back</button>
            <button 
              class="btn" 
              id="submit" 
              type="button" 
              :disabled="submitting" 
              @click="submitBooking"
            >
              <span v-if="submitting">
                {{ isRedirecting ? 'Redirecting to payment...' : (form.payment_method === 'online' ? 'Opening payment...' : 'Confirming your booking...') }}
              </span>
              <span v-else>
                {{ form.payment_method === 'online' ? 'Proceed to Online Payment' : 'Confirm booking (Pay after visit)' }}
              </span>
            </button>
          </div>

          <p class="foot-note">No payment is taken now. Travel is free within 8 km; anything beyond is shown in your WhatsApp confirmation before you agree.</p>

          <!-- Online payment closed / retry container -->
          <div v-if="paymentFailed && pendingBooking" style="margin-top:16px;padding:16px;background:#fff5f5;border:1px solid #fecaca;border-radius:14px">
            <div style="font-size:0.9rem;font-weight:700;color:#991b1b;margin-bottom:6px">
              Booking #{{ pendingBooking.booking_number }} is unpaid & unconfirmed.
            </div>
            <p style="font-size:0.8rem;color:#7f1d1d;margin-bottom:12px;line-height:1.4">
              Your appointment is not scheduled yet. You can retry paying online, or confirm this booking as <b>Pay After Visit (UPI / Cash)</b>.
            </p>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
              <button 
                type="button" 
                class="btn" 
                style="flex:1;min-height:42px"
                @click="retryOnlinePayment" 
                :disabled="submitting"
              >
                {{ submitting ? 'Opening Checkout...' : 'Retry Online Payment' }}
              </button>
              <button 
                type="button" 
                class="btn ghost" 
                style="flex:1;min-height:42px"
                @click="switchToPayAfterVisit" 
                :disabled="switchingToCash"
              >
                {{ switchingToCash ? 'Confirming...' : 'Confirm as Pay After Visit' }}
              </button>
            </div>
          </div>
        </div>

        <!-- Step 4: Done / Booking requested state -->
        <div id="s4" v-if="currentStep === 4" class="done">
          <div class="tick">✓</div>
          <h3 id="doneTitle">{{ doneTitle }}</h3>
          <p class="sub" id="doneSub">{{ doneSubtitle }}</p>

          <div class="summary" id="sum4">
            <div><b>{{ currentService?.name }}</b> · {{ currentService?.price }}</div>
            <div>{{ whenText }}</div>
            <div v-if="bookingResult?.specialist_name || selectedSpecialistName">
              <b>Specialist:</b> {{ bookingResult?.specialist_name || selectedSpecialistName }}
            </div>
            <div>{{ form.name }} · {{ form.email ? form.email + ' · ' : '' }}+91 {{ form.phone }} · {{ form.pin }}</div>
          </div>

          <a class="btn wa" :href="doneWaLink" target="_blank" rel="noopener" id="doneWa" style="width:100%">
            Send details on WhatsApp now
          </a>
          <button class="btn ghost" id="restart" type="button" style="width:100%;margin-top:10px" @click="restartWizard">
            Book another visit
          </button>
        </div>
      </div>
    </div>
  </section>
</template>
