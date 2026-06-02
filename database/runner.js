const db = require("../config/db");
const fs = require("fs");
const path = require("path");

// Ambil argument dari command line
const action = process.argv[2];

// Daftar file migrations secara berurutan
const migrations = [
  {
    name: "001_create_products_table",
    file: "./migrations/001_create_products_table.js",
  },
  {
    name: "002_create_sales_table",
    file: "./migrations/002_create_sales_table.js",
  },
  {
    name: "003_create_roles_table",
    file: "./migrations/003_create_roles_table.js",
  },
  {
    name: "004_create_users_table",
    file: "./migrations/004_create_users_table.js",
  },
  {
    name: "005_create_user_roles_table",
    file: "./migrations/005_create_user_roles_table.js",
  },
  {
    name: "006_create_ruangan_table",
    file: "./migrations/006_create_ruangan_table.js",
  },
  {
    name: "007_create_bhp_table",
    file: "./migrations/007_create_bhp_table.js",
  },
  {
    name: "008_create_draft_pengadaan_table",
    file: "./migrations/008_create_draft_pengadaan_table.js",
  },
  {
    name: "009_create_detail_draft_table",
    file: "./migrations/009_create_detail_draft_table.js",
  },
  {
    name: "010_create_inventaris_table",
    file: "./migrations/010_create_inventaris_table.js",
  },
  {
    name: "011_create_maintenance_log_table",
    file: "./migrations/011_create_maintenance_log_table.js",
  },
  {
    name: "012_add_catatan_to_detail_draft_table",
    file: "./migrations/012_add_catatan_to_detail_draft_table.js",
  },
  {
    name: "013_add_inventaris_digantikan_to_detail_draft_table",
    file: "./migrations/013_add_inventaris_digantikan_to_detail_draft_table.js",
  },
];

const seeders = [
  { name: "001_products_seeder", file: "./seeders/001_products_seeder.js" },
  { name: "002_sales_seeder", file: "./seeders/002_sales_seeder.js" },
  { name: "003_roles_seeder", file: "./seeders/003_roles_seeder.js" },
  { name: "004_users_seeder", file: "./seeders/004_users_seeder.js" },
  { name: "005_user_roles_seeder", file: "./seeders/005_user_roles_seeder.js" },
  { name: "006_ruangan_seeder", file: "./seeders/006_ruangan_seeder.js" },
  { name: "007_bhp_seeder", file: "./seeders/007_bhp_seeder.js" },
  {
    name: "008_draft_pengadaan_seeder",
    file: "./seeders/008_draft_pengadaan_seeder.js",
  },
  {
    name: "009_detail_draft_seeder",
    file: "./seeders/009_detail_draft_seeder.js",
  },
  { name: "010_inventaris_seeder", file: "./seeders/010_inventaris_seeder.js" },
  {
    name: "011_maintenance_log_seeder",
    file: "./seeders/011_maintenance_log_seeder.js",
  },
];

async function runMigrate() {
  console.log(" Running migrations...");
  for (const migration of migrations) {
    console.log(`Migrating: ${migration.name}`);
    const module = require(migration.file);
    await module.up();
  }
  console.log(" All migrations completed successfully.");
}

async function rollbackMigrate() {
  console.log(" Rolling back migrations...");
  // Rollback dari belakang ke depan (karena foreign key constraint)
  for (let i = migrations.length - 1; i >= 0; i--) {
    const migration = migrations[i];
    console.log(`Rolling back: ${migration.name}`);
    const module = require(migration.file);
    await module.down();
  }
  console.log("✨ Rollback completed.");
}

async function runSeed() {
  console.log(" Seeding database...");
  for (const seeder of seeders) {
    console.log(`Seeding: ${seeder.name}`);
    const module = require(seeder.file);
    await module.run();
  }
  console.log(" Seeding completed successfully.");
}

async function main() {
  try {
    // Pastikan database dblab ada dan aktif
    await db.query("CREATE DATABASE IF NOT EXISTS dblab");
    await db.query("USE dblab");

    switch (action) {
      case "migrate":
        await runMigrate();
        break;
      case "migrate:rollback":
        await rollbackMigrate();
        break;
      case "db:seed":
        await runSeed();
        break;
      case "migrate:fresh":
        await rollbackMigrate();
        await runMigrate();
        await runSeed();
        break;
      default:
        console.log(`
Usage Node CLI Database Runner:
  node database/runner.js migrate          - Jalankan semua migrasi tabel
  node database/runner.js migrate:rollback - Rollback semua tabel (drop)
  node database/runner.js db:seed          - Jalankan seeder data
  node database/runner.js migrate:fresh    - Rollback, migrate, dan seed ulang (Sangat disarankan untuk fresh setup)
        `);
    }
  } catch (error) {
    console.error(" Error executing database action:", error);
  } finally {
    // Tutup koneksi database
    await db.end();
    process.exit(0);
  }
}

main();
