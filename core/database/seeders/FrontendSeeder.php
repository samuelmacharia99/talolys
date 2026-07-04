<?php

namespace Database\Seeders;

use App\Models\Frontend;
use Illuminate\Database\Seeder;

class FrontendSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBanner();
        $this->seedAbout();
        $this->seedServices();
        $this->seedFeatures();
        $this->seedWhyChoose();
        $this->seedFaq();
        $this->seedTestimonials();
        $this->seedFooter();
        $this->seedContactUs();
        $this->seedCounter();
        $this->seedSocialLinks();
        $this->seedHowItWorks();
        $this->seedPartners();

        $this->command->info('Frontend content seeded with Kenya-focused banking content.');
    }

    protected function upsertContent(string $dataKeys, array $dataValues): void
    {
        Frontend::updateOrCreate(
            ['data_keys' => $dataKeys, 'tempname' => 'crystal_sky'],
            ['data_values' => $dataValues]
        );
    }

    protected function upsertElement(string $dataKeys, array $dataValues): void
    {
        Frontend::create([
            'data_keys'   => $dataKeys,
            'tempname'    => 'crystal_sky',
            'data_values' => $dataValues,
        ]);
    }

    protected function clearElements(string $dataKeys): void
    {
        Frontend::where('data_keys', $dataKeys)
            ->where('tempname', 'crystal_sky')
            ->delete();
    }

    protected function seedBanner(): void
    {
        $this->upsertContent('banner.content', [
            'heading'         => 'Empowering Kenyan Institutions with Modern Digital Banking',
            'title'           => 'Secure, compliant, and built for Kenyan SACCOs, microfinance institutions, and community banks. Manage deposits, loans, and transfers with confidence.',
            'total_user'      => '10,000+',
            'button_text'     => 'Get Started',
            'button_link'     => '/user/register',
            'video_link'      => '',
            'image'           => '',
            'video_thumbnail' => '',
            'user_images'     => '',
        ]);
    }

    protected function seedAbout(): void
    {
        $this->upsertContent('about.content', [
            'heading'            => 'About Talolys',
            'subheading'         => 'Built for Kenya\'s Growing Financial Sector',
            'image'              => '',
            'image_popup_digit'  => '15+',
            'image_popup_title'  => 'Years Serving Kenyans',
            'image_popup_icon'   => '<i class="las la-award"></i>',
        ]);

        $this->clearElements('about.element');
        $elements = [
            ['heading' => 'Our Mission', 'description' => 'To democratize financial services across Kenya by providing secure, accessible, and affordable digital banking solutions to institutions and their members in all 47 counties.'],
            ['heading' => 'Our Vision', 'description' => 'To be the leading digital banking platform for Kenyan financial institutions, driving financial inclusion from urban centres to the furthest corners of rural Kenya.'],
            ['heading' => 'Our Values', 'description' => 'Integrity, transparency, and community-first approach. We believe every Kenyan deserves access to reliable, secure, and modern financial services regardless of their location.'],
        ];

        foreach ($elements as $element) {
            $this->upsertElement('about.element', $element);
        }
    }

    protected function seedServices(): void
    {
        $this->upsertContent('service.content', [
            'heading'    => 'Our Services',
            'subheading' => 'Complete Banking Solutions for Kenyan Institutions',
            'image'      => '',
        ]);

        $this->clearElements('service.element');
        $services = [
            ['heading' => 'Savings & Deposits', 'description' => 'Flexible savings accounts, fixed deposits, and term deposits with competitive interest rates for your members.', 'icon' => '<i class="las la-piggy-bank"></i>'],
            ['heading' => 'Loan Management', 'description' => 'End-to-end loan processing from application to disbursement. Personal loans, business loans, and emergency facilities.', 'icon' => '<i class="las la-hand-holding-usd"></i>'],
            ['heading' => 'Mobile Banking', 'description' => 'M-Pesa integrated mobile banking. Members can deposit, withdraw, and transfer funds from their phones 24/7.', 'icon' => '<i class="las la-mobile-alt"></i>'],
            ['heading' => 'Money Transfers', 'description' => 'Instant inter-bank transfers, EFT, RTGS, and SWIFT payments. Send money locally or internationally.', 'icon' => '<i class="las la-exchange-alt"></i>'],
            ['heading' => 'Agency Banking', 'description' => 'Extend your reach through agent networks. Process deposits and withdrawals at authorized agent locations across Kenya.', 'icon' => '<i class="las la-store"></i>'],
            ['heading' => 'Digital Payments', 'description' => 'Accept card payments, mobile money, and digital wallets. Seamless payment processing for businesses and individuals.', 'icon' => '<i class="las la-credit-card"></i>'],
        ];

        foreach ($services as $service) {
            $this->upsertElement('service.element', $service);
        }
    }

    protected function seedFeatures(): void
    {
        $this->upsertContent('feature.content', [
            'heading'    => 'Why Talolys',
            'subheading' => 'Trusted by Leading Kenyan Financial Institutions',
        ]);

        $this->clearElements('feature.element');
        $features = [
            ['heading' => 'CBK Compliant', 'subheading' => 'Fully compliant with Central Bank of Kenya regulations and banking standards. Regular audits and reporting built-in.', 'icon' => '<i class="las la-shield-alt"></i>'],
            ['heading' => 'SASRA Ready', 'subheading' => 'Purpose-built for SACCO Societies Regulatory Authority requirements. Automated reporting and compliance tracking.', 'icon' => '<i class="las la-balance-scale"></i>'],
            ['heading' => 'M-Pesa Integration', 'subheading' => 'Seamless Safaricom M-Pesa integration for deposits, withdrawals, and payments. Real-time transaction processing.', 'icon' => '<i class="las la-mobile"></i>'],
            ['heading' => 'Multi-Branch Support', 'subheading' => 'Manage operations across multiple branches with real-time synchronization and centralized reporting.', 'icon' => '<i class="las la-sitemap"></i>'],
            ['heading' => 'Data Security', 'subheading' => 'Bank-grade 256-bit encryption, two-factor authentication, and transaction monitoring to protect member data.', 'icon' => '<i class="las la-lock"></i>'],
            ['heading' => '24/7 Availability', 'subheading' => 'Cloud-hosted infrastructure with 99.9% uptime guarantee. Your banking services never sleep, even during peak periods.', 'icon' => '<i class="las la-server"></i>'],
        ];

        foreach ($features as $feature) {
            $this->upsertElement('feature.element', $feature);
        }
    }

    protected function seedWhyChoose(): void
    {
        $this->upsertContent('why_choose.content', [
            'heading'      => 'Why Choose Us',
            'subheading'   => 'The Banking Partner Kenyan Institutions Trust',
            'icon'         => '<i class="las la-check-circle"></i>',
            'title'        => '99.9% Uptime',
            'subtitle'     => 'Enterprise-grade reliability',
            'slogan'       => 'Banking without boundaries',
            'image_one'    => '',
            'image_two'    => '',
            'circle_image' => '',
        ]);

        $this->clearElements('why_choose.element');
        $elements = [
            ['heading' => 'Local Support Team', 'description' => 'Dedicated Kenya-based support team available via phone, email, and in-person visits. We speak your language and understand your challenges.'],
            ['heading' => 'Rapid Deployment', 'description' => 'Get your institution up and running in days, not months. Our onboarding team handles data migration, staff training, and go-live support.'],
            ['heading' => 'Affordable Pricing', 'description' => 'Transparent pricing with no hidden fees. Scalable plans designed for SACCOs, MFIs, and community banks of all sizes.'],
        ];

        foreach ($elements as $element) {
            $this->upsertElement('why_choose.element', $element);
        }
    }

    protected function seedFaq(): void
    {
        $this->upsertContent('faq.content', [
            'heading'     => 'FAQ',
            'subheading'  => 'Frequently Asked Questions',
            'description' => 'Everything you need to know about getting started with Talolys digital banking.',
            'button_text' => 'Contact Support',
            'button_link' => '/contact',
        ]);

        $this->clearElements('faq.element');
        $faqs = [
            ['question' => 'Is Talolys regulated by the Central Bank of Kenya?', 'answer' => 'Yes. Talolys operates within the regulatory framework set by the Central Bank of Kenya (CBK) and meets all compliance requirements for digital banking platforms serving financial institutions.'],
            ['question' => 'Can members access banking via M-Pesa?', 'answer' => 'Absolutely. Talolys integrates directly with Safaricom M-Pesa, allowing members to deposit, withdraw, check balances, and make loan repayments from their mobile phones.'],
            ['question' => 'How long does it take to set up our institution?', 'answer' => 'Most institutions are fully operational within 5-10 business days. This includes system configuration, data migration from your existing system, staff training, and testing.'],
            ['question' => 'Is our members\' data secure?', 'answer' => 'We employ bank-grade 256-bit SSL encryption, regular security audits, two-factor authentication, and comply with Kenya\'s Data Protection Act 2019 to ensure all member data is fully protected.'],
            ['question' => 'What types of institutions can use Talolys?', 'answer' => 'Talolys is designed for SACCOs, Microfinance Institutions (MFIs), community banks, investment groups (chamas), and other financial cooperatives operating in Kenya.'],
            ['question' => 'Do you provide training for our staff?', 'answer' => 'Yes. We provide comprehensive onboarding training for all staff members, including administrators, tellers, loan officers, and branch managers. Ongoing support and refresher training is also available.'],
        ];

        foreach ($faqs as $faq) {
            $this->upsertElement('faq.element', $faq);
        }
    }

    protected function seedTestimonials(): void
    {
        $this->upsertContent('testimonial.content', [
            'heading'    => 'Testimonials',
            'subheading' => 'What Kenyan Institutions Say About Us',
        ]);

        $this->clearElements('testimonial.element');
        $testimonials = [
            ['name' => 'James Mwangi', 'designation' => 'CEO, Ukulima SACCO', 'quote' => 'Talolys transformed our operations. Our members can now access their accounts and apply for loans from their phones. Loan processing time dropped from 2 weeks to 48 hours.', 'rating' => '5', 'image' => ''],
            ['name' => 'Grace Wanjiku', 'designation' => 'Operations Manager, Bora MFI', 'quote' => 'The M-Pesa integration alone saved us countless hours of manual reconciliation. Our agents across Nairobi and Kiambu counties process transactions seamlessly.', 'rating' => '5', 'image' => ''],
            ['name' => 'Peter Ochieng', 'designation' => 'Chairman, Pwani Savings SACCO', 'quote' => 'As a coastal SACCO, we needed a system that works even with intermittent connectivity. Talolys delivers. Our 3,000+ members across 4 branches are well served.', 'rating' => '5', 'image' => ''],
        ];

        foreach ($testimonials as $testimonial) {
            $this->upsertElement('testimonial.element', $testimonial);
        }
    }

    protected function seedFooter(): void
    {
        $this->upsertContent('footer.content', [
            'title'         => 'Talolys',
            'contact_title' => 'Contact Us',
            'description'   => 'Empowering Kenyan financial institutions with secure, modern digital banking solutions. Regulated, compliant, and built for growth.',
        ]);
    }

    protected function seedContactUs(): void
    {
        $this->upsertContent('contact_us.content', [
            'heading'         => 'Get in Touch',
            'subheading'      => 'We\'d love to hear from you. Reach out to our team.',
            'contact_address' => 'Westlands, Nairobi, Kenya',
            'contact_number'  => '+254 700 000 000',
            'email_address'   => 'info@talolys.com',
            'map_source'      => '',
        ]);
    }

    protected function seedCounter(): void
    {
        $this->upsertContent('counter.content', ['image' => '']);

        $this->clearElements('counter.element');
        $counters = [
            ['title' => 'Active Members', 'digit' => '10000', 'symbol' => '+'],
            ['title' => 'Partner Institutions', 'digit' => '50', 'symbol' => '+'],
            ['title' => 'Counties Covered', 'digit' => '47', 'symbol' => ''],
            ['title' => 'Transactions Daily', 'digit' => '25000', 'symbol' => '+'],
        ];

        foreach ($counters as $counter) {
            $this->upsertElement('counter.element', $counter);
        }
    }

    protected function seedSocialLinks(): void
    {
        $this->clearElements('social_link.element');
        $links = [
            ['social_icon' => '<i class="lab la-facebook-f"></i>', 'social_link' => 'https://facebook.com/talolys'],
            ['social_icon' => '<i class="lab la-twitter"></i>', 'social_link' => 'https://twitter.com/talolys'],
            ['social_icon' => '<i class="lab la-linkedin-in"></i>', 'social_link' => 'https://linkedin.com/company/talolys'],
            ['social_icon' => '<i class="lab la-instagram"></i>', 'social_link' => 'https://instagram.com/talolys'],
        ];

        foreach ($links as $link) {
            $this->upsertElement('social_link.element', $link);
        }
    }

    protected function seedHowItWorks(): void
    {
        $this->upsertContent('how_it_work.content', [
            'title'   => 'Getting Started',
            'heading' => 'Start Banking in 4 Simple Steps',
        ]);

        $this->clearElements('how_it_work.element');
        $steps = [
            ['heading' => 'Register Your Institution', 'subheading' => 'Complete a quick online registration form with your institution details and compliance documents.'],
            ['heading' => 'System Configuration', 'subheading' => 'Our team configures the platform to match your products, rates, and operational workflows.'],
            ['heading' => 'Staff Onboarding', 'subheading' => 'We train your team on the platform — from tellers and loan officers to branch managers.'],
            ['heading' => 'Go Live', 'subheading' => 'Launch your digital banking services and start serving members across all your branches.'],
        ];

        foreach ($steps as $step) {
            $this->upsertElement('how_it_work.element', $step);
        }
    }

    protected function seedPartners(): void
    {
        $this->upsertContent('partner_section.content', [
            'heading' => 'Trusted by Leading Kenyan Financial Institutions',
        ]);

        $this->upsertContent('subscribe.content', [
            'heading' => 'Stay Updated with Talolys',
        ]);
    }
}
