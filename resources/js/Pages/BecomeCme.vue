<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Check, ShieldCheck, ArrowRight, ArrowLeft, MessageSquare, ExternalLink } from 'lucide-vue-next';

const props = defineProps({
  settings: {
    type: Object,
    default: () => ({}),
  },
});

const currentStep = ref(1);
const registeredData = ref(null);
const errorMessage = ref('');

const form = useForm({
  programmes: [],
  nursing_qualification: '',
  pincode: '',
  city: '',
  state: 'Gujarat',
  first_name: '',
  last_name: '',
  age_group: '',
  gender: '',
  highest_qualification: '',
  vehicle: '',
  driving_licence: '',
  mobile: '',
  is_whatsapp_same: true,
  whatsapp_number: '',
  email: '',
  referral_source: '',
  referral_name: '',
  referral_contact: '',
  referral_org: '',
  confirmed_accuracy: false,
  confirmed_conditions: false,
  confirmed_contact: false,
});

// Step 1: Program toggle
const toggleProgramme = (prog) => {
  const index = form.programmes.indexOf(prog);
  if (index > -1) {
    form.programmes.splice(index, 1);
  } else {
    form.programmes.push(prog);
  }
};

const hasNursing = computed(() => form.programmes.includes('Nursing & Healthcare'));

const pinError = computed(() => {
  if (!form.pincode) return '';
  return /^[1-9]\d{5}$/.test(form.pincode) ? '' : 'Please check the 6-digit PIN code.';
});

const isStep1Valid = computed(() => {
  return (
    form.programmes.length > 0 &&
    /^[1-9]\d{5}$/.test(form.pincode) &&
    form.city.trim().length >= 2 &&
    form.state.trim().length > 0 &&
    (!hasNursing.value || form.nursing_qualification)
  );
});

// Step 2 validation
const isMinor = computed(() => form.age_group === 'Under 18');

const isStep2Valid = computed(() => {
  return (
    form.first_name.trim().length >= 1 &&
    form.last_name.trim().length >= 1 &&
    form.age_group &&
    !isMinor.value &&
    form.gender &&
    form.highest_qualification &&
    form.vehicle &&
    form.driving_licence
  );
});

// Step 3 validation
const mobileError = computed(() => {
  if (!form.mobile) return '';
  return /^[6-9]\d{9}$/.test(form.mobile) ? '' : 'Please enter a valid 10-digit mobile number.';
});

const isStep3Valid = computed(() => {
  const phoneOk = /^[6-9]\d{9}$/.test(form.mobile);
  const waOk = form.is_whatsapp_same || /^[6-9]\d{9}$/.test(form.whatsapp_number);
  const emailOk = !form.email || /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(form.email);
  const refOk = form.referral_source && (
    (form.referral_source === 'A person referred me' ? form.referral_name.trim().length > 0 : true) &&
    (form.referral_source === 'Through an organisation' ? form.referral_org.trim().length > 0 : true)
  );
  const declOk = form.confirmed_accuracy && form.confirmed_conditions && form.confirmed_contact;

  return phoneOk && waOk && emailOk && refOk && declOk;
});

const goToStep = (step) => {
  errorMessage.value = '';
  currentStep.value = step;
  const regEl = document.getElementById('register');
  if (regEl) {
    regEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }
};

