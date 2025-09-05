// SmartBus Tracker Chatbot
class SmartBusChatbot {
  constructor() {
    this.isOpen = false;
    this.messages = [];
    this.currentUser = null;
    this.init();
  }

  init() {
    this.createChatbotHTML();
    this.bindEvents();
    this.addWelcomeMessage();
  }

  createChatbotHTML() {
    const chatbotHTML = `
      <!-- Chatbot Container -->
      <div id="chatbot-container" class="fixed bottom-4 right-4 z-50">
        <!-- Chat Button -->
        <button id="chatbot-toggle" class="bg-blue-600 hover:bg-blue-700 text-white rounded-full p-4 shadow-lg transition-all duration-300 transform hover:scale-110">
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
          </svg>
        </button>

        <!-- Chat Window -->
        <div id="chatbot-window" class="hidden absolute bottom-16 right-0 w-80 h-96 bg-white rounded-lg shadow-2xl border border-gray-200 flex flex-col">
          <!-- Header -->
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 text-white p-4 rounded-t-lg flex items-center justify-between">
            <div class="flex items-center space-x-2">
              <div class="w-8 h-8 bg-white rounded-full flex items-center justify-center">
                <span class="text-blue-600 text-lg">🚌</span>
              </div>
              <div>
                <h3 class="font-semibold">SmartBus Assistant</h3>
                <p class="text-xs opacity-90">Online • Ready to help</p>
              </div>
            </div>
            <button id="chatbot-close" class="text-white hover:text-gray-200">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>

          <!-- Messages Area -->
          <div id="chatbot-messages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-gray-50">
            <!-- Messages will be added here -->
          </div>

          <!-- Input Area -->
          <div class="p-4 border-t border-gray-200 bg-white rounded-b-lg">
            <div class="flex space-x-2">
              <input type="text" id="chatbot-input" placeholder="Type your message..." 
                     class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
              <button id="chatbot-send" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
              </button>
            </div>
            
            <!-- Quick Actions -->
            <div class="mt-3 flex flex-wrap gap-2">
              <button class="quick-action bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs transition-colors" data-action="track-bus">
                Track Bus
              </button>
              <button class="quick-action bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs transition-colors" data-action="find-route">
                Find Route
              </button>
              <button class="quick-action bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-full text-xs transition-colors" data-action="help">
                Help
              </button>
            </div>
          </div>
        </div>
      </div>
    `;

    document.body.insertAdjacentHTML('beforeend', chatbotHTML);
  }

  bindEvents() {
    // Toggle chatbot
    document.getElementById('chatbot-toggle').addEventListener('click', () => {
      this.toggleChatbot();
    });

    document.getElementById('chatbot-close').addEventListener('click', () => {
      this.closeChatbot();
    });

    // Send message
    document.getElementById('chatbot-send').addEventListener('click', () => {
      this.sendMessage();
    });

    document.getElementById('chatbot-input').addEventListener('keypress', (e) => {
      if (e.key === 'Enter') {
        this.sendMessage();
      }
    });

    // Quick actions
    document.querySelectorAll('.quick-action').forEach(button => {
      button.addEventListener('click', (e) => {
        const action = e.target.dataset.action;
        this.handleQuickAction(action);
      });
    });
  }

  toggleChatbot() {
    const window = document.getElementById('chatbot-window');
    this.isOpen = !this.isOpen;
    
    if (this.isOpen) {
      window.classList.remove('hidden');
      window.classList.add('animate-slide-up');
      document.getElementById('chatbot-input').focus();
    } else {
      this.closeChatbot();
    }
  }

  closeChatbot() {
    const window = document.getElementById('chatbot-window');
    this.isOpen = false;
    window.classList.add('hidden');
  }

  addWelcomeMessage() {
    const welcomeMessage = this.getLocalizedMessage('chatbot_welcome');
    this.addMessage('bot', welcomeMessage, 'welcome');
  }

  getLocalizedMessage(key) {
    if (window.languageManager) {
      return window.languageManager.getTranslation(key);
    }
    return languages['en'][key] || key;
  }

  updateLanguage(lang) {
    // Update chatbot title and status
    const titleElement = document.querySelector('#chatbot-window h3');
    if (titleElement) {
      titleElement.textContent = this.getLocalizedMessage('chatbot_title');
    }
    
    // Update placeholder
    const inputElement = document.getElementById('chatbot-input');
    if (inputElement) {
      inputElement.placeholder = lang === 'hi' ? 'अपना संदेश लिखें...' : 'Type your message...';
    }
  }

