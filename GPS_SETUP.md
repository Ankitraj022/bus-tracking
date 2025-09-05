
# Real GPS Tracking Setup (Option A: Smartphone)

This repo has been wired for **real, accurate GPS** using a driver's smartphone (no simulation).

## What you got in this commit
- `Docs/driver.html` — open on the driver's phone to stream GPS in real time (uses high-accuracy browser Geolocation).
- `Docs/live.html` — open on any device to see buses move live on a Leaflet map.
- `Docs/server.js` — Express + Socket.IO backend. Endpoints added:
  - `POST /api/update-location` (protected by JWT)
  - `GET /api/public/buses` (public, read-only)
  - pages: `/driver`, `/live`

## 1) Install & Run
```bash
cd Bus-tracking
npm install
npm run start   # server listens on http://localhost:3000
```

> If running on your LAN so a phone can reach it, replace `localhost` with your **computer's LAN IP**, e.g. `http://192.168.1.5:3000`

## 2) Login and get a token
- Open `http://<server>:3000/login`
- Use demo admin credentials (shown in console on startup) or register a new user.
- After login, the server issues a **JWT token**.
  - If the UI does not show it, you can call the API directly:
    ```bash
    curl -X POST http://<server>:3000/api/auth/login -H "Content-Type: application/json" -d '{"email":"admin@smartbus.com","password":"admin123"}'
    ```
    The JSON response includes `token`.

## 3) Start the **Driver app** (no install required)
On the driver's Android phone:
- Open: `http://<server>:3000/driver?busId=BUS-101&token=PASTE_JWT_HERE`
- Grant **Location permission**.
- Keep the page open while driving. The page:
  - Requests **high accuracy** GPS and keeps the screen awake (when supported)
  - Sends continuous updates to `POST /api/update-location` with `{ id, lat, lng, speed }`

> You can also paste the token and busId in the inputs instead of using URL params.

## 4) Watch the **Live Map**
- Open: `http://<server>:3000/live`
- You will see markers appear and move in real-time via **Socket.IO** events.
- Initial markers are loaded from `GET /api/public/buses` (no auth).

## 5) Production tips
- Use HTTPS and a trusted domain so mobile browsers give best GPS accuracy.
- Put the server on a public host (Render/Railway/VPS) or expose via ngrok for quick tests.
- Replace the in-memory store with a database (MongoDB/Redis) if you want history/ETA analytics.
- Lock down `POST /api/update-location` by issuing **driver-role tokens** only to driver accounts.

## API Reference
### `POST /api/update-location` (auth required)
**Headers:** `Authorization: Bearer <JWT>`  
**Body:**
```json
{
  "id": "BUS-101",
  "lat": 28.6129,
  "lng": 77.2295,
  "speed": 10.5,
  "occupancy": 20
}
```

### `GET /api/public/buses`
Returns array of buses with `{ id, lat, lng, speed, occupancy, routeId }`.

---

**That’s it.** No fake data — the driver's phone streams its real GPS to your backend, and your map updates live.
