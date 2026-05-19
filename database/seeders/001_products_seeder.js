const db = require('../../config/db');

async function run() {
  const products = [
    ['PRD001', 'Gaming Joy Stick',           'Electronics', 'Brand Name', 99.99,  'pcs', 150, 'product-1.png', 'High-performance gaming joystick with ergonomic design.'],
    ['PRD002', 'Wireless Earphones',         'Electronics', 'Tech Pro',   89.99,  'pcs', 320, 'product-2.png', 'Premium wireless earphones with noise cancellation.'],
    ['PRD003', 'Smart Watch Pro',            'Electronics', 'Tech Pro',   98.00,  'pcs', 200, 'product-3.png', 'Smartwatch with health tracking and GPS.'],
    ['PRD004', 'USB-C Fast Charger',         'Electronics', 'Tech Pro',   86.00,  'pcs',  80, 'product-4.png', '65W USB-C fast charger compatible with most devices.'],
    ['PRD005', 'Portable Bluetooth Speaker', 'Electronics', 'Tech Pro',   32.00,  'pcs', 110, 'product-5.png', 'Compact bluetooth speaker with 20-hour battery life.'],
    ['PRD006', 'Magic Keyboard',             'Electronics', 'Tech Pro',   49.00,  'pcs',  10, 'product-6.png', 'Slim wireless mechanical keyboard with RGB backlight.'],
    ['PRD007', 'MacBook Pro 16"',            'Computers',   'Tech Pro',   99.00,  'pcs',  10, 'product-7.png', 'High-performance laptop for professionals.'],
    ['PRD008', 'Wireless Headphones',        'Audio',       'Tech Pro',  109.00,  'pcs', 200, 'product-8.png', 'Over-ear wireless headphones with Hi-Fi sound.'],
    ['PRD009', 'AirPods Pro Max',            'Audio',       'Tech Pro',  549.00,  'pcs',  45, 'product-9.png', 'Premium over-ear headphones with active noise cancellation.'],
    ['PRD010', 'Phone Screen Protector',     'Accessories', 'Tech Pro',    8.99,  'pcs',   3, 'product-10.png','Tempered glass screen protector, pack of 2.']
  ];

  for (const product of products) {
    await db.query(
      `INSERT INTO products (code, name, category, brand, price, unit, quantity, image, description)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
       ON DUPLICATE KEY UPDATE
       name=VALUES(name), category=VALUES(category), brand=VALUES(brand), price=VALUES(price),
       unit=VALUES(unit), quantity=VALUES(quantity), image=VALUES(image), description=VALUES(description)`,
      product
    );
  }
  console.log('🌱  Seeded "products" table.');
}

module.exports = { run };
