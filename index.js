const express = require('express');
const path    = require('path');
const cors    = require('cors');
const webRoutes = require('./routes/web');

const app  = express();
const port = 3000;

// Enable CORS for Laravel frontend on http://localhost:8000
app.use(cors({
  origin: 'http://localhost:8000',
  credentials: true,
  methods: ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
  allowedHeaders: ['Content-Type', 'Authorization']
}));

app.use(express.urlencoded({ extended: true, limit: '5mb' }));
app.use(express.json({ limit: '5mb' }));
app.use('/uploads', express.static(path.join(__dirname, 'public', 'uploads')));

// Load Web Routes under /api prefix
app.use('/api', webRoutes);

// Catch-all 404
app.use((req, res) => {
  res.status(404).json({ error: 'Endpoint not found' });
});

// Start Server
app.listen(port, () => {
  console.log(`API Server running di http://localhost:${port}`);
});
