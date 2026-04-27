# Analytics Click Tracking Guide

## Setup

### 1. **Enable Click Tracking in Your Layout**

Add this line to your main website layout file (usually in the `<head>` or before `</body>`):

```php
<?php require_once __DIR__ . '/Andison/includes/analytics-tracking.php'; ?>
```

### 2. **Add Data Attributes to Links**

To track clicks on product, brand, or category links, add data attributes to your HTML:

#### **Product Links**
```html
<a href="/product-page.php" data-product="Product Name" data-category="Category Name">
    View Product
</a>
```

#### **Brand Links**
```html
<a href="/brands.php?brand=BrandName" data-brand="Brand Name">
    View Brand
</a>
```

#### **Category Links**
```html
<a href="/category.php?cat=category-name" data-category="Category Name">
    View Category
</a>
```

## How It Works

1. **Click Detection**: When users click links with data attributes, the tracking script captures the click
2. **Deduplication**: Multiple clicks on the same link within 1 second are ignored (prevents double-click spam)
3. **Recording**: Clicks are sent to `/Andison/includes/track-click.php` and stored in Supabase
4. **Analytics Dashboard**: Tracked clicks appear in the admin analytics dashboard

## What Gets Tracked

The analytics dashboard now tracks:
- ✅ Page Views (how many people visit each page)
- ✅ Brand Visits (how many people visit brand pages)
- ✅ Category Visits (how many people visit category pages)
- ✅ **Product Clicks** (NEW - how many people click on products)
- ✅ **Brand Clicks** (NEW - how many people click on brand links)
- ✅ **Category Clicks** (NEW - how many people click on category links)

## Examples

### Product Page with Tracking
```html
<div class="product-card">
    <a href="./product.php?id=123" data-product="Arc Welding Machine XYZ" data-category="Welding">
        <img src="product.jpg" alt="Product">
        <h3>Arc Welding Machine XYZ</h3>
    </a>
</div>
```

### Brand Page Link
```html
<a href="./brands.php?brand=PANASONIC" data-brand="Panasonic Connect">
    <img src="panasonic-logo.png" alt="Panasonic">
</a>
```

### Category Navigation
```html
<nav>
    <a href="./arc-welding-machine/" data-category="Arc Welding Machines">
        Arc Welding Machines
    </a>
    <a href="./batteries/" data-category="Batteries">
        Batteries
    </a>
</nav>
```

## Admin Dashboard

Visit `Admin > Analytics` to see:
- **Site Overview**: Total visitors, today's visitors, weekly/monthly stats
- **Brand Views**: Which brands get the most visits
- **Category Views**: Which categories get the most visits
- **All Clicks**: Product, brand, and category link clicks (NEW)

Data updates every 10 seconds in real-time.
