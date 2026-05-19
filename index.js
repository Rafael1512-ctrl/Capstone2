const express = require('express');
const path = require('path');
const app = express();
const port = 3000;

app.set('view engine', 'pug');
app.set('views', path.join(__dirname, 'views'));


app.use('/assets', express.static(path.join(__dirname, 'assets')));

app.get('/', (req, res) => {
  res.render('index', { 
    title: 'Dashboard', 
    activePath: '/' 
  });
});

app.get('/inventory', (req, res) => {
  res.render('inventory', { 
    title: 'Inventory', 
    activePath: '/inventory' 
  });
});

app.listen(port, () => {
  console.log(`Server running di http://localhost:${port}`);
});
