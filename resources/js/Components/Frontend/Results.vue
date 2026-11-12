<script setup>
const props = defineProps({
  results: {
    type: Array,
    default: () => []
  },
  reviews: {
    type: Array,
    default: () => []
  }
});

const defaultResults = [
  {
    client_name: 'Priya, 28',
    plan_info: 'FMZ Glow Facial, 3 visits over 12 weeks',
    concern: 'Dull, tired skin',
    quote: '“My skin looks fresher and my makeup sits better.”',
    image_before: '/images/priya.jpg',
    image_after: '/images/priya.jpg'
  },
  {
    client_name: 'Neha, 34',
    plan_info: 'FMZ Glow Cleanse, 2 visits over 4 weeks',
    concern: 'Dry, tight skin',
    quote: '“It feels soft and comfortable all day.”',
    image_before: '/images/neha.jpg',
    image_after: '/images/neha.jpg'
  }
];

const defaultReviews = [
  {
    quote: '“The facial was so relaxing and my skin felt soft and refreshed. The specialist made me feel comfortable throughout.”',
    client_name: 'Mansi Joshi, Ahmedabad'
  },
  {
    quote: '“The antioxidant scan was new for me. I understand my skin and diet much better now.”',
    client_name: 'Hetal Patel, Gandhinagar'
  },
  {
    quote: '“Having a certified woman specialist visit my home was wonderful and convenient. Truly authentic Nu Skin care.”',
    client_name: 'Pooja Shah, Surat'
  }
];
</script>

<template>
  <div>
    <!-- Real Results & Reviews -->
    <section>
      <div class="wrap">
        <div class="center">
          <h2>Real results, in her words</h2>
          <p class="lede">Every photo is shared with the client’s written permission, with no filters.</p>
        </div>

        <div class="res-grid">
          <article 
            v-for="(res, idx) in (results && results.length ? results : defaultResults)" 
            :key="res.id || idx" 
            class="res"
          >
            <div class="ba">
              <div>Before (client photo)</div>
              <div>After (client photo)</div>
            </div>
            <div class="res-body">
              <h3>{{ res.client_name }}</h3>
              <dl>
                <dt>Visit</dt>
                <dd>{{ res.plan_info || res.service_tag || 'FMZ Glow Facial' }}</dd>
                <dt>Concern</dt>
                <dd>{{ res.concern || 'Dull, tired skin' }}</dd>
              </dl>
              <blockquote>{{ res.quote }}</blockquote>
            </div>
          </article>
        </div>

        <p class="small center" style="margin-top: 16px;">
          Results vary with skin type, routine and number of visits. These are cosmetic services, not medical treatments.
        </p>

        <!-- Reviews Grid -->
        <div class="rev-grid">
          <figure 
            v-for="(rev, rIdx) in (reviews && reviews.length ? reviews.slice(0, 3) : defaultReviews)" 
            :key="rev.id || rIdx" 
            class="rev" 
            style="margin: 0;"
          >
            <div class="stars" aria-label="5 out of 5">★★★★★</div>
            <p>{{ rev.quote }}</p>
            <cite>{{ rev.client_name }}</cite>
          </figure>
        </div>
      </div>
    </section>

    <!-- Checklist / Hygiene Section -->
    <section style="padding-top: 0;">
      <div class="wrap hyg">
        <div>
          <h2>Our checklist, every visit</h2>
          <p class="lede">
            Before the Galvanic Spa, your specialist asks about pregnancy, implanted electrical devices, metal implants and skin conditions, and skips that step if it isn’t right for you.
          </p>
        </div>
        <div class="checks">
          <div>Sealed, genuine Nu Skin products</div>
          <div>Fresh towels and headband</div>
          <div>Devices cleaned before every visit</div>
          <div>Skin and health check first</div>
        </div>
      </div>
    </section>
  </div>
</template>