const submitRegistration = () => {
  errorMessage.value = '';
  if (!isStep3Valid.value) {
    errorMessage.value = 'Please complete all required fields and accept all three confirmations.';
    return;
  }

  form.post('/become-a-cme', {
    preserveScroll: true,
    onSuccess: (page) => {
      const resp = page.props.flash || {};
      const generatedId = resp.registration_id || `FMZ-CME-${new Date().getFullYear().toString().slice(2)}${String(new Date().getMonth()+1).padStart(2,'0')}-${Math.floor(1000 + Math.random()*9000)}`;
      registeredData.value = {
        id: generatedId,
        name: form.first_name,
        programmes: [...form.programmes],
      };
      currentStep.value = 4;
      const regEl = document.getElementById('register');
      if (regEl) {
        regEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    },
    onError: (errors) => {
      const errList = Object.values(errors);
      errorMessage.value = errList.length ? errList[0] : 'Please verify all fields and try again.';
    }
  });
};

const whatsappUrl = computed(() => {
  const phone = (props.settings?.whatsapp_number || '+917303706047').replace(/[^0-9]/g, '');
  const id = registeredData.value?.id || '';
  const msg = encodeURIComponent(`Hi FMZ Recruitment Team, I registered as a CME (${id}). I would like to know the next steps.`);
  return `https://wa.me/${phone}?text=${msg}`;
});
</script>

<template>
  <Head title="Become an FMZ Certified Micro Entrepreneur · Register free">
    <meta name="description" content="Register free as an FMZ Certified Micro Entrepreneur (CME). Professional training, equipment support, and direct customer bookings in beauty, wellness, and care services." />
  </Head>

  <div class="cme-page">
    <!-- Header -->
    <header class="cme-header">
      <div class="wrap nav">
        <Link href="/glow" class="logo">
          <span class="fmz">FMZ</span>
          <span class="sub">Certified Micro Entrepreneur programme</span>
        </Link>
        <div class="nav-right">
          <Link href="/glow" class="back-site-link">
            <span>FMZ Glow Home</span>
          </Link>
          <a class="btn" href="#register">Register free</a>
        </div>
      </div>
    </header>

    <main id="top">
      <!-- 1. Hero Section -->
      <section class="hero">
        <div class="wrap hero-grid">
          <div>
            <h1>Your skills. <em>Your business.</em> Our support.</h1>
            <p class="lede">
              Become an FMZ Certified Micro Entrepreneur. We train you, help you get equipped and send you customers, so you can earn from beauty, wellness and care services near home.
            </p>
            <div class="cta-row">
              <a class="btn" href="#register">Register your interest</a>
              <a class="btn ghost" href="#how">How it works</a>
            </div>
            <p class="small" style="margin-top: 16px">Free to register · takes about 3 minutes</p>
          </div>

          <div class="hero-media">
            <div class="photo-wrapper">
              <img 
                src="/images/cme-photo.jpg" 
                alt="A smiling FMZ specialist in uniform, with her kit" 
                class="photo"
                onerror="this.src='/images/Burgundy-Luxury-Skincare.png'"
              />
            </div>
            <div class="badge">
              <b>150</b>
              <span>places in the first intake</span>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. Choose your path / Programmes -->
      <section id="programmes" class="programmes-section">
        <div class="wrap">
          <div class="center">
            <h2>Choose your path</h2>
            <p class="lede">Register for one or more. Full training is given for each.</p>
          </div>

          <div class="prog-grid">
            <!-- Programme 1 -->
            <article class="prog">
              <div class="prog-img-box">
                <img src="/images/fmz-glow-facial.png" alt="Specialist giving an at-home facial" class="prog-photo" />
              </div>
              <div class="body">
                <h3>Beauty &amp; Wellness</h3>
                <p>At-home skincare with genuine Nu Skin products and devices, under FMZ Glow.</p>
                <span class="tag">No experience needed</span>
              </div>
            </article>

            <!-- Programme 2 -->
            <article class="prog">
              <div class="prog-img-box img-placeholder">
                <span class="placeholder-tag">Nursing &amp; Screening</span>
              </div>
              <div class="body">
                <h3>Nursing &amp; Healthcare</h3>
                <p>Home nursing visits and health screening, such as iBreastExam.</p>
                <span class="tag">Nursing registration needed</span>
              </div>
            </article>

            <!-- Programme 3 -->
            <article class="prog">
              <div class="prog-img-box img-placeholder carer">
                <span class="placeholder-tag">Aged Care &amp; Companionship</span>
              </div>
              <div class="body">
                <h3>Carer &amp; Aged Care</h3>
                <p>Daily care and companionship for older people and their families.</p>
                <span class="tag">No experience needed</span>
              </div>
            </article>
          </div>
        </div>
      </section>

      <!-- 3. How it works -->
      <section class="how" id="how">
        <div class="wrap">
          <div class="center">
            <h2>How it works</h2>
          </div>
          <div class="steps">
            <div class="step">
              <span class="n">1</span>
              <b>Register</b>
              <p>A short, free form.</p>
            </div>
            <div class="step">
              <span class="n">2</span>
              <b>Meet us and get verified</b>
              <p>An information meeting, then ID and police checks.</p>
            </div>
            <div class="step">
              <span class="n">3</span>
              <b>Train and get equipped</b>
              <p>Training, a certificate, and equipment through our finance partners.</p>
            </div>
            <div class="step">
              <span class="n">4</span>
              <b>Start earning</b>
              <p>Bookings come through FMZ and your own web page.</p>
            </div>
          </div>
        </div>
      </section>

      <!-- 4. Registration Section -->
      <section class="reg" id="register">
        <div class="wrap reg-grid">
          <!-- Left side -->
          <div class="reg-side">
            <h2>Join the first 150</h2>
            <p>Three short steps. If you’re shortlisted, we’ll invite you to an information meeting on WhatsApp.</p>
            <ul>
              <li>Free; FMZ never asks for money to register</li>
              <li>Your own business, not a salaried job</li>
              <li>Registering doesn’t guarantee selection</li>
            </ul>
          </div>

          <!-- Right side: Form -->
          <div class="form" aria-live="polite">
            <!-- Progress indicator -->
            <div class="progress" aria-hidden="true" v-if="currentStep <= 3">
              <span :class="{ on: currentStep >= 1 }"></span>
              <span :class="{ on: currentStep >= 2 }"></span>
              <span :class="{ on: currentStep >= 3 }"></span>
            </div>

            <!-- STEP 1: What interests you? -->
            <div v-if="currentStep === 1">
              <h3>What interests you?</h3>
              <p class="sub">Step 1 of 3 · choose one or more</p>

              <div class="cards" id="progOpts">
                <button 
                  type="button" 
                  class="card-opt" 
                  :aria-pressed="form.programmes.includes('Beauty & Wellness')"
                  @click="toggleProgramme('Beauty & Wellness')"
                >
                  <span class="box"></span>
                  <span>
                    <b>Beauty &amp; Wellness</b>
                    <small>At-home skincare with Nu Skin</small>
                  </span>
                </button>

                <button 
                  type="button" 
                  class="card-opt" 
                  :aria-pressed="form.programmes.includes('Nursing & Healthcare')"
                  @click="toggleProgramme('Nursing & Healthcare')"
                >
                  <span class="box"></span>
                  <span>
                    <b>Nursing &amp; Healthcare</b>
                    <small>Home nursing and screening</small>
                  </span>
                </button>

                <button 
                  type="button" 
                  class="card-opt" 
                  :aria-pressed="form.programmes.includes('Carer & Aged Care')"
                  @click="toggleProgramme('Carer & Aged Care')"
                >
                  <span class="box"></span>
                  <span>
                    <b>Carer &amp; Aged Care</b>
                    <small>Daily care for older people</small>
                  </span>
                </button>
              </div>

              <!-- Nursing qualification prompt -->
              <div v-if="hasNursing" class="conditional-field">
                <label class="lbl" for="fNurse">Your nursing or health qualification</label>
                <select class="field" id="fNurse" v-model="form.nursing_qualification">
                  <option value="">Select qualification</option>
                  <option value="GNM">GNM</option>
                  <option value="ANM">ANM</option>
                  <option value="B.Sc Nursing">B.Sc Nursing</option>
                  <option value="Post-basic / M.Sc Nursing">Post-basic / M.Sc Nursing</option>
                  <option value="Other health qualification">Other health qualification</option>
                  <option value="Not yet qualified">Not yet qualified</option>
                </select>
              </div>

              <!-- Location fields -->
              <div class="row3">
                <div>
                  <label class="lbl" for="fPin">PIN code</label>
                  <input 
                    class="field" 
                    id="fPin" 
                    v-model="form.pincode" 
                    inputmode="numeric" 
                    maxlength="6" 
                    placeholder="380007" 
                    autocomplete="postal-code"
                  />
                </div>
                <div>
                  <label class="lbl" for="fCity">City / town</label>
                  <input 
                    class="field" 
                    id="fCity" 
                    v-model="form.city" 
                    placeholder="Ahmedabad" 
                    autocomplete="address-level2"
                  />
                </div>
                <div>
                  <label class="lbl" for="fState">State</label>
                  <select class="field" id="fState" v-model="form.state" autocomplete="address-level1">
                    <option value="">Select</option>
                    <option value="Gujarat">Gujarat</option>
                    <option value="Maharashtra">Maharashtra</option>
                    <option value="Rajasthan">Rajasthan</option>
                    <option value="Madhya Pradesh">Madhya Pradesh</option>
                    <option value="Other">Other</option>
                  </select>
                </div>
              </div>

              <p class="hint err" v-if="pinError">{{ pinError }}</p>

              <div class="nav-row">
                <button 
                  type="button" 
                  class="btn" 
                  :disabled="!isStep1Valid" 
                  @click="goToStep(2)"
                >
                  Continue
                </button>
              </div>
            </div>

            <!-- STEP 2: About you -->
            <div v-if="currentStep === 2">
              <h3>About you</h3>
              <p class="sub">Step 2 of 3</p>

              <div class="row2">
                <div>
                  <label class="lbl" for="fFirst">First name</label>
                  <input class="field" id="fFirst" v-model="form.first_name" autocomplete="given-name" placeholder="Priya" />
                </div>
                <div>
                  <label class="lbl" for="fLast">Last name</label>
                  <input class="field" id="fLast" v-model="form.last_name" autocomplete="family-name" placeholder="Sharma" />
                </div>
              </div>

              <span class="lbl">Age group</span>
              <div class="chips">
                <button 
                  v-for="age in ['Under 18', '18–24', '25–34', '35–44', '45+']" 
                  :key="age"
                  type="button" 
                  class="chip" 
                  :aria-pressed="form.age_group === age"
                  @click="form.age_group = age"
                >
                  {{ age }}
                </button>
              </div>
              <p class="hint err" v-if="isMinor">Sorry, you need to be 18 or older to register.</p>

              <span class="lbl">Gender</span>
              <div class="chips">
                <button 
                  v-for="g in ['Female', 'Male', 'Other', 'Prefer not to say']" 
                  :key="g"
                  type="button" 
                  class="chip" 
                  :aria-pressed="form.gender === g"
                  @click="form.gender = g"
                >
                  {{ g }}
                </button>
              </div>

              <label class="lbl" for="fEdu">Highest qualification</label>
              <select class="field" id="fEdu" v-model="form.highest_qualification">
                <option value="">Select qualification</option>
                <option value="Below 10th">Below 10th</option>
                <option value="10th pass">10th pass</option>
                <option value="12th pass">12th pass</option>
                <option value="Diploma / ITI">Diploma / ITI</option>
                <option value="Graduate">Graduate</option>
                <option value="Postgraduate">Postgraduate</option>
              </select>

              <span class="lbl">Your vehicle</span>
              <div class="chips">
                <button 
                  v-for="v in ['Two-wheeler', 'Four-wheeler', 'Both', 'None']" 
                  :key="v"
                  type="button" 
                  class="chip" 
                  :aria-pressed="form.vehicle === v"
                  @click="form.vehicle = v"
                >
                  {{ v }}
                </button>
              </div>

              <span class="lbl">Valid driving licence?</span>
              <div class="chips">
                <button 
                  v-for="l in ['Yes', 'No']" 
                  :key="l"
                  type="button" 
                  class="chip" 
                  :aria-pressed="form.driving_licence === l"
                  @click="form.driving_licence = l"
                >
                  {{ l }}
                </button>
              </div>

              <div class="nav-row">
                <button type="button" class="btn ghost" @click="goToStep(1)">Back</button>
                <button type="button" class="btn" :disabled="!isStep2Valid" @click="goToStep(3)">Continue</button>
              </div>
            </div>

            <!-- STEP 3: How can we reach you? -->
            <div v-if="currentStep === 3">
              <h3>How can we reach you?</h3>
              <p class="sub">Step 3 of 3</p>

              <label class="lbl" for="fMob">Mobile number</label>
              <div class="prefix">
                <span>+91</span>
                <input 
                  class="field" 
                  id="fMob" 
                  v-model="form.mobile" 
                  inputmode="numeric" 
                  maxlength="10" 
                  autocomplete="tel-national" 
                  placeholder="98765 43210" 
                />
              </div>
              <p class="hint err" v-if="mobileError">{{ mobileError }}</p>

              <label class="check" style="margin-top: 10px;">
                <input type="checkbox" v-model="form.is_whatsapp_same" />
                <span>This number is on WhatsApp</span>
              </label>

              <div v-if="!form.is_whatsapp_same" class="conditional-field">
                <label class="lbl" for="fWa">WhatsApp number</label>
                <div class="prefix">
                  <span>+91</span>
                  <input class="field" id="fWa" v-model="form.whatsapp_number" inputmode="numeric" maxlength="10" placeholder="98765 43210" />
                </div>
              </div>

              <label class="lbl" for="fEmail">Email <em>(optional)</em></label>
              <input class="field" id="fEmail" type="email" v-model="form.email" autocomplete="email" placeholder="you@example.com" />

              <span class="lbl">How did you hear about us?</span>
              <div class="chips">
                <button 
                  v-for="r in ['On my own', 'A person referred me', 'Through an organisation']" 
                  :key="r"
                  type="button" 
                  class="chip" 
                  :aria-pressed="form.referral_source === r"
                  @click="form.referral_source = r"
                >
                  {{ r }}
                </button>
              </div>

              <div v-if="form.referral_source === 'A person referred me'" class="row2 conditional-field">
                <div>
                  <label class="lbl" for="fRefName">Their name</label>
                  <input class="field" id="fRefName" v-model="form.referral_name" placeholder="Full name of referrer" />
                </div>
                <div>
                  <label class="lbl" for="fRefMob">Their mobile or code <em>(optional)</em></label>
                  <input class="field" id="fRefMob" v-model="form.referral_contact" placeholder="98765 43210" />
                </div>
              </div>

              <div v-if="form.referral_source === 'Through an organisation'" class="conditional-field">
                <label class="lbl" for="fOrg">Organisation name</label>
                <input class="field" id="fOrg" v-model="form.referral_org" placeholder="e.g. self-help group, NGO, college" />
              </div>

              <details class="conds">
                <summary>Registration conditions</summary>
                <ul>
                  <li>Registration is an expression of interest and does not guarantee selection as a CME.</li>
                  <li>The first intake is limited to 150 candidates; early registrations are screened first.</li>
                  <li>Shortlisted candidates are invited to an information and selection meeting.</li>
                  <li>Selection depends on eligibility, verification, programme requirements and FMZ’s terms.</li>
                  <li>Selected CMEs can access equipment through FMZ’s finance partners, subject to their approval.</li>
                  <li>FMZ never charges a fee to register.</li>
                </ul>
              </details>

              <label class="check">
                <input type="checkbox" v-model="form.confirmed_accuracy" />
                <span>My details are accurate and complete.</span>
              </label>

              <label class="check">
                <input type="checkbox" v-model="form.confirmed_conditions" />
                <span>I accept the conditions and understand registering doesn’t guarantee selection.</span>
              </label>

              <label class="check">
                <input type="checkbox" v-model="form.confirmed_contact" />
                <span>FMZ may contact me by phone, WhatsApp or email about my registration. <Link href="/privacy-policy" target="_blank" class="policy-link">Privacy notice</Link></span>
              </label>

              <p class="err-box" v-if="errorMessage">{{ errorMessage }}</p>

              <div class="nav-row">
                <button type="button" class="btn ghost" @click="goToStep(2)">Back</button>
                <button 
                  type="button" 
                  class="btn" 
                  :disabled="form.processing || !isStep3Valid" 
                  @click="submitRegistration"
                >
                  {{ form.processing ? 'Submitting...' : 'Submit registration' }}
                </button>
              </div>
            </div>

            <!-- STEP 4: Success / Done -->
            <div v-if="currentStep === 4" class="done">
              <div class="tick">✓</div>
              <h3>Thank you, {{ registeredData?.name || 'Candidate' }}!</h3>
              <div class="id">{{ registeredData?.id }}</div>
              <p class="sub">
                Keep this registration ID. Your interest: <b>{{ registeredData?.programmes?.join(', ') }}</b>
              </p>
              <ol>
                <li>We review registrations in the order received.</li>
                <li>If shortlisted, you’ll get a WhatsApp invitation to an information meeting.</li>
                <li>Bring a photo ID and any certificates to that meeting.</li>
              </ol>
              <p class="small">FMZ will never ask you for money to register or be selected.</p>

              <div class="done-actions">
                <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="btn wa-btn">
                  <MessageSquare class="w-4 h-4 mr-2" />
                  <span>Connect with FMZ on WhatsApp</span>
                </a>
                <Link href="/glow" class="btn ghost">
                  <span>Return to FMZ Glow</span>
                </Link>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- 5. FAQ Section -->
      <section id="faq">
        <div class="wrap">
          <div class="center">
            <h2>Questions</h2>
          </div>
          <div class="faq">
            <details>
              <summary>Is registration free?</summary>
              <p>Yes. FMZ never charges a fee to register or to be selected. If anyone asks you for money in FMZ’s name, please report it to us.</p>
            </details>
            <details>
              <summary>Is this a job?</summary>
              <p>No. As a CME you run your own small service business, and FMZ supports you with training, a certificate, equipment finance, customers and the booking platform.</p>
            </details>
            <details>
              <summary>Do I need experience?</summary>
              <p>Not for Beauty &amp; Wellness or Carer programmes; full training is given. Nursing services need a valid nursing or health registration.</p>
            </details>
            <details>
              <summary>Do I have to buy the equipment?</summary>
              <p>Selected CMEs can get equipment financed through FMZ’s bank and NBFC partners, subject to their approval. Instalments come out of your earnings on the platform. All terms are explained before you commit.</p>
            </details>
            <details>
              <summary>How much can I earn, and where?</summary>
              <p>It depends on your programme, area and how many visits you take. We share realistic figures, and the areas in the first intake, at the information meeting.</p>
            </details>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="cme-footer">
      <div class="wrap ft">
        <span>© 2026 FMZ India · Certified Micro Entrepreneur Programme</span>
        <span class="ft-links">
          <Link href="/privacy-policy">Privacy notice</Link>
          <Link href="/terms-and-conditions">Terms</Link>
          <Link href="/cancellation-and-refunds">Refund policy</Link>
          <Link href="/glow">FMZ Glow</Link>
        </span>
      </div>
    </footer>

    <!-- Mobile sticky CTA bar -->
    <div class="mbar">
      <a class="btn" href="#register">Register free · 150 places</a>
    </div>
  </div>
</template>

<style scoped>
/* Page Scoped Styles matching the client HTML specification */
.cme-page {
  --bg: #FDF6F4;
  --surface: #FFFFFF;
  --tint: #F9E8E5;
  --ink: #2E1A1D;
  --muted: #6D5659;
  --wine: #7A2A35;
  --deep: #5A1F27;
  --cta: #A8243A;
  --gold: #D9B489;
  --line: #EFDCD8;
  --ok: #2F7A4F;
  --err: #B3261E;
  --note: #FFF4D6;
  --note-ink: #5C4300;
  --note-line: #E9C766;

  background: var(--bg);
  color: var(--ink);
  font-family: Montserrat, "Segoe UI", Roboto, Arial, sans-serif;
  line-height: 1.65;
  min-height: 100vh;
}

h1, h2, h3 {
  margin: 0;
  font-family: "Cormorant Garamond", Georgia, "Times New Roman", serif;
  font-weight: 600;
  line-height: 1.1;
  color: var(--ink);
}

h1 { font-size: clamp(2.6rem, 6vw, 4rem); }
h2 { font-size: clamp(2rem, 4vw, 2.7rem); }
h3 { font-size: 1.55rem; }

p { margin: 0; }

.wrap {
  width: min(1080px, 100% - 40px);
  margin-inline: auto;
}

section {
  padding: 80px 0;
}

.lede {
  color: var(--muted);
  max-width: 56ch;
  margin-top: 14px;
}

.center {
  text-align: center;
}

.center .lede {
  margin-inline: auto;
}

.btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  min-height: 50px;
  padding: 0 26px;
  border-radius: 999px;
  border: 1.5px solid var(--cta);
  background: var(--cta);
  color: #fff;
  font: 600 15px/1 Montserrat, Arial, sans-serif;
  text-decoration: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.btn:hover {
  filter: brightness(1.08);
}

.btn:disabled {
  opacity: 0.45;
  cursor: not-allowed;
  filter: none;
}

.btn.ghost {
  background: transparent;
  color: var(--wine);
  border-color: var(--wine);
}

.btn.wa-btn {
  background: #25D366;
  border-color: #25D366;
  color: #fff;
}

.small {
  font-size: 12.5px;
  color: var(--muted);
}

/* Header */
.cme-header {
  position: sticky;
  top: 0;
  z-index: 50;
  background: var(--surface);
  border-bottom: 1px solid var(--line);
}

.nav {
  display: flex;
  align-items: center;
  justify-content: space-between;
  height: 64px;
  gap: 12px;
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  text-decoration: none;
  color: var(--muted);
  font-size: 13px;
  font-weight: 600;
}

.logo .fmz {
  background: #B3121B;
  color: #fff;
  font: 700 18px/1 Montserrat, Arial, sans-serif;
  padding: 5px 7px;
  border-radius: 3px;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 14px;
}

.back-site-link {
  font-size: 13px;
  font-weight: 600;
  color: var(--wine);
  text-decoration: none;
}

.back-site-link:hover {
  text-decoration: underline;
}

.nav .btn {
  min-height: 42px;
  padding: 0 20px;
}

@media (max-width: 600px) {
  .logo .sub { display: none; }
  .back-site-link { display: none; }
}

/* Hero */
.hero {
  background: linear-gradient(var(--tint), var(--bg));
  padding: 64px 0 72px;
}

.hero-grid {
  display: grid;
  grid-template-columns: 1.05fr 0.95fr;
  gap: 56px;
  align-items: center;
}

.hero h1 em {
  font-style: italic;
  color: var(--wine);
}

.hero .lede {
  font-size: 17px;
}

.cta-row {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
  margin-top: 28px;
}

.hero-media {
  position: relative;
}

.photo-wrapper {
  aspect-ratio: 4/4.4;
  border-radius: 240px 240px 28px 28px;
  overflow: hidden;
  background: #F4D9D4;
  box-shadow: 0 16px 40px rgba(70, 20, 30, 0.08);
}

.photo {
  display: block;
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.badge {
  position: absolute;
  left: -22px;
  bottom: 30px;
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: 18px;
  padding: 14px 20px;
  box-shadow: 0 12px 32px rgba(70, 20, 30, 0.14);
}

.badge b {
  display: block;
  font: 600 2.4rem/1 "Cormorant Garamond", Georgia, serif;
  color: var(--cta);
}

.badge span {
  font-size: 13px;
  font-weight: 600;
}

@media (max-width: 860px) {
  .hero-grid {
    grid-template-columns: 1fr;
    gap: 36px;
  }
  .badge {
    left: 12px;
    bottom: 12px;
  }
}

/* Programmes */
.prog-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
  margin-top: 40px;
}

.prog {
  background: var(--surface);
  border: 1px solid var(--line);
  border-radius: 22px;
  overflow: hidden;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.02);
}

