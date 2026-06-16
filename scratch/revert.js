const db = require('../config/db');

async function revert() {
  const conn = await db.getConnection();
  await conn.beginTransaction();
  try {
    await conn.query('DELETE FROM maintenance_log WHERE id IN (2, 3)');
    await conn.query('UPDATE bhp SET stok = stok + 5 WHERE id = 8');
    await conn.query('UPDATE inventaris SET kondisi = "baik" WHERE id = 8');
    await conn.query('UPDATE inventaris SET kondisi = "rusak_ringan" WHERE id = 3');
    await conn.commit();
    console.log('Reverted successfully');
  } catch (e) {
    await conn.rollback();
    console.error(e);
  } finally {
    conn.release();
    process.exit(0);
  }
}

revert();
