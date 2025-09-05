// Multi-language support for SmartBus Tracker
const languages = {
  en: {
    nav_live_tracking: "Live Tracking",
    nav_routes: "Routes",
    nav_help: "Help",
    nav_login: "Login",
    nav_logout: "Logout",
    nav_welcome: "Welcome",
    hero_title: "Real-Time Bus Tracking",
    hero_subtitle: "Track buses live, check routes & estimated arrival times for small cities",
    search_from: "From",
    search_to: "To",
    search_button: "Find Route",
    active_buses: "Active Buses",
    add_demo_bus: "Add Demo Bus",
    popular_routes_title: "Popular Routes",
    features_title: "Why Choose SmartBus Tracker?",
    help_title: "Help & Support",
    chatbot_title: "SmartBus Assistant",
    chatbot_welcome: "Hello! 👋 I'm your SmartBus Assistant. How can I help you today?",
    login_title: "SmartBus Tracker",
    login_subtitle: "Sign in to your account",
    login_email: "Email address",
    login_password: "Password",
    login_button: "Sign in",
    login_no_account: "Don't have an account?",
    login_signup: "Sign up here",
    register_title: "Full Name",
    register_button: "Create Account",
    register_has_account: "Already have an account?",
    register_signin: "Sign in here",
    demo_credentials: "Demo Credentials:",
    nav_logout: "Logout",
    nav_welcome: "Welcome"
  },
  hi: {
    nav_live_tracking: "लाइव ट्रैकिंग",
    nav_routes: "रूट्स",
    nav_help: "सहायता",
    nav_login: "लॉगिन",
    nav_logout: "लॉगआउट",
    nav_welcome: "स्वागत है",
    hero_title: "रीयल-टाइम बस ट्रैकिंग",
    hero_subtitle: "बसों को लाइव ट्रैक करें, रूट्स और अनुमानित आगमन समय देखें",
    search_from: "कहाँ से",
    search_to: "कहाँ तक",
    search_button: "रूट खोजें",
    active_buses: "सक्रिय बसें",
    add_demo_bus: "डेमो बस जोड़ें",
    popular_routes_title: "लोकप्रिय रूट्स",
    features_title: "स्मार्टबस ट्रैकर क्यों चुनें?",
    help_title: "सहायता और समर्थन",
    chatbot_title: "स्मार्टबस असिस्टेंट",
    chatbot_welcome: "नमस्ते! 👋 मैं आपका स्मार्टबस असिस्टेंट हूं। आज मैं आपकी कैसे मदद कर सकता हूं?",
    login_title: "स्मार्टबस ट्रैकर",
    login_subtitle: "अपने खाते में साइन इन करें",
    login_email: "ईमेल पता",
    login_password: "पासवर्ड",
    login_button: "साइन इन करें",
    login_no_account: "खाता नहीं है?",
    login_signup: "यहाँ साइन अप करें",
    register_title: "पूरा नाम",
    register_button: "खाता बनाएं",
    register_has_account: "पहले से खाता है?",
    register_signin: "यहाँ साइन इन करें",
    demo_credentials: "डेमो क्रेडेंशियल्स:",
    nav_logout: "लॉगआउट",
    nav_welcome: "स्वागत है"
  }
};

class LanguageManager {
  constructor() {
    this.currentLanguage = localStorage.getItem('language') || 'en';
    this.init();
  }

  init() {
    this.createLanguageSwitcher();
    this.updateLanguage(this.currentLanguage);
  }

  createLanguageSwitcher() {
    const switcherHTML = `
      <div id="language-switcher" class="fixed top-4 left-4 z-50">
        <div class="bg-white rounded-lg shadow-lg border border-gray-200 p-2">
          <button id="language-toggle" class="flex items-center space-x-2 px-3 py-2 text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">
            <span id="current-lang-text">${this.currentLanguage === 'en' ? 'English' : 'हिंदी'}</span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
          </button>
          <div id="language-dropdown" class="hidden absolute top-full left-0 mt-1 bg-white rounded-lg shadow-lg border border-gray-200 min-w-[120px]">
            <button class="language-option w-full text-left px-3 py-2 text-sm hover:bg-gray-100 transition-colors" data-lang="en">
              <span class="flag">🇺🇸</span> English
            </button>
            <button class="language-option w-full text-left px-3 py-2 text-sm hover:bg-gray-100 transition-colors" data-lang="hi">
              <span class="flag">🇮🇳</span> हिंदी
            </button>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', switcherHTML);
    this.bindLanguageEvents();
  }

  bindLanguageEvents() {
    const toggle = document.getElementById('language-toggle');
    const dropdown = document.getElementById('language-dropdown');
    const options = document.querySelectorAll('.language-option');

    toggle.addEventListener('click', () => {
      dropdown.classList.toggle('hidden');
    });

    options.forEach(option => {
      option.addEventListener('click', (e) => {
        const lang = e.target.closest('.language-option').dataset.lang;
        this.updateLanguage(lang);
        dropdown.classList.add('hidden');
      });
    });

    document.addEventListener('click', (e) => {
      if (!e.target.closest('#language-switcher')) {
        dropdown.classList.add('hidden');
      }
    });
  }

  updateLanguage(lang) {
    this.currentLanguage = lang;
    localStorage.setItem('language', lang);
    
    const currentLangText = document.getElementById('current-lang-text');
    if (currentLangText) {
      currentLangText.textContent = lang === 'en' ? 'English' : 'हिंदी';
    }

    this.translatePage();
  }

  translatePage() {
    const elements = document.querySelectorAll('[data-translate]');
    elements.forEach(element => {
      const key = element.getAttribute('data-translate');
      const translation = this.getTranslation(key);
      if (translation) {
        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA') {
          element.placeholder = translation;
        } else {
          element.textContent = translation;
        }
      }
    });

    if (window.chatbot) {
      window.chatbot.updateLanguage(this.currentLanguage);
    }
  }

  getTranslation(key) {
    return languages[this.currentLanguage][key] || languages['en'][key] || key;
  }
}

let languageManager;
document.addEventListener('DOMContentLoaded', () => {
  languageManager = new LanguageManager();
  window.languageManager = languageManager;
});

window.LanguageManager = LanguageManager;
window.languages = languages;
