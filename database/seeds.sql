-- OsunBiz Seed Data (Phase 4)
-- Run: psql -U osunbiz -d osunbiz -f database/seeds.sql

-- Insert categories
INSERT INTO categories (name, slug, description) VALUES
    ('Restaurants', 'restaurants', 'Places to eat and drink'),
    ('Fashion & Clothing', 'fashion-clothing', 'Clothing, shoes, and accessories'),
    ('Technology', 'technology', 'Electronics, computers, and IT services'),
    ('Arts & Crafts', 'arts-crafts', 'Handmade crafts and artwork'),
    ('Books & Stationery', 'books-stationery', 'Books, writing materials, and office supplies'),
    ('Food & Beverages', 'food-beverages', 'Grocery stores, bakeries, and beverage shops'),
    ('Health & Wellness', 'health-wellness', 'Pharmacies, clinics, and wellness centers'),
    ('Professional Services', 'professional-services', 'Legal, accounting, consulting, and other services')
ON CONFLICT (slug) DO NOTHING;

-- Insert Osun State towns/locations
INSERT INTO locations (name, slug, state) VALUES
    ('Osogbo', 'osogbo', 'Osun'),
    ('Ile-Ife', 'ile-ife', 'Osun'),
    ('Ilesa', 'ilesa', 'Osun'),
    ('Ede', 'ede', 'Osun'),
    ('Iwo', 'iwo', 'Osun'),
    ('Ila-Orangun', 'ila-orangun', 'Osun'),
    ('Ikirun', 'ikirun', 'Osun'),
    ('Ipetumodu', 'ipetumodu', 'Osun'),
    ('Ejigbo', 'ejigbo', 'Osun'),
    ('Gbongan', 'gbongan', 'Osun')
ON CONFLICT (slug) DO NOTHING;