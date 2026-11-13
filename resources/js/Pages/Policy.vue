<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import TopBar from '@/Components/Frontend/TopBar.vue';
import Header from '@/Components/Frontend/Header.vue';
import Footer from '@/Components/Frontend/Footer.vue';
import WhatsAppFloat from '@/Components/Frontend/WhatsAppFloat.vue';
import { ArrowLeft, ShieldCheck, FileText, RefreshCw, MessageSquare } from 'lucide-vue-next';

const props = defineProps({
  page: {
    type: Object,
    required: true,
  },
  settings: {
    type: Object,
    default: () => ({}),
  },
});

const policyTabs = [
  { slug: 'privacy-policy', label: 'Privacy Policy', icon: ShieldCheck },
  { slug: 'terms-and-conditions', label: 'Terms & Conditions', icon: FileText },
  { slug: 'cancellation-and-refunds', label: 'Cancellation & Refunds', icon: RefreshCw },
];

const parsedContent = computed(() => {
  const text = props.page?.content || '';
  if (!text) return '';

  const raw = text.replace(/\r\n/g, '\n');
  const blocks = raw.split(/\n\s*\n/);

  const formatInline = (str) => {
    return str
      .replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>')
      .replace(/\*(.+?)\*/g, '<em>$1</em>')
      .replace(/"(.+?)"/g, '&ldquo;$1&rdquo;');
  };

  return blocks.map(block => {
    const trimmed = block.trim();
    if (!trimmed) return '';

    // Headings
    if (trimmed.startsWith('### ')) {
      return `<h3 class="policy-h3">${formatInline(trimmed.replace(/^###\s+/, ''))}</h3>`;
    }
    if (trimmed.startsWith('## ')) {
      return `<h2 class="policy-h2">${formatInline(trimmed.replace(/^##\s+/, ''))}</h2>`;
    }
    if (trimmed.startsWith('# ')) {
      return `<h2 class="policy-h2">${formatInline(trimmed.replace(/^#\s+/, ''))}</h2>`;
    }

    // List items
    const lines = trimmed.split('\n');
    const isList = lines.every(line => line.trim().startsWith('- ') || line.trim().startsWith('* '));
    if (isList) {
      const items = lines.map(line => {
        const itemText = line.trim().replace(/^[-*]\s+/, '');
        return `<li>${formatInline(itemText)}</li>`;
      }).join('');
      return `<ul class="policy-ul">${items}</ul>`;
    }

    // Paragraph
    return `<p class="policy-p">${formatInline(trimmed).replace(/\n/g, '<br>')}</p>`;
  }).join('\n');
});

const whatsappUrl = computed(() => {
  const phone = (props.settings?.whatsapp_number || '+917303706047').replace(/[^0-9]/g, '');
  const msg = encodeURIComponent(`Hi FMZ Glow, I have a question regarding your ${props.page?.title || 'policies'}.`);
  return `https://wa.me/${phone}?text=${msg}`;
});
</script>

<template>
  <Head :title="`${page.title || 'Legal & Policy'} · FMZ Glow`">
    <meta v-if="page.meta_description" name="description" :content="page.meta_description" />
  </Head>

  <div class="fmz-frontend policy-page-wrapper">
    <!-- Top Announcement Banner -->
    <TopBar :text="settings.topbar_text" />

    <!-- Sticky Header & Nav -->
    <Header />

    <main class="policy-main">
      <div class="wrap">
        <!-- Breadcrumb & Back Link -->
        <div class="breadcrumb-bar">
          <Link href="/glow" class="back-link">
            <ArrowLeft class="w-4 h-4" />
            <span>Back to Home</span>
          </Link>
          <span class="sep">/</span>
          <span class="curr">Policies</span>
          <span class="sep">/</span>
          <span class="curr highlight">{{ page.title }}</span>
        </div>

        <!-- Policy Switcher Tabs -->
        <div class="policy-tabs">
          <Link
            v-for="tab in policyTabs"
            :key="tab.slug"
            :href="`/${tab.slug}`"
            class="tab-btn"
            :class="{ active: page.slug === tab.slug }"
          >
            <component :is="tab.icon" class="w-4 h-4" />
            <span>{{ tab.label }}</span>
          </Link>
        </div>

        <!-- Main Document Article -->
        <article class="policy-article">
          <header class="policy-header">
            <span class="eyebrow">FMZ Glow · Official Legal Document</span>
            <h1 class="page-title">{{ page.title }}</h1>
            <p class="updated-time">
              Last updated: {{ page.updated_at ? new Date(page.updated_at).toLocaleDateString('en-IN', { year: 'numeric', month: 'long', day: 'numeric' }) : 'November 2026' }} · FMZ India
            </p>
          </header>

          <div class="policy-body" v-html="parsedContent"></div>

          <!-- Bottom Help Card -->
          <div class="support-card">
            <div class="support-icon">
              <MessageSquare class="w-5 h-5 text-[#7A2A2E]" />
            </div>
            <div class="support-info">
              <h4>Questions about this policy?</h4>
              <p>Our client care team is available every day from 9:00 AM to 8:00 PM to assist you with transparent answers.</p>
            </div>
            <a :href="whatsappUrl" target="_blank" rel="noopener noreferrer" class="support-cta">
              Chat on WhatsApp
            </a>
          </div>
        </article>
      </div>
    </main>

    <!-- Footer -->
    <Footer :settings="settings" />

    <!-- WhatsApp Floating Action -->
    <WhatsAppFloat 
      :whatsapp-number="settings.whatsapp_number"
      :whatsapp-message="settings.whatsapp_default_message"
    />
  </div>
