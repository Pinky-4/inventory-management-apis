# Inventory Management APIs

This is a Laravel-based REST API for managing inventory (products, categories, warehouses, and stock movements).

The main idea behind this project is to handle stock correctly (especially during concurrent updates) and keep APIs fast using caching.

## Table of Contents

- [Setup Instructions](#setup-instructions)
- [API Reference](#api-reference)
- [Caching Strategy](#caching-strategy)
- [Indexing Decisions](#indexing-decisions)
- [Assumptions](#assumptions)
- [Notes](#notes)

## Setup Instructions

### Requirements
- PHP 8.1+
- Laravel 10+
- MySQL 8+
- Redis (for caching)

### Folder Structure

```
Root
|- app/
|- bootstrap/
|- config/
|- database/
|- public/
|- resources/
|- routes/
|- storage/
|- vendor/
|- .env
|- artisan
|- composer.json
|- package.json
```

### Clone Project

To start using Inventory Management Apis, clone the repository:

```bash
git clone git@bitbucket.org:inventory-management-apis.git
```

### Install Dependencies 

```bash
composer install
```

### Configuration

Laravel .env Setup

Copy `.env.example` and create your `.env` file:

```bash
cp .env.example .env
```

Update the configurations according to your environment in the `.env` file.

### Database Setup

Run migrations and seed the database:

```bash
php artisan migrate --seed
```
### Seeder includes:

- Nested categories
- Around 50 products
- Multiple warehouses
- Stock and movement records (including edge cases)

Generate an application key:

```bash
php artisan key:generate
```

Create a symbolic link for storage:

```bash
php artisan storage:link
```    

### Running the Project

In Terminal run below command for running the project for Development Only:

Run the project locally for development:

**Run the Laravel ::** 

```bash
php artisan serve
```

## API Reference

### 1. Category Tree Endpoint 

GET /api/categories/tree

- Returns nested category structure
- Cached for 1 hour
- Skips inactive categories

Response : 

```bash
{
    "message": "Category tree fetched successfully",
    "data": {
        "category_list": [
            {
                "id": 1,
                "name": "Electronics",
                "children": [
                    {
                        "id": 4,
                        "name": "Mobiles",
                        "children": [
                            {
                                "id": 7,
                                "name": "Smart Phones",
                                "children": []
                            }
                        ]
                    },
                    {
                        "id": 5,
                        "name": "Laptops",
                        "children": []
                    }
                ]
            },
            {
                "id": 2,
                "name": "Fashion",
                "children": [
                    {
                        "id": 6,
                        "name": "Men Clothing",
                        "children": []
                    }
                ]
            },
            {
                "id": 3,
                "name": "Empty Category",
                "children": []
            }
        ]
    },
    "code": 200
}
```

### 2. Product Listing with Filters

GET /api/products

Query Params : 

```bash
page:1
category_id:
warehouse_id:
available: // 0=> Zero stock meand no stock available, 1=> Stock Available
min_price:
max_price:
sort_by: // name | price | stock
sort_order: // sort field which are defined in sort_by and that sorting order is defined in this asc|desc.
search: // Search with name and sku
```

Response : 

```bash
{
    "message": "Product list fetched successfully",
    "data": {
        "product_list": [
            {
                "id": 50,
                "name": "Product 50",
                "sku": "SKU-53VGOCZV",
                "price": 6274,
                "category": "Mobiles",
                "available_stock": 38
            },
            {
                "id": 49,
                "name": "Product 49",
                "sku": "SKU-IPZOO5EW",
                "price": 2985,
                "category": "Mobiles",
                "available_stock": 6
            },
        ],
        "pagination": {
            "total": 50,
            "count": 10,
            "per_page": 10,
            "current_page": 1,
            "last_page": 5,
            "hasMorePages": true
        }
    },
    "code": 200
}
```

### 3. Stock Adjustment Endpoint 

POST /api/stock/adjust

Form Data : 
For Example : 
 ```bash
product_id:3
warehouse_id:3
movement_type:1
quantity:2
reference_id:1
reference_type:product
note:Hello i am
```

Rules:

- Cannot go below available stock
- Uses DB transaction + row locking
- Every change logged in stock_movements

Response : 
```bash
{
    "message": "Stock updated successfully",
    "data": {
        "available_quantity": 61
    },
    "code": 200
}
```

### 4. Inventory Summary Report

GET /api/inventory/summary

Response : 
```bash
{
    "message": "Inventory summary fetched successfully",
    "data": [
        {
            "product_id": 1,
            "product_name": "Product 1",
            "total_quantity": 40,
            "total_reserved": 16,
            "available_quantity": 24,
            "top_warehouse_id": "1",
            "top_warehouse_name": "Warehouse A"
        },
        {
            "product_id": 2,
            "product_name": "Product 2",
            "total_quantity": 59,
            "total_reserved": 2,
            "available_quantity": 57,
            "top_warehouse_id": "1",
            "top_warehouse_name": "Warehouse A"
        },
        {
            "product_id": 3,
            "product_name": "Product 3",
            "total_quantity": 429,
            "total_reserved": 108,
            "available_quantity": 321,
            "top_warehouse_id": "3",
            "top_warehouse_name": "Warehouse C"
        }, 
    ],
    "code": 200
}
```

### 5. Movement History with Aggregations

GET /api/products/{id}/movements

Response : 
```bash
{
    "message": "Product movement history fetched successfully",
    "data": {
        "summary": {
            "total_in": 6,
            "total_out": 13,
            "net_movement": -7
        },
        "movements": {
            "list": [
                {
                    "id": 209,
                    "product_id": 3,
                    "warehouse_id": 3,
                    "movement_type": 1,
                    "movement_type_name": "Stock In",
                    "quantity": 2,
                    "reference_id": 1,
                    "reference_type": "product",
                    "note": "Hello i am",
                    "moved_at": "2026-04-14T08:51:31.000000Z",
                    "moved_at_formatted": "2026-04-14 08:51:31"
                },
                {
                    "id": 208,
                    "product_id": 3,
                    "warehouse_id": 3,
                    "movement_type": 1,
                    "movement_type_name": "Stock In",
                    "quantity": 2,
                    "reference_id": 1,
                    "reference_type": "product",
                    "note": "Hello i am",
                    "moved_at": "2026-04-14T08:45:42.000000Z",
                    "moved_at_formatted": "2026-04-14 08:45:42"
                },
            ],
            "pagination": {
                "total": 5,
                "count": 5,
                "per_page": 20,
                "current_page": 1,
                "last_page": 1,
                "hasMorePages": false
            }
        }
    },
    "code": 200
}
```

### 6. Low Stock Alert Endpoint 

GET /api/inventory/low-stock

Query Params : 
```bash
threshold=10
```

Response : 
```bash
{
    "message": "Low stock items fetched successfully",
    "data": {
        "list": [
            {
                "product_id": 16,
                "product_name": "Product 16",
                "sku": "SKU-SOMWH3V4",
                "warehouse_id": 2,
                "warehouse_name": "Warehouse B",
                "quantity": 0,
                "reserved_quantity": 0,
                "available_quantity": 0
            },
            {
                "product_id": 34,
                "product_name": "Product 34",
                "sku": "SKU-WW5NAGHI",
                "warehouse_id": 2,
                "warehouse_name": "Warehouse B",
                "quantity": 14,
                "reserved_quantity": 14,
                "available_quantity": 0
            },
            {
                "product_id": 19,
                "product_name": "Product 19",
                "sku": "SKU-HPXFVIQE",
                "warehouse_id": 3,
                "warehouse_name": "Warehouse C",
                "quantity": 10,
                "reserved_quantity": 10,
                "available_quantity": 0
            },
        ]
    },
    "code": 200
}
```

## Caching Strategy

Cache Driver
Redis is used for caching.

### Cache Key Naming Convention : 
Consistent format:
inventory:<resource>:<optional-params>

Examples:
- inventory:categories:tree
- inventory:summary
- inventory:low_stock:10

### TTL (Time To Live)
- Category Tree → 10 minutes
- Inventory Summary → 10 minutes
- Low Stock → 10 minutes
- Product Search Filter -> 5 minutes

### Cache Invalidation
Endpoint -> Invalidation Trigger
- Category Tree -> On category create/update/delete
- Inventory Summary -> On stock update
- Low Stock -> On stock update

### Notes
- Cache is not used for empty responses
- Cache is cleared immediately after write operations
- Filters (like threshold) are included in cache keys

## Indexing Decisions

Indexes are added only where queries require faster filtering or joins.

- Categories Table : 
    - parent_id : Used for building category tree
    - is_active : Used to filter active categories

- Products Table :
    - category_id	: Filtering by category
    - base_price : Price range filtering
    - FULLTEXT(name, sku) : Optimized search

- Stock Table :
    - UNIQUE(product_id, warehouse_id) : Prevent duplicate stock records
    - (product_id, warehouse_id) : Fast lookup during stock updates

- Stock Movements Table :
    - (product_id, warehouse_id) : Filtering movement history
    - movement_type : Used in aggregation
    - moved_at : Date range filtering
    - (product_id, moved_at) : Optimized reporting queries

### Assumptions
Movement types are stored as integers:
```
1 = Stock In
2 = Stock Out
3 = Reservation
4 = Reservation Release
```

#### Available stock is calculated as:

- quantity - reserved_quantity
- Stock cannot go below zero
- Reserved quantity cannot exceed total quantity
- Inactive categories are excluded from tree, but active children are still included
- Products with zero stock are still included in inventory summary
- Low stock threshold defaults to 10 if not provided

### Notes
- Business logic is handled in Service classes
- Controllers are kept minimal
- API Resources are used for all responses
- Database aggregation is used for reports (no PHP loops)
- Transactions are used for stock updates to maintain consistency

