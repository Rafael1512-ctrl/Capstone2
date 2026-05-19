const express = require('express');
const path    = require('path');
const session = require('express-session');
const webRoutes = require('./routes/web');

const app  = express();
const port = 3000;

app.set('view engine', 'pug');
app.set('views', path.join(__dirname, 'views'));

app.use(express.urlencoded({ extended: true }));
app.use(express.json());
app.use('/assets', express.static(path.join(__dirname, 'assets')));

// Inisialisasi Session Support
app.use(session({
  secret: 'super-secret-key-capstone-inventory-lab',
  resave: false,
  saveUninitialized: false,
  cookie: { maxAge: 24 * 60 * 60 * 1000 } // 1 hari
}));

// Share session user ke semua Pug views (res.locals)
app.use((req, res, next) => {
  res.locals.user = req.session.user || null;
  next();
});

// Load Web Routes (seperti routes/web.php di Laravel)
app.use('/', webRoutes);

// Catch-all 404
app.use((req, res) => {
  res.status(404).render('404-error', { title: '404 Error' });
});

// Start Server
app.listen(port, () => {
  console.log(`Server running di http://localhost:${port}`);
});
