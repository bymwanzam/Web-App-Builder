<?php

namespace App\Plugins\Platforms\AndroidWebView;

class PageTemplates
{
    /**
     * Get all available page templates.
     */
    public static function getTemplates(): array
    {
        return [
            'blank' => [
                'name' => 'Blank Page',
                'description' => 'Start with a clean slate',
                'html' => '<div class="container">
    <h1>Page Title</h1>
    <p>Your content here...</p>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    padding: 20px;
}

.container {
    max-width: 800px;
    margin: 0 auto;
}

h1 {
    margin-bottom: 16px;
    color: #111;
}

p {
    margin-bottom: 12px;
}',
                'js' => '',
            ],
            'about' => [
                'name' => 'About Us',
                'description' => 'Company information page',
                'html' => '<div class="about-page">
    <header class="hero">
        <h1>About Us</h1>
        <p class="tagline">Your company tagline goes here</p>
    </header>

    <section class="content">
        <h2>Our Story</h2>
        <p>Tell your story here. Share your mission, vision, and what makes your company unique.</p>

        <h2>Our Mission</h2>
        <p>Describe your mission and the values that drive your business.</p>

        <h2>Contact Us</h2>
        <p>Email: contact@example.com</p>
        <p>Phone: (123) 456-7890</p>
    </section>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
}

.about-page {
    max-width: 800px;
    margin: 0 auto;
}

.hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 20px;
    text-align: center;
}

.hero h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.tagline {
    font-size: 1.2rem;
    opacity: 0.9;
}

.content {
    padding: 40px 20px;
}

.content h2 {
    color: #667eea;
    margin-top: 30px;
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.content h2:first-child {
    margin-top: 0;
}

.content p {
    margin-bottom: 12px;
}',
                'js' => '',
            ],
            'contact' => [
                'name' => 'Contact',
                'description' => 'Contact information page',
                'html' => '<div class="contact-page">
    <h1>Contact Us</h1>
    <p class="intro">We\'d love to hear from you. Get in touch with us using the information below.</p>

    <div class="contact-info">
        <div class="contact-item">
            <div class="icon">📧</div>
            <div class="details">
                <h3>Email</h3>
                <p>contact@example.com</p>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon">📞</div>
            <div class="details">
                <h3>Phone</h3>
                <p>(123) 456-7890</p>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon">📍</div>
            <div class="details">
                <h3>Address</h3>
                <p>123 Main Street<br>City, State 12345</p>
            </div>
        </div>

        <div class="contact-item">
            <div class="icon">🕐</div>
            <div class="details">
                <h3>Hours</h3>
                <p>Mon-Fri: 9am - 5pm<br>Sat-Sun: Closed</p>
            </div>
        </div>
    </div>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    padding: 20px;
    background: #f5f5f5;
}

.contact-page {
    max-width: 600px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    margin-bottom: 10px;
    color: #111;
}

.intro {
    text-align: center;
    color: #666;
    margin-bottom: 30px;
}

.contact-info {
    display: grid;
    gap: 15px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 20px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.contact-item .icon {
    font-size: 24px;
    width: 50px;
    height: 50px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f0f0;
    border-radius: 50%;
}

.contact-item h3 {
    font-size: 14px;
    color: #666;
    margin-bottom: 4px;
}

.contact-item p {
    font-size: 16px;
    color: #111;
}',
                'js' => '',
            ],
            'menu' => [
                'name' => 'Menu / Products',
                'description' => 'Grid layout for items',
                'html' => '<div class="menu-page">
    <h1>Our Menu</h1>

    <div class="category">
        <h2>Category 1</h2>
        <div class="items">
            <div class="item">
                <div class="item-info">
                    <h3>Item Name</h3>
                    <p>Description of this item goes here.</p>
                </div>
                <div class="item-price">$9.99</div>
            </div>
            <div class="item">
                <div class="item-info">
                    <h3>Another Item</h3>
                    <p>Description of this item goes here.</p>
                </div>
                <div class="item-price">$12.99</div>
            </div>
            <div class="item">
                <div class="item-info">
                    <h3>Third Item</h3>
                    <p>Description of this item goes here.</p>
                </div>
                <div class="item-price">$8.99</div>
            </div>
        </div>
    </div>

    <div class="category">
        <h2>Category 2</h2>
        <div class="items">
            <div class="item">
                <div class="item-info">
                    <h3>Item Name</h3>
                    <p>Description of this item goes here.</p>
                </div>
                <div class="item-price">$14.99</div>
            </div>
            <div class="item">
                <div class="item-info">
                    <h3>Another Item</h3>
                    <p>Description of this item goes here.</p>
                </div>
                <div class="item-price">$11.99</div>
            </div>
        </div>
    </div>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    padding: 20px;
    background: #fafafa;
}

.menu-page {
    max-width: 800px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #111;
}

.category {
    margin-bottom: 30px;
}

.category h2 {
    font-size: 1.3rem;
    color: #e67e22;
    margin-bottom: 15px;
    padding-bottom: 8px;
    border-bottom: 2px solid #e67e22;
}

.items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    padding: 15px;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
}

.item-info h3 {
    font-size: 1rem;
    margin-bottom: 4px;
}

