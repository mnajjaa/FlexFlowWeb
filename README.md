# FlexFlow Web Platform

FlexFlow is a Symfony-powered sports and wellness platform that unifies coaching, events, products, and community engagement into a single experience. The application blends dynamic content, transactional services, and intelligent automation so teams can manage their ecosystem and offer members real-time support.

## Core Technologies (and why we use them)

| Technology | Why it matters |
| --- | --- |
| **Symfony 5.4** (`symfony/framework-bundle`, `symfony/security-bundle`, `symfony/messenger`, etc.) | Provides a mature MVC framework, dependency injection container, and rich security/queueing toolset for building maintainable enterprise-grade modules.【F:composer.json†L32-L56】 |
| **Doctrine ORM & DBAL** (`doctrine/orm`, `doctrine/dbal`) | Powers data persistence with expressive repositories and migrations, enabling complex entities such as events, reservations, products, and evaluations.【F:composer.json†L13-L19】 |
| **Twig + Symfony UX** (`symfony/twig-bundle`, `symfony/stimulus-bundle`, `symfony/ux-turbo`) | Delivers fast, server-rendered templates enhanced with reactive components and Turbo-powered partial updates for member and admin dashboards.【F:composer.json†L45-L52】 |
| **ApexCharts & Google Charts** (`akaunting/laravel-apexcharts`, `cmen/google-charts-bundle`) | Supplies ready-to-use charting layers for analytics dashboards that visualize offers, demands, and member trends with minimal boilerplate.【F:composer.json†L9-L12】 |
| **Stripe PHP SDK** (`stripe/stripe-php`) | Handles secure checkout sessions and redirects for product payments, reducing PCI footprint while still offering branded purchase flows.【F:composer.json†L27-L28】【F:src/Controller/PaymentController.php†L21-L47】 |
| **Twilio SDK** (`twilio/sdk`) | Automates outbound SMS confirmations so attendees receive instant reservation details and reminders on their phones.【F:composer.json†L57-L58】【F:src/Controller/SmsController.php†L9-L46】 |
| **OpenAI PHP Client + Guzzle** (`openai-php/client`, `guzzlehttp/guzzle`) | Bridges AI-assisted chat that augments support staff with contextual replies tailored to members’ sports queries.【F:composer.json†L20-L22】【F:src/Controller/ChatController.php†L20-L52】 |
| **Endroid QR Code Bundle** (`endroid/qr-code`, `endroid/qr-code-bundle`) | Generates scannable passes that encode participant and event metadata, streamlining check-in and anti-fraud controls.【F:composer.json†L23-L26】【F:src/Controller/EvenementMemberController.php†L18-L56】 |
| **Symfony Notifier & Mailer stack** (`symfony/mailer`, `symfony/notifier`, `symfony/twilio-notifier`, `symfony/google-mailer`) | Coordinates multi-channel communication pipelines—email, SMS, and push—so each workflow reaches members on their preferred channel.【F:composer.json†L39-L44】 |

## Fabulous Features

- **AI-powered concierge chat.** Members can open `/chat` to receive contextual answers backed by OpenAI, improving support availability around the clock.【F:src/Controller/ChatController.php†L15-L52】
- **One-click Stripe checkout.** The `/checkout` flow spins up Stripe Sessions and redirects customers to a branded payment page with post-payment success and cancel routes.【F:src/Controller/PaymentController.php†L21-L52】
- **QR-coded event passes.** Event reservations instantly produce QR tickets encoding participant, event, and timestamp details for smooth door scanning.【F:src/Controller/EvenementMemberController.php†L34-L56】
- **Smart capacity & calendar management.** Event booking endpoints prevent duplicate reservations, decrement available seats, and feed dynamic calendars so members always see fresh availability.【F:src/Controller/EvenementMemberController.php†L73-L136】
- **Transactional SMS reminders.** After securing a reservation, Twilio dispatches personalized confirmations—keeping attendees informed without manual outreach.【F:src/Controller/SmsController.php†L9-L46】
- **Feedback-driven improvements.** Offers collect member evaluations that are persisted for analytics, guiding coaching and product iteration.【F:src/Controller/EvaluationController.php†L13-L33】
- **Data-rich dashboards.** Doctrine-powered repositories aggregate demand statistics for visual reports rendered via the charting integrations.【F:src/Controller/StatController.php†L9-L21】

## Getting Started

1. **Install PHP dependencies**
   ```bash
   composer install
   ```
2. **Configure environment secrets** (database, Stripe, Twilio, OpenAI) in your `.env.local` file.
3. **Run database migrations**
   ```bash
   php bin/console doctrine:migrations:migrate
   ```
4. **Start the Symfony server**
   ```bash
   symfony serve -d
   ```
5. **Build front-end assets** (optional, if using Webpack Encore or Stimulus controllers)
   ```bash
   yarn install && yarn encore dev
   ```

With those steps complete, open your browser at `https://localhost:8000` (or the port reported by Symfony CLI) and explore the FlexFlow experience.
