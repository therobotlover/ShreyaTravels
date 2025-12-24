# 📘 Shreya Travels — Initial Business & Product Documentation

## 1️⃣ Business Overview

### Business Name
**Shreya Travels**

### Business Type
Local travel & tour agency (group-based, experience-focused)

### Positioning
**Affordable Premium Travel**

- More premium than budget/cheap tour operators  
- Still affordable vs luxury tour companies  
- Focus on:
  - Safety
  - Experience
  - Hassle-free booking
  - Clean UI & professional operations

### Tagline
**“Luxury. Safety. Experiences.”**

---

## 2️⃣ Problem Statement

Local travel agencies often suffer from:
- Manual WhatsApp/Facebook bookings
- No transparency in pricing
- No booking confirmation system
- Unsafe or poorly managed tours
- Zero tech trust

Customers want:
- Simple booking
- Trusted payment
- Clear confirmation
- Premium service at a reasonable price

---

## 3️⃣ Solution (Your Product)

### What Shreya Travels Solves
- One-click booking via email + OTP (no passwords)
- Secure bKash payment
- Clear booking dashboard
- Admin visibility on bookings
- Minimal but professional web presence

### Key Differentiators

| Feature | Cheap Agencies | Shreya Travels |
|------|---------------|----------------|
| Booking system | WhatsApp | Web App |
| Payment | Cash / Manual | bKash |
| Confirmation | Verbal | Dashboard |
| Trust | Low | OTP + Payment |
| Brand feel | Cheap | Premium |

---

## 4️⃣ Target Market

### Primary Customers
- Young professionals (22–40)
- Couples
- Small groups
- First-time travelers
- Dhaka-based urban users

### Travel Types
- Day tours
- 2–4 day group tours
- Local destinations only (initially)

---

## 5️⃣ Product Scope (MVP)

### Must-Have
- Tour catalog (single page)
- Email + OTP login
- Booking modal
- bKash payment
- User dashboard
- Admin booking view
- Rate limiting & security

### Explicitly Excluded (for now)
- User profiles
- Multiple roles
- Traveler details
- Reviews
- Refund automation
- Mobile app

---

## 6️⃣ Functional Requirements

### User Side
- View tours
- Book a tour
- Receive OTP via email
- Pay via bKash
- View booking status
- Re-pay pending bookings
- Logout

### Admin Side
- Login via same OTP
- View all bookings
- Filter bookings
- See payment details
- Cancel bookings (manual)

---

## 7️⃣ Non-Functional Requirements
- Minimal database (4 tables)
- OTP stored in cache only
- Session expiry: 1 hour
- Rate-limited OTP, booking, payment
- Env-controlled security matrix
- Works on Railway free tier
- Stateless & simple

---

## 8️⃣ Technology Stack

### Backend
- Laravel 12
- PHP 8.2+
- MySQL (Railway)

### Frontend
- Blade
- Tailwind CDN
- Alpine.js

### Infra
- Railway.app (free tier)
- Docker-based deployment
- File-based cache & sessions

### Payments
- bKash (sandbox → production)

---

## 9️⃣ Revenue Model

### Pricing Strategy
**Affordable Premium**

- Slightly higher than budget operators
- Lower than luxury agencies

Example:
- Cheap tour: BDT 4,000
- Shreya Travels: BDT 4,800–5,500

### Revenue Streams
- Tour package margin (primary)
- Group pricing leverage

Future add-ons:
- Private tours
- Premium seating
- Insurance
- Photography

### Discount Strategy
- Early users only
- ENV-based discount tokens
- No long-term discounts

---

## 🔢 Sample Unit Economics

**Cox’s Bazar – 3 Days**
- Customer price: BDT 6,000 × 10 = 60,000
- Actual cost: ~45,000
- Gross margin: ~15,000
- Margin: ~25%

---

## 1️⃣0️⃣ Go-To-Market Plan

### Phase 1: Soft Launch
- Friends & family
- Limited discount tokens
- Manual support

### Phase 2: Social Proof
- Instagram & Facebook
- Booking screenshots
- Promote “secure booking system”

### Phase 3: Scale Carefully
- Repeat tours
- Better photos
- Transport & hotel partnerships

---

## 1️⃣1️⃣ Operations Plan

### Booking Flow
1. Customer books online
2. Pays via bKash
3. Admin sees booking
4. Manual confirmation
5. Trip execution
6. Post-trip feedback

### Customer Support
- WhatsApp / Phone
- Booking reference used everywhere

---

## 1️⃣2️⃣ Risk & Mitigation

| Risk | Mitigation |
|----|------------|
| Fake bookings | OTP + payment |
| Payment abuse | Rate limiting |
| Tech failure | Simple architecture |
| Trust issues | Dashboard |
| Over-discounting | ENV tokens |

---

## 1️⃣3️⃣ Legal & Compliance
- Terms & Conditions (later)
- Manual cancellation policy
- No sensitive personal data
- Email-only authentication

---

## 1️⃣4️⃣ Scaling Roadmap

### Technical
- Redis cache/session
- DB sessions
- Background jobs
- SMS OTP
- Admin analytics

### Business
- Partner onboarding
- Private tours
- Loyalty program
- Mobile app

---

## 1️⃣5️⃣ KPIs
- Booking conversion rate
- OTP success rate
- Payment success rate
- Repeat customers
- Average booking value

---

## 1️⃣6️⃣ Summary

You now have:
- Clear business model
- Well-defined MVP scope
- Secure minimal architecture
- Revenue clarity
- Deployment-ready plan

**This is strong enough to pitch, build, and launch.**