.item-info p {
    font-size: 0.9rem;
    color: #666;
}

.item-price {
    font-weight: 600;
    color: #e67e22;
    font-size: 1.1rem;
    white-space: nowrap;
    margin-left: 15px;
}',
                'js' => '',
            ],
            'terms' => [
                'name' => 'Terms & Conditions',
                'description' => 'Legal document template',
                'html' => '<div class="terms-page">
    <h1>Terms & Conditions</h1>
    <p class="last-updated">Last updated: January 1, 2025</p>

    <section>
        <h2>1. Introduction</h2>
        <p>Welcome to our application. By using this app, you agree to be bound by these Terms and Conditions.</p>
    </section>

    <section>
        <h2>2. Use of Service</h2>
        <p>You agree to use this service only for lawful purposes and in accordance with these Terms.</p>
        <ul>
            <li>You must be at least 13 years old to use this service</li>
            <li>You are responsible for maintaining the security of your account</li>
            <li>You must not misuse or abuse the service</li>
        </ul>
    </section>

    <section>
        <h2>3. Privacy</h2>
        <p>Your privacy is important to us. Please review our Privacy Policy to understand how we collect and use your information.</p>
    </section>

    <section>
        <h2>4. Modifications</h2>
        <p>We reserve the right to modify these terms at any time. Continued use of the service after changes constitutes acceptance of the new terms.</p>
    </section>

    <section>
        <h2>5. Contact</h2>
        <p>If you have questions about these Terms, please contact us at legal@example.com.</p>
    </section>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.8;
    color: #333;
    padding: 20px;
    background: #fff;
}

.terms-page {
    max-width: 800px;
    margin: 0 auto;
}

h1 {
    font-size: 1.8rem;
    margin-bottom: 8px;
    color: #111;
}

.last-updated {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

section {
    margin-bottom: 25px;
}

h2 {
    font-size: 1.2rem;
    color: #111;
    margin-bottom: 10px;
}

p {
    margin-bottom: 12px;
}

ul {
    margin-left: 25px;
    margin-bottom: 12px;
}

li {
    margin-bottom: 6px;
}',
                'js' => '',
            ],
            'faq' => [
                'name' => 'FAQ',
                'description' => 'Frequently asked questions',
                'html' => '<div class="faq-page">
    <h1>Frequently Asked Questions</h1>

    <div class="faq-list">
        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>What is this app about?</span>
                <span class="icon">+</span>
            </button>
            <div class="faq-answer">
                <p>This is a sample FAQ answer. Replace this with information about your app.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>How do I get started?</span>
                <span class="icon">+</span>
            </button>
            <div class="faq-answer">
                <p>Getting started is easy! Just follow the onboarding steps when you first open the app.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>How can I contact support?</span>
                <span class="icon">+</span>
            </button>
            <div class="faq-answer">
                <p>You can reach our support team at support@example.com. We typically respond within 24 hours.</p>
            </div>
        </div>

        <div class="faq-item">
            <button class="faq-question" onclick="toggleFaq(this)">
                <span>Is my data secure?</span>
                <span class="icon">+</span>
            </button>
            <div class="faq-answer">
                <p>Yes! We take security seriously and use industry-standard encryption to protect your data.</p>
            </div>
        </div>
    </div>
</div>',
                'css' => '* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
    padding: 20px;
    background: #f5f5f5;
}

.faq-page {
    max-width: 700px;
    margin: 0 auto;
}

h1 {
    text-align: center;
    margin-bottom: 30px;
    color: #111;
}

.faq-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.faq-item {
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.faq-question {
    width: 100%;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 18px 20px;
    background: white;
    border: none;
    cursor: pointer;
    font-size: 1rem;
    font-weight: 500;
    text-align: left;
    color: #111;
}

.faq-question:hover {
    background: #fafafa;
}

.faq-question .icon {
    font-size: 1.5rem;
    color: #667eea;
    transition: transform 0.3s;
}

.faq-question.active .icon {
    transform: rotate(45deg);
}

.faq-answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.faq-answer p {
    padding: 0 20px 18px;
    color: #666;
}

.faq-answer.show {
    max-height: 200px;
}',
                'js' => 'function toggleFaq(button) {
    const item = button.parentElement;
    const answer = item.querySelector(\'.faq-answer\');
    const isActive = button.classList.contains(\'active\');

    // Close all other FAQs
    document.querySelectorAll(\'.faq-question\').forEach(q => {
        q.classList.remove(\'active\');
        q.parentElement.querySelector(\'.faq-answer\').classList.remove(\'show\');
    });

    // Toggle current FAQ
    if (!isActive) {
        button.classList.add(\'active\');
        answer.classList.add(\'show\');
    }
}',
            ],
        ];
    }

    /**
     * Get a specific template by key.
     */
    public static function getTemplate(string $key): ?array
    {
        return static::getTemplates()[$key] ?? null;
    }

    /**
     * Get template options for dropdown.
     */
    public static function getTemplateOptions(): array
    {
        $options = [];
        foreach (static::getTemplates() as $key => $template) {
            $options[$key] = [
                'name' => $template['name'],
                'description' => $template['description'],
            ];
        }

        return $options;
    }
}