  addMessage(sender, text, type = 'text') {
    const messagesContainer = document.getElementById('chatbot-messages');
    const messageDiv = document.createElement('div');
    const timestamp = new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

    messageDiv.className = `flex ${sender === 'user' ? 'justify-end' : 'justify-start'}`;
    
    const messageContent = `
      <div class="max-w-xs ${sender === 'user' ? 'bg-blue-600 text-white' : 'bg-white border border-gray-200'} rounded-lg px-3 py-2 shadow-sm">
        <p class="text-sm">${text}</p>
        <p class="text-xs ${sender === 'user' ? 'text-blue-100' : 'text-gray-500'} mt-1">${timestamp}</p>
      </div>
    `;

    messageDiv.innerHTML = messageContent;
    messagesContainer.appendChild(messageDiv);
    messagesContainer.scrollTop = messagesContainer.scrollHeight;

    // Store message
    this.messages.push({ sender, text, type, timestamp });
  }

  sendMessage() {
    const input = document.getElementById('chatbot-input');
    const message = input.value.trim();
    
    if (message) {
      this.addMessage('user', message);
      input.value = '';
      
      // Simulate typing delay
      setTimeout(() => {
        this.processUserMessage(message);
      }, 500);
    }
  }

  handleQuickAction(action) {
    let message = '';
    switch (action) {
      case 'track-bus':
        message = 'How do I track a bus?';
        break;
      case 'find-route':
        message = 'How do I find a route?';
        break;
      case 'help':
        message = 'I need help';
        break;
    }
    
    if (message) {
      this.addMessage('user', message);
      setTimeout(() => {
        this.processUserMessage(message);
      }, 500);
    }
  }

  processUserMessage(message) {
    const lowerMessage = message.toLowerCase();
    let response = '';

    // Bus tracking related queries
    if (lowerMessage.includes('track') || lowerMessage.includes('bus location') || lowerMessage.includes('where is')) {
      response = 'To track buses, use the "Add Demo Bus" button on the right panel to add test buses, then you can see them on the map in real-time! 🚌📍';
    }
    else if (lowerMessage.includes('route') || lowerMessage.includes('find route') || lowerMessage.includes('how to find')) {
      response = 'To find routes, use the search box at the top of the page. Enter your starting location and destination, then click "Find Route" to see available options! 🗺️';
    }
    else if (lowerMessage.includes('login') || lowerMessage.includes('sign in') || lowerMessage.includes('account')) {
      response = 'You can log in using the demo credentials: Email: admin@smartbus.com, Password: admin123. Or create a new account using the registration form! 🔐';
    }
    else if (lowerMessage.includes('help') || lowerMessage.includes('support') || lowerMessage.includes('problem')) {
      response = 'I\'m here to help! You can ask me about:\n• How to track buses\n• Finding routes\n• Login issues\n• General questions\nWhat specific help do you need? 🤝';
    }
    else if (lowerMessage.includes('time') || lowerMessage.includes('schedule') || lowerMessage.includes('when')) {
      response = 'Bus schedules vary by route. Popular routes like PB-12 run every 15 minutes, PB-21 every 20 minutes, and PB-45 every 25 minutes. Check the route details for specific times! ⏰';
    }
    else if (lowerMessage.includes('cost') || lowerMessage.includes('price') || lowerMessage.includes('fare')) {
      response = 'Bus fares typically range from ₹10-50 depending on the route and distance. Contact your local transport authority for exact pricing information! 💰';
    }
    else if (lowerMessage.includes('contact') || lowerMessage.includes('phone') || lowerMessage.includes('email')) {
      response = 'For direct support, you can contact us at:\n📧 support@smartbus.com\n📞 1800-SMARTBUS\nWe\'re available 24/7! 📞';
    }
    else if (lowerMessage.includes('hello') || lowerMessage.includes('hi') || lowerMessage.includes('hey')) {
      response = 'Hello! 👋 How can I assist you with SmartBus Tracker today?';
    }
    else if (lowerMessage.includes('thank')) {
      response = 'You\'re welcome! 😊 Is there anything else I can help you with?';
    }
    else {
      response = 'I\'m not sure I understand. Try asking about:\n• Bus tracking\n• Route finding\n• Login help\n• Schedules\n• Contact information\nHow can I assist you? 🤔';
    }

    this.addMessage('bot', response);
  }

  // Method to update user info when logged in
  updateUserInfo(user) {
    this.currentUser = user;
    if (user) {
      this.addMessage('bot', `Welcome back, ${user.name}! How can I help you today? 👋`);
    }
  }
}

// Initialize chatbot when page loads
let chatbot;
document.addEventListener('DOMContentLoaded', () => {
  chatbot = new SmartBusChatbot();
});

// Export for use in other files
window.SmartBusChatbot = SmartBusChatbot;
