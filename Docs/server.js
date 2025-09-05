const express = require('express');
const cors = require('cors');
const path = require('path');
const crypto = require('crypto');
const app = express();
const http = require('http').createServer(app);
const { Server } = require('socket.io');
const io = new Server(http, { cors: { origin: '*' } });
const PORT = process.env.PORT || 3000;

// Middleware
app.use(cors()); // Enable CORS for all routes
app.use(express.json()); // allow JSON body
app.use(express.static(path.join(__dirname))); // Serve static files

// In-memory storage for buses and users
const buses = [];
const users = [
  {
    id: 1,
    name: 'Admin User',
    email: 'admin@smartbus.com',
    password: 'admin123', // In production, this should be hashed
    role: 'admin',
    createdAt: new Date()
  }
];

// JWT Secret (in production, use environment variable)
const JWT_SECRET = 'your-secret-key-change-in-production';

// Simple JWT implementation
function generateToken(user) {
  const header = { alg: 'HS256', typ: 'JWT' };
  const payload = {
    userId: user.id,
    email: user.email,
    role: user.role,
    exp: Math.floor(Date.now() / 1000) + (24 * 60 * 60) // 24 hours
  };
  
  const encodedHeader = Buffer.from(JSON.stringify(header)).toString('base64url');
  const encodedPayload = Buffer.from(JSON.stringify(payload)).toString('base64url');
  const signature = crypto.createHmac('sha256', JWT_SECRET)
    .update(`${encodedHeader}.${encodedPayload}`)
    .digest('base64url');
  
  return `${encodedHeader}.${encodedPayload}.${signature}`;
}

function verifyToken(token) {
  try {
    const parts = token.split('.');
    if (parts.length !== 3) return null;
    
    const [header, payload, signature] = parts;
    const expectedSignature = crypto.createHmac('sha256', JWT_SECRET)
      .update(`${header}.${payload}`)
      .digest('base64url');
    
    if (signature !== expectedSignature) return null;
    
    const decodedPayload = JSON.parse(Buffer.from(payload, 'base64url').toString());
    
    // Check if token is expired
    if (decodedPayload.exp < Math.floor(Date.now() / 1000)) return null;
    
    return decodedPayload;
  } catch (error) {
    return null;
  }
}

// Authentication middleware
function authenticateToken(req, res, next) {
  const authHeader = req.headers['authorization'];
  const token = authHeader && authHeader.split(' ')[1]; // Bearer TOKEN
  
  if (!token) {
    return res.status(401).json({ message: 'Access token required' });
  }
  
  const user = verifyToken(token);
  if (!user) {
    return res.status(403).json({ message: 'Invalid or expired token' });
  }
  
  req.user = user;
  next();
}

// Routes
app.get('/', (req, res) => {
  res.sendFile(path.join(__dirname, 'index.html'));
});

app.get('/login', (req, res) => {
  res.sendFile(path.join(__dirname, 'login.html'));
});

// Authentication routes
app.post('/api/auth/register', (req, res) => {
  const { name, email, password } = req.body;
  
  // Validation
  if (!name || !email || !password) {
    return res.status(400).json({ message: 'All fields are required' });
  }
  
  if (password.length < 6) {
    return res.status(400).json({ message: 'Password must be at least 6 characters long' });
  }
  
  // Check if user already exists
  const existingUser = users.find(user => user.email === email);
  if (existingUser) {
    return res.status(400).json({ message: 'User with this email already exists' });
  }
  
  // Create new user
  const newUser = {
    id: users.length + 1,
    name,
    email,
    password, // In production, hash this password
    role: 'user',
    createdAt: new Date()
  };
  
  users.push(newUser);
  
  res.status(201).json({ 
    message: 'User registered successfully',
    user: {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: newUser.role
    }
  });
});

app.post('/api/auth/login', (req, res) => {
  const { email, password } = req.body;
  
  // Validation
  if (!email || !password) {
    return res.status(400).json({ message: 'Email and password are required' });
  }
  
  // Find user
  const user = users.find(u => u.email === email && u.password === password);
  if (!user) {
    return res.status(401).json({ message: 'Invalid email or password' });
  }
  
  // Generate token
  const token = generateToken(user);
  
  res.json({
    message: 'Login successful',
    token,
    user: {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role
    }
  });
});

app.post('/api/auth/logout', authenticateToken, (req, res) => {
  // In a real application, you might want to blacklist the token
  res.json({ message: 'Logged out successfully' });
});

// Protected route to get user profile
app.get('/api/auth/profile', authenticateToken, (req, res) => {
  const user = users.find(u => u.id === req.user.userId);
  if (!user) {
    return res.status(404).json({ message: 'User not found' });
  }
  
  res.json({
    user: {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role
    }
  });
});


// Public route to fetch live bus locations (no auth, limited fields)
app.get('/api/public/buses', (req, res) => {
  res.json(buses.map(({ id, lat, lng, speed, occupancy, routeId }) => ({ id, lat, lng, speed, occupancy, routeId })));
});
// Bus routes (protected)
app.get('/api/buses', authenticateToken, (req, res) => {
  res.json(buses);
});

app.post('/api/update-location', authenticateToken, (req, res) => {
  const { id, lat, lng, speed, occupancy } = req.body;

  // find bus in array and update
  const bus = buses.find(b => b.id === id);
  if (bus) {
    bus.lat = lat;
    bus.lng = lng;
    bus.speed = speed || bus.speed;
    bus.occupancy = occupancy || bus.occupancy;
    io.emit('busLocation', { id, lat, lng, speed: bus.speed, occupancy: bus.occupancy });
    res.json({ status: "updated", bus });
  } else {
    // new bus if not found
    buses.push({ id, lat, lng, speed, occupancy, routeId: "PB-12" });
    io.emit('busLocation', { id, lat, lng, speed, occupancy });
    res.json({ status: "added", bus: { id, lat, lng } });
  }
});

// Public route for checking server status
app.get('/api/status', (req, res) => {
  res.json({ 
    status: 'running', 
    timestamp: new Date().toISOString(),
    busesCount: buses.length,
    usersCount: users.length
  });
});

// Start server
http.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
  console.log(`Open http://localhost:${PORT} in your browser`);
  console.log(`Login page: http://localhost:${PORT}/login`);
  console.log('Demo credentials: admin@smartbus.com / admin123');
});


// Serve driver page for GPS reporting
app.get('/driver', (req, res) => {
  res.sendFile(path.join(__dirname, 'driver.html'));
});

// Serve live map page
app.get('/live', (req, res) => {
  res.sendFile(path.join(__dirname, 'live.html'));
});
