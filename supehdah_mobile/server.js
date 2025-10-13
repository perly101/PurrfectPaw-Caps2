// Simple static file server for PWA development
const express = require('express');
const path = require('path');
const cors = require('cors');
const app = express();
const PORT = process.env.PORT || 3000;

// Enable CORS for API requests
app.use(cors());

// Serve static files from the web directory
app.use(express.static(path.join(__dirname, 'web')));

// Handle all routes for PWA client-side routing
app.get('*', (req, res) => {
  // Exclude API routes from catch-all handler
  if (req.path.startsWith('/api')) {
    return res.status(404).send('API endpoint not found on static server');
  }
  
  // For all other routes, serve the index.html file
  res.sendFile(path.join(__dirname, 'web', 'index.html'));
});

// Start the server
app.listen(PORT, () => {
  console.log(`
  ┌──────────────────────────────────────────────────┐
  │  PurrfectPaw PWA Development Server              │
  │  Running at: http://localhost:${PORT}                │
  │                                                  │
  │  Debug page: http://localhost:${PORT}/debug.html     │
  │                                                  │
  │  Note: This is a static file server only.        │
  │  The Laravel API must be running separately at:  │
  │  http://localhost:8000                           │
  └──────────────────────────────────────────────────┘
  `);
});