</template>

<style scoped>
.policy-page-wrapper {
  background: var(--bg);
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.policy-main {
  flex: 1;
  padding: 36px 0 72px;
}

.wrap {
  max-width: 920px;
  margin: 0 auto;
  padding: 0 20px;
}

.breadcrumb-bar {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 13px;
  color: var(--muted);
  margin-bottom: 24px;
  flex-wrap: wrap;
}

.back-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  color: var(--wine);
  text-decoration: none;
  font-weight: 600;
  transition: opacity 0.2s;
}

.back-link:hover {
  opacity: 0.8;
  text-decoration: underline;
}

.sep {
  color: var(--line);
}

.curr.highlight {
  color: var(--ink);
  font-weight: 600;
}

.policy-tabs {
  display: flex;
  gap: 8px;
  margin-bottom: 32px;
  overflow-x: auto;
  padding-bottom: 4px;
}

.tab-btn {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  padding: 10px 18px;
  font-size: 13px;
  font-weight: 600;
  color: var(--ink);
  background: #ffffff;
  border: 1px solid var(--line);
  border-radius: 999px;
  text-decoration: none;
  white-space: nowrap;
  transition: all 0.2s ease;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
}

.tab-btn:hover {
  background: #fdf5f2;
  border-color: var(--wine);
  color: var(--wine);
}

.tab-btn.active {
  background: var(--wine);
  border-color: var(--wine);
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(122, 42, 46, 0.25);
}

.policy-article {
  background: #ffffff;
  border: 1px solid var(--line);
  border-radius: 24px;
  padding: 44px 48px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
}

.policy-header {
  border-bottom: 1px solid var(--line);
  padding-bottom: 24px;
  margin-bottom: 32px;
}

.eyebrow {
  display: block;
  font-size: 11px;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: var(--wine);
  margin-bottom: 8px;
}

.page-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: clamp(32px, 5vw, 44px);
  line-height: 1.15;
  color: var(--ink);
  margin: 0 0 10px;
  font-weight: 600;
}

.updated-time {
  font-size: 13px;
  color: var(--muted);
  margin: 0;
}

:deep(.policy-h3) {
  font-family: 'Cormorant Garamond', serif;
  font-size: 24px;
  font-weight: 600;
  color: var(--ink);
  margin: 32px 0 12px;
  padding-bottom: 6px;
  border-bottom: 1px dashed var(--line);
}

:deep(.policy-h2) {
  font-family: 'Cormorant Garamond', serif;
  font-size: 28px;
  font-weight: 600;
  color: var(--wine);
  margin: 36px 0 14px;
}

:deep(.policy-p) {
  font-size: 15px;
  line-height: 1.75;
  color: #3b3534;
  margin: 0 0 18px;
}

:deep(.policy-ul) {
  margin: 12px 0 24px 24px;
  padding: 0;
  list-style: disc !important;
}

:deep(.policy-ul li) {
  font-size: 15px;
  line-height: 1.7;
  color: #3b3534;
  margin-bottom: 8px;
  display: list-item !important;
}

:deep(.policy-ul li::marker) {
  color: var(--wine);
}

:deep(strong) {
  color: var(--ink);
  font-weight: 600;
}

.support-card {
  margin-top: 48px;
  padding: 24px;
  background: #fdf8f6;
  border: 1px solid #fae6de;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 20px;
  flex-wrap: wrap;
}

.support-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: #ffffff;
  border: 1px solid #fae6de;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.support-info {
  flex: 1;
  min-width: 240px;
}

.support-info h4 {
  font-size: 15px;
  font-weight: 700;
  color: var(--ink);
  margin: 0 0 4px;
}

.support-info p {
  font-size: 13px;
  color: var(--muted);
  margin: 0;
  line-height: 1.5;
}

.support-cta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: var(--wine);
  color: #ffffff;
  padding: 10px 20px;
  font-size: 13px;
  font-weight: 600;
  border-radius: 999px;
  text-decoration: none;
  transition: all 0.2s ease;
  white-space: nowrap;
}

.support-cta:hover {
  background: #5a1e22;
  box-shadow: 0 2px 8px rgba(122, 42, 46, 0.3);
}

@media (max-width: 768px) {
  .policy-article {
    padding: 28px 20px;
    border-radius: 18px;
  }
  .support-card {
    flex-direction: column;
    align-items: flex-start;
  }
  .support-cta {
    width: 100%;
    justify-content: center;
  }
}
</style>