.prog-img-box {
  aspect-ratio: 4/3;
  overflow: hidden;
}

.prog-photo {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.img-placeholder {
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #F9E8E5, #EFDCD8);
}

.img-placeholder.carer {
  background: linear-gradient(135deg, #F3EBEA, #E8D6D3);
}

.placeholder-tag {
  background: rgba(255, 255, 255, 0.85);
  padding: 6px 14px;
  border-radius: 999px;
  font-size: 13px;
  font-weight: 600;
  color: var(--wine);
}

.prog div.body {
  padding: 22px;
}

.prog p {
  font-size: 14.5px;
  color: var(--muted);
  margin-top: 8px;
}

.prog .tag {
  display: inline-block;
  margin-top: 14px;
  font-size: 12px;
  font-weight: 600;
  color: var(--wine);
  background: var(--tint);
  padding: 6px 12px;
  border-radius: 999px;
}

@media (max-width: 860px) {
  .prog-grid {
    grid-template-columns: 1fr;
  }
}

/* Steps */
.how {
  background: var(--surface);
}

.steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 28px;
  margin-top: 40px;
}

.step .n {
  font: 600 2.6rem/1 "Cormorant Garamond", Georgia, serif;
  color: var(--cta);
}

.step b {
  display: block;
  margin-top: 8px;
  font-size: 15px;
}

.step p {
  font-size: 14px;
  color: var(--muted);
  margin-top: 4px;
}

@media (max-width: 860px) {
  .steps { grid-template-columns: 1fr 1fr; }
}

@media (max-width: 480px) {
  .steps { grid-template-columns: 1fr; }
}

/* Registration section */
.reg {
  background: var(--deep);
  color: #fff;
}

.reg h2 {
  color: #fff;
}

.reg-grid {
  display: grid;
  grid-template-columns: 0.8fr 1.2fr;
  gap: 48px;
  align-items: start;
}

.reg-side p {
  color: #F2DADA;
  margin-top: 12px;
}

.reg-side ul {
  list-style: none;
  padding: 0;
  margin: 24px 0 0;
  display: grid;
  gap: 12px;
  font-size: 14.5px;
}

.reg-side li {
  display: flex;
  gap: 10px;
}

.reg-side li::before {
  content: "✓";
  color: var(--gold);
  font-weight: 700;
}

.form {
  background: var(--surface);
  color: var(--ink);
  border-radius: 24px;
  padding: 28px;
  box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
}

.progress {
  display: flex;
  gap: 8px;
  margin-bottom: 20px;
}

.progress span {
  flex: 1;
  height: 5px;
  border-radius: 5px;
  background: var(--line);
  transition: background 0.3s ease;
}

.progress span.on {
  background: var(--cta);
}

.form h3 {
  font-size: 1.7rem;
}

.form .sub {
  font-size: 13.5px;
  color: var(--muted);
  margin: 2px 0 16px;
}

.lbl {
  display: block;
  font-size: 13px;
  font-weight: 600;
  margin: 18px 0 8px;
}

.lbl em {
  font-style: normal;
  font-weight: 400;
  color: var(--muted);
}

.field {
  width: 100%;
  min-height: 50px;
  border: 1.5px solid var(--line);
  border-radius: 14px;
  padding: 12px 14px;
  font: 15px Montserrat, Arial, sans-serif;
  background: var(--surface);
  color: var(--ink);
  transition: border-color 0.2s;
}

.field:focus {
  outline: none;
  border-color: var(--wine);
}

select.field {
  appearance: none;
  background-image: linear-gradient(45deg, transparent 50%, var(--muted) 50%), linear-gradient(135deg, var(--muted) 50%, transparent 50%);
  background-position: calc(100% - 20px) 22px, calc(100% - 14px) 22px;
  background-size: 6px 6px;
  background-repeat: no-repeat;
  padding-right: 36px;
}

.row2 {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 12px;
}

.row3 {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 12px;
}

.prefix {
  display: flex;
}

.prefix span {
  display: grid;
  place-items: center;
  padding: 0 12px;
  border: 1.5px solid var(--line);
  border-right: 0;
  border-radius: 14px 0 0 14px;
  font-weight: 600;
  color: var(--muted);
  background: var(--tint);
}

.prefix .field {
  border-radius: 0 14px 14px 0;
}

.cards {
  display: grid;
  gap: 10px;
}

.card-opt {
  display: flex;
  gap: 14px;
  align-items: center;
  text-align: left;
  width: 100%;
  border: 1.5px solid var(--line);
  background: var(--surface);
  color: var(--ink);
  border-radius: 16px;
  padding: 14px 16px;
  font: inherit;
  cursor: pointer;
  transition: all 0.2s;
}

.card-opt .box {
  flex: none;
  width: 22px;
  height: 22px;
  border-radius: 7px;
  border: 2px solid var(--line);
  display: grid;
  place-items: center;
  font-size: 13px;
  color: #fff;
  transition: all 0.2s;
}

.card-opt b {
  display: block;
  font-size: 15px;
}

.card-opt small {
  font-size: 12.5px;
  color: var(--muted);
}

.card-opt[aria-pressed="true"] {
  border-color: var(--cta);
  background: var(--tint);
}

.card-opt[aria-pressed="true"] .box {
  background: var(--cta);
  border-color: var(--cta);
}

.card-opt[aria-pressed="true"] .box::after {
  content: "✓";
}

.chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.chip {
  border: 1.5px solid var(--line);
  background: var(--surface);
  color: var(--ink);
  border-radius: 999px;
  padding: 10px 16px;
  font: 600 14px Montserrat, Arial, sans-serif;
  cursor: pointer;
  transition: all 0.2s;
}

.chip[aria-pressed="true"] {
  background: var(--cta);
  border-color: var(--cta);
  color: #fff;
}

.hint {
  font-size: 12.5px;
  margin-top: 6px;
  min-height: 1em;
}

.hint.err {
  color: var(--err);
}

.conditional-field {
  margin-top: 14px;
}

.check {
  display: flex;
  gap: 10px;
  align-items: flex-start;
  font-size: 13.5px;
  margin-top: 12px;
  cursor: pointer;
}

.check input {
  flex: none;
  width: 20px;
  height: 20px;
  margin-top: 1px;
  accent-color: var(--cta);
}

.policy-link {
  color: var(--wine);
  text-decoration: underline;
}

.conds {
  background: var(--tint);
  border-radius: 14px;
  padding: 12px 16px;
  margin-top: 18px;
  font-size: 13px;
}

.conds summary {
  cursor: pointer;
  font-weight: 600;
}

.conds ul {
  margin: 10px 0 0;
  padding-left: 18px;
  display: grid;
  gap: 5px;
  color: var(--muted);
}

.nav-row {
  display: flex;
  gap: 10px;
  margin-top: 24px;
}

.nav-row .btn {
  flex: 1;
}

.err-box {
  margin-top: 14px;
  font-size: 13px;
  color: var(--err);
  font-weight: 600;
}

.done {
  text-align: center;
  padding: 10px 0;
}

.done .tick {
  width: 64px;
  height: 64px;
  border-radius: 50%;
  background: var(--ok);
  color: #fff;
  display: grid;
  place-items: center;
  font-size: 30px;
  margin: 0 auto 14px;
}

.done .id {
  display: inline-block;
  margin: 14px 0;
  font-weight: 700;
  font-size: 18px;
  letter-spacing: 0.04em;
  background: var(--tint);
  padding: 10px 16px;
  border-radius: 12px;
  font-family: monospace;
}

.done ol {
  text-align: left;
  font-size: 14px;
  display: grid;
  gap: 6px;
  margin: 16px 0;
  padding-left: 20px;
  color: var(--ink);
}

.done-actions {
  display: flex;
  flex-direction: column;
  gap: 10px;
  margin-top: 24px;
}

@media (max-width: 900px) {
  .reg-grid {
    grid-template-columns: 1fr;
    gap: 28px;
  }
  .form {
    padding: 22px;
  }
}

@media (max-width: 480px) {
  .row2, .row3 {
    grid-template-columns: 1fr;
  }
}

/* FAQ */
.faq {
  max-width: 720px;
  margin: 32px auto 0;
}

.faq details {
  border-bottom: 1px solid var(--line);
  padding: 16px 0;
}

.faq summary {
  cursor: pointer;
  font-weight: 600;
  list-style: none;
  display: flex;
  justify-content: space-between;
  gap: 16px;
}

.faq summary::after {
  content: "+";
  font-size: 22px;
  line-height: 1;
  color: var(--cta);
}

.faq details[open] summary::after {
  content: "−";
}

.faq summary::-webkit-details-marker {
  display: none;
}

.faq p {
  margin-top: 10px;
  color: var(--muted);
  font-size: 14.5px;
}

/* Footer */
.cme-footer {
  background: var(--surface);
  border-top: 1px solid var(--line);
  padding: 32px 0 100px;
  font-size: 13.5px;
  color: var(--muted);
}

.ft {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 16px;
  align-items: center;
}

.ft-links {
  display: flex;
  gap: 16px;
  flex-wrap: wrap;
}

.ft a {
  color: inherit;
  text-decoration: none;
}

.ft a:hover {
  color: var(--wine);
  text-decoration: underline;
}

/* Mobile Bar */
.mbar {
  display: none;
}

@media (max-width: 760px) {
  .mbar {
    display: flex;
    position: fixed;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 55;
    background: var(--surface);
    border-top: 1px solid var(--line);
    padding: 10px 16px calc(10px + env(safe-area-inset-bottom, 0px));
  }
  .mbar .btn {
    flex: 1;
  }
}
</style>

