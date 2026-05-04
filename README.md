# 💧 Water Store POS

> A Point of Sale system built for water stores — designed to handle cup sizes, water types, and special extras with real-time pricing and clean order management.

**Status:** 🚧 In Development &nbsp;|&nbsp; **Stack:** Laravel · Blade · Tailwind CSS · MySQL

---

## Table of Contents

- [Vision & Idea](#vision--idea)
- [System Overview](#system-overview)
- [System Analysis](#system-analysis)
  - [Problem Statement](#problem-statement)
  - [Actors](#actors)
  - [Functional Requirements](#functional-requirements)
  - [Non-Functional Requirements](#non-functional-requirements)
- [Database Design](#database-design)
  - [Entity Relationship](#entity-relationship)
  - [Tables](#tables)
- [Application Architecture](#application-architecture)
  - [Folder Structure](#folder-structure)
  - [Request Lifecycle](#request-lifecycle)
- [API Reference](#api-reference)
- [Features](#features)
- [Tech Stack](#tech-stack)
- [Installation](#installation)
- [Roadmap](#roadmap)
- [Contributing](#contributing)

---

## Vision & Idea

The idea behind this project came from a simple problem: **water stores that sell customized water cups have no easy way to manage orders**.

Most small water stores still use paper receipts or basic calculators to tally up a customer's order. When a customer asks for a *Large Alkaline water with lemon, mint, and chia seeds* — the cashier has to mentally add up each price, remember the combinations, and write it down manually. This leads to:

- Pricing errors
- Slow service
- No order history or sales data
- No way to track what extras are popular

**The vision for Water Store POS is to give any water store — no matter how small — a professional, fast, and accurate point of sale terminal** that:

1. Lets the cashier build any order in seconds by clicking: cup size → water type → extras
2. Shows the price update live as each selection is made
3. Supports multiple items per order (one receipt, one transaction)
4. Saves every order to a database so the owner can review history and spot trends
5. Stays simple enough that no training is needed — any staff member can use it on day one

This is not just a calculator. It is a full order management system designed specifically around how water stores actually work — with the flexibility to add new cup sizes, water types, and extras directly from the database without touching any code.

---

## System Overview

```
┌─────────────────────────────────────────────────────────────┐
│                      WATER STORE POS                        │
│                                                             │
│   Cashier builds order                                      │
│   ┌──────────┐   ┌──────────┐   ┌──────────┐              │
│   │ Cup Size │ → │  Water   │ → │  Extras  │              │
│   │ (price)  │   │  Type    │   │ (multi)  │              │
│   └──────────┘   │ (price)  │   │ (price)  │              │
│                  └──────────┘   └──────────┘              │
│                        ↓                                    │
│              Live price calculation                         │
│                        ↓                                    │
│              Add item to order receipt                      │
│                        ↓                                    │
│        (Repeat for each cup in the order)                   │
│                        ↓                                    │
│              Confirm → Save to database                     │
└─────────────────────────────────────────────────────────────┘
```

Each **Order** contains one or more **Order Items**. Each Order Item is one cup with its chosen water type and any extras. This means one customer visit = one order number = one total = one receipt, even if they bought five different cups.

---

## System Analysis

### Problem Statement

Traditional water store operations rely on manual price calculation and paper records. This creates:

| Problem | Impact |
|---------|--------|
| Manual price addition | Pricing errors, slow service |
| No order records | Cannot track sales or popular items |
| Paper receipts | Easily lost, no history |
| No item-level tracking | Cannot see which water types or extras sell most |
| One cup per transaction | Cannot handle a customer buying multiple different cups at once |

**Solution:** A web-based POS terminal that automates pricing, records every order in a relational database, and supports multi-item orders in a single transaction.

---

### Actors

| Actor | Role | Access |
|-------|------|--------|
| **Cashier** | Builds orders, confirms transactions | POS Terminal (`/`) |
| **Store Owner / Manager** | Reviews order history, checks totals | Orders list (`/orders`), Order detail (`/orders/{id}`) |
| **System** | Calculates prices, generates order numbers, saves records | Internal |

---

### Functional Requirements

#### FR-01 — Cup Size Selection
- The system must display all active cup sizes with their name, volume, and price.
- The cashier must be able to select exactly one cup size per item.
- Selecting a new cup size deselects the previous one.

#### FR-02 — Water Type Selection
- The system must display all active water types with their name, description, and additional price.
- The cashier must be able to select exactly one water type per item.
- "Still" water adds 0.00 EGP (base price only from cup size).

#### FR-03 — Extras Selection
- The system must display all active extras with their name and additional price.
- The cashier must be able to select **zero or more** extras per item (multi-select).
- Each selected extra adds its price to the item total.

#### FR-04 — Live Price Calculation
- The item total must update instantly whenever the cashier changes cup size, water type, extras, or quantity.
- Formula: `unit_price = cup_price + water_price + sum(extras_prices)`, `line_total = unit_price × quantity`.

#### FR-05 — Multi-Item Order
- The cashier must be able to add multiple items to one order before confirming.
- Each item added to the receipt clears the builder for the next item.
- Any item on the receipt can be removed before confirming.

#### FR-06 — Order Confirmation
- On confirm, the system must save: the order header, all order items, all extras per item with price snapshots.
- The system must auto-generate a unique sequential order number (e.g. `ORD-0001`).
- The order total must be recalculated from all items server-side before saving.

#### FR-07 — Order History
- The system must display all past orders in reverse chronological order with pagination.
- Each order row must show: order number, item count, total, status, and date.

#### FR-08 — Order Detail
- The system must show a full breakdown of any past order: each item, its cup/water/extras, unit price, and line total.
- Price snapshots (`price_at_time`, `cup_price`, `water_price`) must reflect what was charged at the time of the order, not the current price.

#### FR-09 — Order Notes
- The cashier must be able to add optional free-text notes to an order before confirming.

---

### Non-Functional Requirements

| ID | Category | Requirement |
|----|----------|-------------|
| NFR-01 | Performance | Price calculation must respond in under 200ms |
| NFR-02 | Usability | The terminal must be operable with no training — click-based, no typing required to build an order |
| NFR-03 | Data Integrity | All order creation must run inside a database transaction; partial saves must roll back |
| NFR-04 | Price Accuracy | Prices must be stored as `DECIMAL(8,2)`, never as floats |
| NFR-05 | History Accuracy | Extra prices and item prices must be snapshotted at order time so future price changes don't alter past records |
| NFR-06 | Security | All POST routes must be CSRF-protected |
| NFR-07 | Responsiveness | The interface must work on tablet-size screens used at a store counter |

---

## Database Design

### Entity Relationship

```
cup_sizes          water_types         extras
──────────         ───────────         ──────
id                 id                  id
name               name                name
volume             description         price
price              price               sort_order
sort_order         sort_order          is_active
is_active          is_active
    │                   │                  │
    │                   │                  │
    └──────────┬─────────┘                 │
               │                           │
           order_items ───────────────────►order_item_extras
           ─────────────                   ────────────────────
           id                              id
           order_id ◄──┐                   order_item_id
           cup_size_id  │                   extra_id
           water_type_id│                   price_at_time
           quantity     │
           cup_price    │         orders
           water_price  │         ──────
           extras_price │         id
           unit_price   └──────── order_number
           total_price            subtotal
                                  total
                                  status
                                  notes
```

### Tables

#### `cup_sizes`
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `name` | VARCHAR | e.g. Small, Medium, Large |
| `volume` | VARCHAR | e.g. 350ml, 500ml |
| `price` | DECIMAL(8,2) | Base price for this cup |
| `sort_order` | INT | Display order in terminal |
| `is_active` | BOOLEAN | Hide without deleting |

#### `water_types`
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `name` | VARCHAR | e.g. Still, Sparkling, Alkaline |
| `description` | VARCHAR | Short descriptor shown in UI |
| `price` | DECIMAL(8,2) | Added on top of cup price |
| `sort_order` | INT | Display order |
| `is_active` | BOOLEAN | Hide without deleting |

#### `extras`
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `name` | VARCHAR | e.g. Lemon slice, Chia seeds |
| `price` | DECIMAL(8,2) | Added on top of unit price |
| `sort_order` | INT | Display order |
| `is_active` | BOOLEAN | Hide without deleting |

#### `orders`
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `order_number` | VARCHAR UNIQUE | e.g. ORD-0001 |
| `subtotal` | DECIMAL(8,2) | Sum of all line totals |
| `total` | DECIMAL(8,2) | Final total (equals subtotal currently) |
| `status` | ENUM | `pending`, `completed`, `cancelled` |
| `notes` | TEXT | Optional cashier note |

#### `order_items`
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `order_id` | FK → orders | Parent order |
| `cup_size_id` | FK → cup_sizes | Which cup was chosen |
| `water_type_id` | FK → water_types | Which water was chosen |
| `quantity` | INT | How many of this item |
| `cup_price` | DECIMAL(8,2) | **Snapshot** — cup price at order time |
| `water_price` | DECIMAL(8,2) | **Snapshot** — water price at order time |
| `extras_price` | DECIMAL(8,2) | **Snapshot** — total extras price at order time |
| `unit_price` | DECIMAL(8,2) | cup + water + extras |
| `total_price` | DECIMAL(8,2) | unit_price × quantity |

#### `order_item_extras` *(pivot)*
| Column | Type | Description |
|--------|------|-------------|
| `id` | BIGINT PK | Auto increment |
| `order_item_id` | FK → order_items | Which item |
| `extra_id` | FK → extras | Which extra |
| `price_at_time` | DECIMAL(8,2) | **Snapshot** — extra price at order time |

> **Why price snapshots?** If you increase the price of "Chia seeds" next month, all past orders must still show what the customer actually paid. Every price column on `order_items` and `order_item_extras` is a snapshot copied at the moment the order is saved — it never changes.

---

## Application Architecture

### Folder Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── PosController.php        # All POS logic
│   ├── Requests/
│   │   ├── CalculateItemRequest.php  # Validation for price calculation
│   │   └── StoreRequest.php         # Validation for order creation
│   └── Resources/
│       └── CalculateItemResource.php # JSON response formatter
├── Models/
│   ├── CupSize.php                   # active() scope
│   ├── WaterType.php                 # active() scope
│   ├── Extra.php                     # active() scope
│   ├── Order.php                     # generateOrderNumber(), recalculateTotals()
│   └── OrderItem.php                 # belongs to Order, hasMany extras

database/
├── migrations/
│   ├── ..._create_cup_sizes_table.php
│   ├── ..._create_water_types_table.php
│   ├── ..._create_extras_table.php
│   ├── ..._create_orders_table.php
│   ├── ..._create_order_items_table.php
│   └── ..._create_order_item_extras_table.php
└── seeders/
    └── PosSeeder.php                 # Default cup sizes, water types, extras

resources/views/
├── layouts/
│   └── app.blade.php                 # Base layout, nav, toast
└── pos/
    ├── index.blade.php               # POS terminal (main page)
    ├── orders.blade.php              # Order history list
    └── show.blade.php                # Single order detail

routes/
└── web.php                           # All 5 routes
```

### Request Lifecycle

#### Building an order (client-side only, no server call)
```
Cashier clicks cup → JS updates state
Cashier clicks water → JS updates state
Cashier toggles extras → JS updates state
Cashier adjusts quantity → JS recalculates
                ↓
        Live total displayed
                ↓
Cashier clicks "Add to Order" → item pushed to receipt array
                ↓
        Repeat for next item
```

#### Confirming an order (server call)
```
Cashier clicks "Confirm Order"
        ↓
POST /orders  { notes, items: [{cup_size_id, water_type_id, extra_ids, quantity}] }
        ↓
StoreRequest validates all fields
        ↓
DB::transaction begins
        ↓
Order header created (order_number generated)
        ↓
For each item:
  - Fetch cup, water, extras from DB
  - Calculate prices server-side (never trust client prices)
  - Create order_item record
  - Attach extras with price_at_time snapshot
        ↓
Order total recalculated from all items
        ↓
DB::transaction commits
        ↓
JSON response → { success: true, order_number, total, message }
        ↓
Receipt cleared, toast shown
```

---

## API Reference

All routes are web routes with CSRF protection.

| Method | URI | Controller Method | Description |
|--------|-----|-------------------|-------------|
| `GET` | `/` | `index()` | POS terminal page |
| `POST` | `/calculate-item` | `calculateItem()` | Live price calculation (JSON) |
| `POST` | `/orders` | `store()` | Confirm and save an order (JSON) |
| `GET` | `/orders` | `orders()` | Order history (paginated view) |
| `GET` | `/orders/{order}` | `show()` | Single order detail view |

---

### POST `/calculate-item`

Calculates the price of a single item. Does **not** save anything.

**Request body:**
```json
{
  "cup_size_id": 2,
  "water_type_id": 3,
  "extra_ids": [1, 4],
  "quantity": 2
}
```

**Response:**
```json
{
  "cup_name": "Medium (500ml)",
  "water_name": "Alkaline",
  "extras": [
    { "id": 1, "name": "Lemon slice", "price": 2 },
    { "id": 4, "name": "Cucumber",    "price": 3 }
  ],
  "cup_price":    "8.00",
  "water_price":  "5.00",
  "extras_price": "5.00",
  "unit_price":   "18.00",
  "quantity":     2,
  "line_total":   "36.00"
}
```

---

### POST `/orders`

Saves the full order with all items.

**Request body:**
```json
{
  "notes": "Extra cold please",
  "items": [
    {
      "cup_size_id": 2,
      "water_type_id": 3,
      "extra_ids": [1, 4],
      "quantity": 2
    },
    {
      "cup_size_id": 1,
      "water_type_id": 1,
      "extra_ids": [],
      "quantity": 1
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "order": { ... },
  "message": "Order created successfully"
}
```

---

## Features

### ✅ Implemented

- [x] Cup size selection (6 default sizes, fully configurable in DB)
- [x] Water type selection (6 default types)
- [x] Extras multi-select (8 default extras)
- [x] Live price calculation (instant, client-side)
- [x] Multi-item order (add multiple cups to one order)
- [x] Remove items from receipt before confirming
- [x] Quantity control per item
- [x] Order notes
- [x] Order confirmation with full server-side validation
- [x] Price snapshots (past orders never change)
- [x] Auto-generated order numbers (ORD-0001, ORD-0002 ...)
- [x] DB transaction — all-or-nothing order saving
- [x] Order history list with pagination
- [x] Order detail view with full item breakdown
- [x] Form Request validation (`CalculateItemRequest`, `StoreRequest`)
- [x] API Resource response (`CalculateItemResource`)

### 🚧 In Progress / Planned

- [ ] Admin panel to manage cup sizes, water types, and extras (add/edit/deactivate) without touching DB directly
- [ ] Daily sales report — total revenue, most popular water type, most popular extra
- [ ] Receipt printing (thermal printer / PDF export)
- [ ] Order cancellation from the orders list
- [ ] Discount / coupon code support
- [ ] Cashier login system with PIN
- [ ] Multiple store/branch support

---

## Tech Stack

| Layer | Technology |
|-------|-----------|
| Backend framework | Laravel 11 |
| Language | PHP 8.2+ |
| Database | MySQL 8 |
| Frontend templating | Blade |
| CSS | Tailwind CSS (CDN in dev, Vite in production) |
| Fonts | Syne (headings) · DM Mono (body) |
| HTTP client (frontend) | Native `fetch` API |
| DB transactions | `DB::transaction()` |
| Validation | Laravel Form Requests |
| API responses | Laravel API Resources |

---

## Installation

### Requirements

- PHP 8.2+
- Composer
- MySQL 8+
- Node.js 18+ (for Vite in production)

### Steps

```bash
# 1. Clone the repository
git clone https://github.com/your-username/water-store-pos.git
cd water-store-pos

# 2. Install PHP dependencies
composer install

# 3. Copy environment file and generate app key
cp .env.example .env
php artisan key:generate

# 4. Configure your database in .env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=water_pos
DB_USERNAME=root
DB_PASSWORD=secret

# 5. Run migrations
php artisan migrate

# 6. Seed default data (cup sizes, water types, extras)
php artisan db:seed --class=PosSeeder

# 7. Start the development server
php artisan serve
```

Visit **http://127.0.0.1:8000** — the POS terminal loads immediately, no login required.

### Customizing Default Items

Edit `database/seeders/PosSeeder.php` to change names, volumes, or prices, then re-run:

```bash
php artisan db:seed --class=PosSeeder
```

Or update directly via `php artisan tinker`:

```php
App\Models\CupSize::where('name', 'Small')->update(['price' => 7.00]);
```

---

## Roadmap

```
v1.0  ─── Current (In Development)
      ✓   Core POS terminal
      ✓   Multi-item orders
      ✓   Order history & detail
      ✓   Price snapshots

v1.1  ─── Admin Panel
      ○   CRUD for cup sizes, water types, extras
      ○   Activate / deactivate items
      ○   Password-protected admin area

v1.2  ─── Reports
      ○   Daily revenue summary
      ○   Best-selling water types
      ○   Best-selling extras
      ○   Orders per hour heatmap

v1.3  ─── Operations
      ○   Order cancellation
      ○   Thermal receipt printing
      ○   PDF receipt export
      ○   Cashier PIN login

v2.0  ─── Scale
      ○   Multi-branch support
      ○   Discount / promo codes
      ○   Inventory tracking
      ○   Mobile-first redesign
```

---

## Contributing

This project is currently in development. If you have ideas, find a bug, or want to contribute:

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/your-idea`
3. Commit your changes: `git commit -m "Add: your feature"`
4. Push to the branch: `git push origin feature/your-idea`
5. Open a Pull Request

Please keep pull requests focused on one thing at a time.

---

<p align="center">Built with ☕ and 💧</p>