# Real-Time Public Transport Tracking for Small Cities

A comprehensive web-based solution for real-time bus tracking designed specifically for small cities and tier-2 towns with limited digital infrastructure.

## 🚌 Features

- **Real-Time Tracking**: Live GPS-based bus location tracking
- **Interactive Map**: Google Maps integration with custom bus markers
- **Low Bandwidth Optimized**: Designed for areas with limited internet connectivity
- **Responsive Design**: Works on mobile devices and desktop
- **Route Information**: Popular routes with frequency and status
- **Live Updates**: Auto-refreshes every 5 seconds

## 🛠️ Technology Stack

- **Backend**: Node.js with Express.js
- **Frontend**: HTML5, CSS3 (Tailwind CSS), JavaScript
- **Maps**: Google Maps API
- **Real-time Updates**: RESTful API with polling

## 📋 Prerequisites

- Node.js (v14 or higher)
- npm or yarn
- Google Maps API key (optional for demo)

## 🚀 Installation & Setup

1. **Clone or download the project**
   ```bash
   cd Bus-tracking
   ```

2. **Install dependencies**
   ```bash
   npm install
   ```

3. **Start the server**
   ```bash
   node Docs/server.js
   ```

4. **Open in browser**
   ```
   http://localhost:3000
   ```

## 🗺️ Google Maps Setup (Optional)

To enable full map functionality:

1. Get a Google Maps API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Replace `YOUR_GOOGLE_MAPS_API_KEY` in `Docs/index.html` line 7
3. Enable Maps JavaScript API in your Google Cloud project

## 📱 How to Use

### For Commuters:
1. **Track Buses**: View all active buses on the interactive map
2. **Find Routes**: Enter your starting point and destination
3. **Check Status**: See bus occupancy, speed, and estimated arrival times
4. **Real-time Updates**: Information refreshes automatically every 5 seconds

### For Transport Authorities:
1. **Add Demo Buses**: Click "Add Demo Bus" to test the system
2. **Monitor Fleet**: View all active buses and their status
3. **API Integration**: Use the REST API to integrate with existing systems

## 🔧 API Endpoints

### GET `/api/buses`
Returns all active buses
```json
[
  {
    "id": "BUS-001",
    "lat": 12.9716,
    "lng": 77.5946,
    "speed": 25,
    "occupancy": 60,
    "routeId": "PB-12"
  }
]
```

### POST `/api/update-location`
Updates or adds a bus location
```json
{
  "id": "BUS-001",
  "lat": 12.9716,
  "lng": 77.5946,
  "speed": 25,
  "occupancy": 60
}
```

## 🎯 Problem Statement Addressed

This solution addresses the critical issue of lack of real-time tracking in small cities:

- **60% of commuters** face delays due to unpredictable schedules
- **Reduced public transport usage** due to lack of information
- **Increased private vehicle dependency** worsening traffic and pollution
- **Limited digital infrastructure** in tier-2 cities

## 💡 Key Benefits

- **Enhanced Commuter Experience**: Real-time information reduces waiting time
- **Sustainable Transport**: Encourages public transport usage
- **Low-Cost Solution**: Optimized for small city budgets
- **Scalable**: Can be expanded to include more features

## 🔮 Future Enhancements

- **Mobile App**: Native Android/iOS applications
- **SMS Integration**: For areas with limited internet
- **Route Optimization**: AI-powered route suggestions
- **Payment Integration**: Digital ticketing system
- **Analytics Dashboard**: For transport authorities

## 🤝 Contributing

This project is designed to be easily customizable for different cities. Key areas for customization:

- **Map Coordinates**: Update default location in `initMap()` function
- **Route Information**: Modify route data in the HTML
- **API Integration**: Connect with existing transport systems
- **UI/UX**: Customize colors and branding

## 📞 Support

For technical support or customization requests, please refer to the documentation or contact the development team.

---

**Built for Small Cities, Designed for Big Impact** 🚌✨
