# Landing Page KeuanganKu - Premium Dark Mode Fintech Design

## Overview

Landing page modern dengan design fintech premium, dark mode, dan glassmorphism effects untuk aplikasi **KeuanganKu** - Catat Pendapatan & Manajemen Wallet.

## Design Specifications

### Color Palette

| Nama | Hex | Usage |
|------|-----|-------|
| Primary Green | `#10B981` | Buttons, accents, primary elements |
| Light Green | `#22C55E` | Secondary accents, hover states |
| Background | `#050505` | Main background (almost black) |
| Glass BG | `rgba(255, 255, 255, 0.05)` | Glassmorphism background |
| Text Primary | `#FFFFFF` | Main text |
| Text Secondary | `#9CA3AF` | Subtext, descriptions |

### Typography

- **Font Family**: Inter (Google Fonts)
- **Headings**: 400-800 weight, sizes from 1.5rem to 3.5rem
- **Body**: 300-500 weight, 0.875rem to 1.25rem

### Design Elements

#### 1. Glassmorphism Cards
- Background: `rgba(255, 255, 255, 0.05)`
- Backdrop blur: 10px
- Border: `1px solid rgba(255, 255, 255, 0.1)`
- Hover effect: Slightly higher opacity + green border tint
- Smooth transitions: 300ms

#### 2. Gradient Effects
- Hero text: Linear gradient from `#10B981` to `#22C55E`
- Section backgrounds: Subtle green gradient (0.05-0.1 opacity)

#### 3. Glow Effects
- Primary glow: `0 0 30px rgba(16, 185, 129, 0.4)`
- Hover glow: `0 0 40px rgba(16, 185, 129, 0.6)`

#### 4. Animations
- Fade in: 0.8s ease-in from bottom
- Float animation: 3s infinite with 15px vertical movement
- Transitions: 300ms smooth transitions on hover

## Page Sections

### 1. Navbar (Fixed)
- **Position**: Fixed top, z-50
- **Style**: Glassmorphic with blur effect
- **Content**:
  - Logo + App name (left)
  - Navigation links (desktop only)
  - Auth buttons (Login/Register)
- **Responsive**: Hamburger menu hidden on mobile

### 2. Hero Section
- **Layout**: 2-column grid (text + illustration)
- **Left side**:
  - Large gradient heading
  - Descriptive subtext
  - CTA buttons (primary + secondary)
- **Right side** (desktop only):
  - Glassmorphic card with dashboard mockup
  - Floating animation
  - Sample wallet data

### 3. Features Section
- **Layout**: 3-column grid (6 features)
- **Features**:
  1. Smart Wallet Management
  2. Automatic Money Allocation
  3. Expense Tracking
  4. Telegram Notification
  5. Secure Account
  6. Financial Report
- **Cards**: Glassmorphic with hover lift effect + icon + title + description

### 4. How It Works Section
- **Layout**: Linear timeline (5 steps)
- **Step format**: Number badge + title + description
- **Styling**: Simple, clean, easy to follow
- **Numbers**: 1-5 in circular gradient badges

### 5. Dashboard Preview Section
- **Content**: Large glassmorphic card showing app dashboard
- **Includes**:
  - Header with total balance
  - 3 stat cards (income, expenses, transactions)
  - 4 wallet cards with progress bars
  - Sample transactions list
- **Styling**: Dark theme with color-coded elements (green, blue, yellow, purple)

### 6. CTA Section (Call To Action)
- **Layout**: Centered, full-width gradient background
- **Content**: 
  - Large heading
  - Subtext
  - Primary CTA button
- **Styling**: Prominent glow effect

### 7. Footer
- **Layout**: 4-column grid + copyright bar
- **Content**:
  - Logo + company description
  - Product links
  - Account links
  - Legal links
- **Style**: Minimalist dark theme

## Responsive Design

### Breakpoints (Tailwind)
- **Mobile**: < 768px (md)
  - Single column layouts
  - Simplified navigation
  - Adjusted font sizes
  - Hidden desktop elements

- **Tablet**: 768px - 1024px
  - 2-column where applicable
  - Adjusted spacing

- **Desktop**: > 1024px
  - Full multi-column layouts
  - Large typography
  - Enhanced effects

## Features

### Interactive Elements

1. **Smooth Scroll Navigation**
   - Anchor links scroll smoothly to sections
   - JavaScript scroll behavior

2. **Fade-in Animation on Scroll**
   - Intersection Observer API
   - Elements fade in as they enter viewport

3. **Hover Effects**
   - Card lift (transform translateY)
   - Color transitions
   - Opacity changes

4. **Float Animation**
   - Continuous floating effect on hero illustration
   - 3-second infinite loop

### Performance Optimizations

- Minimal JavaScript (only for scroll interaction)
- CSS animations (hardware accelerated)
- Optimized Tailwind classes
- No external JS libraries required

## File Location

```
resources/views/landing.blade.php
```

## Route

```php
Route::get('/', function () {
    return view('landing');
})->name('home');
```

Access at: `http://localhost:8000/` (when Laravel server running)

## Browser Support

- Chrome/Edge: Full support
- Firefox: Full support
- Safari: Full support (with standard properties)
- Mobile browsers: Full support

## Customization Guide

### Change Primary Color

Find this in the `<style>` tag and replace `#10B981`:

```css
.gradient-primary {
    background: linear-gradient(135deg, #10B981 0%, #059669 100%);
}
```

### Add/Remove Features

Edit the Features Section to add/remove feature cards:

```html
<div class="glass glass-hover rounded-2xl p-8">
    <!-- New feature -->
</div>
```

### Update Dashboard Preview

Modify the dashboard preview section HTML to match current app state.

### Adjust Animations

Modify `@keyframes` in the `<style>` tag:

```css
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); } /* Adjust this value */
}
```

## SEO Considerations

- Meta title: "KeuanganKu - Kelola Keuanganmu Lebih Cerdas"
- Meta description: (Add in head)
- Semantic HTML structure
- Heading hierarchy (H1 > H2 > H3)

## Deployment

### For Production

1. Optimize images in dashboard preview
2. Add CDN for Google Fonts
3. Minify CSS/JS if needed
4. Add meta tags (OG, Twitter cards)
5. Consider adding schema.org structured data

### Environment Setup

**Requirements:**
- PHP 8.0+
- Laravel 11+
- Node.js (for Vite dev server)

**Setup:**
```bash
cd /vercel/share/v0-project
npm install
php artisan serve
npm run dev
```

Then visit: `http://localhost:8000`

## Future Enhancements

- [ ] Add light mode toggle
- [ ] Implement animations with Framer Motion
- [ ] Add video demo section
- [ ] Testimonials section
- [ ] Blog/News section
- [ ] Pricing information
- [ ] Language switcher (i18n)

## Browser DevTools Tips

### Inspect Glassmorphism
```css
/* View in DevTools */
backdrop-filter: blur(10px);
background: rgba(255, 255, 255, 0.05);
```

### Test Animations
- Use Chrome DevTools > Performance panel
- Check animation smoothness at 60fps

### Mobile Testing
- Use DevTools Device Emulation
- Test at 375px, 768px, 1024px viewports

---

**Created**: 2025-01-15
**Last Updated**: 2025-01-15
**Status**: ✅ Production Ready
