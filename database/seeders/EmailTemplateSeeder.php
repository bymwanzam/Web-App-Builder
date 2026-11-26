<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // Build Notifications
            [
                'slug' => 'build_completed',
                'name' => 'Build Completed',
                'category' => 'build',
                'subject' => 'Your {app_name} build is ready!',
                'body' => '<p>Hello {user_name},</p><p>Great news! Your app build has completed successfully.</p><h3>Build Details</h3><ul><li><strong>Platform:</strong> {platform}</li><li><strong>Version:</strong> {version_name} (Build #{version_code})</li><li><strong>File Size:</strong> {file_size}</li><li><strong>Build Time:</strong> {build_time}</li></ul><p><a href="{download_url}">Download your build now</a></p><p>You can also scan the QR code on your device to install directly, or view full details in your dashboard.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'app_name', 'platform', 'version_name', 'version_code', 'file_size', 'build_time', 'download_url'],
            ],
            [
                'slug' => 'build_failed',
                'name' => 'Build Failed',
                'category' => 'build',
                'subject' => 'Build failed for {app_name}',
                'body' => '<p>Hello {user_name},</p><p>Unfortunately, your app build has failed.</p><h3>Build Details</h3><ul><li><strong>App:</strong> {app_name}</li><li><strong>Platform:</strong> {platform}</li><li><strong>Version:</strong> {version_name}</li></ul><h3>Error Details</h3><p>{error_message}</p><h3>Common Solutions</h3><ul><li>Check your app configuration settings</li><li>Verify all required assets are uploaded</li><li>Review the build logs for specific errors</li></ul><p>If the issue persists, please contact our support team.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'app_name', 'platform', 'version_name', 'error_message'],
            ],

            // Payment Notifications
            [
                'slug' => 'bank_transfer_pending',
                'name' => 'Bank Transfer Pending (Admin)',
                'category' => 'payment',
                'subject' => 'New Bank Transfer Payment Pending',
                'body' => '<p>Hello Admin,</p><p>A new bank transfer payment is awaiting verification.</p><h3>User Details</h3><ul><li><strong>Name:</strong> {user_name}</li><li><strong>Email:</strong> {user_email}</li></ul><h3>Transaction Details</h3><ul><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Transaction ID:</strong> {transaction_id}</li><li><strong>Plan:</strong> {plan_name}</li></ul><p><a href="{review_url}">Review this transaction</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'user_email', 'amount', 'currency', 'transaction_id', 'plan_name', 'review_url', 'app_name'],
            ],
            [
                'slug' => 'bank_transfer_approved',
                'name' => 'Bank Transfer Approved',
                'category' => 'payment',
                'subject' => 'Your payment has been confirmed!',
                'body' => '<p>Hello {user_name},</p><p>Great news! Your bank transfer payment has been verified and approved.</p><h3>Payment Details</h3><ul><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Transaction ID:</strong> {transaction_id}</li><li><strong>Date:</strong> {transaction_date}</li></ul><h3>Subscription Details</h3><ul><li><strong>Plan:</strong> {plan_name}</li><li><strong>Status:</strong> Active</li><li><strong>Next Billing:</strong> {next_billing}</li></ul><p>Your subscription is now active and you have full access to all features. Start building your amazing apps!</p><p><a href="{billing_url}">View Billing</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'amount', 'currency', 'transaction_id', 'transaction_date', 'plan_name', 'next_billing', 'billing_url', 'app_name'],
            ],
            [
                'slug' => 'bank_transfer_declined',
                'name' => 'Bank Transfer Declined',
                'category' => 'payment',
                'subject' => 'Payment verification unsuccessful',
                'body' => '<p>Hello {user_name},</p><p>We were unable to verify your bank transfer payment.</p><h3>Transaction Details</h3><ul><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Transaction ID:</strong> {transaction_id}</li></ul><h3>Common Reasons</h3><ul><li>Payment not received in our account</li><li>Amount does not match</li><li>Reference number incorrect</li></ul><p>Please verify your payment details and try again, or contact our support team for assistance.</p><p><a href="{billing_url}">Try Again</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'amount', 'currency', 'transaction_id', 'billing_url', 'app_name'],
            ],

            // Authentication Notifications
            [
                'slug' => 'welcome',
                'name' => 'Welcome Email',
                'category' => 'auth',
                'subject' => 'Welcome to {app_name}!',
                'body' => '<p>Hello {user_name},</p><p>Welcome to {app_name}! We\'re excited to have you on board.</p><p>Your account has been successfully created and you\'re ready to start building amazing mobile apps.</p><h3>Getting Started</h3><ul><li>Create your first app from the dashboard</li><li>Customize your app\'s design and features</li><li>Build and download your native mobile app</li></ul><p><a href="{dashboard_url}">Go to Dashboard</a></p><p>If you have any questions, our support team is here to help.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'app_name', 'dashboard_url'],
            ],
            [
                'slug' => 'password_reset',
                'name' => 'Password Reset',
                'category' => 'auth',
                'subject' => 'Reset Your Password',
                'body' => '<p>Hello {user_name},</p><p>You are receiving this email because we received a password reset request for your account.</p><p><a href="{reset_url}">Reset Password</a></p><p>This password reset link will expire in {expire_minutes} minutes.</p><p>If you did not request a password reset, no further action is required.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'reset_url', 'expire_minutes', 'app_name'],
            ],
            [
                'slug' => 'email_verification',
                'name' => 'Email Verification',
                'category' => 'auth',
                'subject' => 'Verify Your Email Address',
                'body' => '<p>Hello {user_name},</p><p>Please click the button below to verify your email address.</p><p><a href="{verification_url}">Verify Email Address</a></p><p>If you did not create an account, no further action is required.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'verification_url', 'app_name'],
            ],

            // Subscription & Payment Notifications
            [
                'slug' => 'subscription_activated',
                'name' => 'Subscription Activated',
                'category' => 'payment',
                'subject' => 'Welcome to {plan_name}! Your subscription is active',
                'body' => '<p>Hello {user_name},</p><p>Great news! Your subscription has been successfully activated.</p><h3>Subscription Details</h3><ul><li><strong>Plan:</strong> {plan_name}</li><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Next Billing Date:</strong> {next_billing}</li></ul><p>You now have full access to all features included in your plan. Start building your amazing apps!</p><p><a href="{dashboard_url}">Go to Dashboard</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'amount', 'currency', 'next_billing', 'dashboard_url', 'app_name'],
            ],
            [
                'slug' => 'payment_completed',
                'name' => 'Payment Completed',
                'category' => 'payment',
                'subject' => 'Payment received for {plan_name}',
                'body' => '<p>Hello {user_name},</p><p>We have received your payment. Thank you!</p><h3>Payment Details</h3><ul><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Transaction ID:</strong> {transaction_id}</li><li><strong>Date:</strong> {payment_date}</li><li><strong>Plan:</strong> {plan_name}</li></ul><h3>Next Billing</h3><p>Your next billing date is <strong>{next_billing}</strong>.</p><p><a href="{billing_url}">View Billing History</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'amount', 'currency', 'transaction_id', 'payment_date', 'next_billing', 'billing_url', 'app_name'],
            ],
            [
                'slug' => 'payment_failed',
                'name' => 'Payment Failed',
                'category' => 'payment',
                'subject' => 'Payment failed for {plan_name}',
                'body' => '<p>Hello {user_name},</p><p>We were unable to process your payment for your {plan_name} subscription.</p><h3>Payment Details</h3><ul><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Plan:</strong> {plan_name}</li></ul><h3>What to do next</h3><ul><li>Check that your payment method is valid and has sufficient funds</li><li>Update your payment information if needed</li><li>Contact your bank if the issue persists</li></ul><p><a href="{billing_url}">Update Payment Method</a></p><p>If you need assistance, please contact our support team.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'amount', 'currency', 'billing_url', 'app_name'],
            ],
            [
                'slug' => 'subscription_cancelled',
                'name' => 'Subscription Cancelled',
                'category' => 'payment',
                'subject' => 'Your {plan_name} subscription has been cancelled',
                'body' => '<p>Hello {user_name},</p><p>Your {plan_name} subscription has been cancelled as requested.</p><h3>What happens next</h3><ul><li>You will continue to have access until <strong>{end_date}</strong></li><li>After this date, your account will be downgraded to the free plan</li><li>Your apps and data will be preserved</li></ul><p>We\'re sorry to see you go! If you change your mind, you can resubscribe at any time.</p><p><a href="{billing_url}">Resubscribe</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'end_date', 'billing_url', 'app_name'],
            ],
            [
                'slug' => 'subscription_expired',
                'name' => 'Subscription Expired',
                'category' => 'payment',
                'subject' => 'Your {plan_name} subscription has expired',
                'body' => '<p>Hello {user_name},</p><p>Your {plan_name} subscription has expired.</p><h3>What this means</h3><ul><li>Your account has been downgraded to the free plan</li><li>Some features may no longer be available</li><li>Your apps and data are still preserved</li></ul><p>To regain full access to all features, please renew your subscription.</p><p><a href="{billing_url}">Renew Subscription</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'billing_url', 'app_name'],
            ],
            [
                'slug' => 'subscription_renewal_reminder',
                'name' => 'Subscription Renewal Reminder',
                'category' => 'payment',
                'subject' => 'Your {plan_name} subscription renews on {renewal_date}',
                'body' => '<p>Hello {user_name},</p><p>This is a friendly reminder that your subscription will automatically renew soon.</p><h3>Renewal Details</h3><ul><li><strong>Plan:</strong> {plan_name}</li><li><strong>Amount:</strong> {amount} {currency}</li><li><strong>Renewal Date:</strong> {renewal_date}</li></ul><p>No action is required if you wish to continue your subscription. Your payment method on file will be charged automatically.</p><p>If you wish to make changes to your subscription or update your payment method, please visit your billing settings.</p><p><a href="{billing_url}">Manage Subscription</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'plan_name', 'amount', 'currency', 'renewal_date', 'billing_url', 'app_name'],
            ],

            // GDPR & Privacy Notifications
            [
                'slug' => 'data_export_ready',
                'name' => 'Data Export Ready',
                'category' => 'gdpr',
                'subject' => 'Your data export is ready to download',
                'body' => '<p>Hello {user_name},</p><p>Your data export request has been processed and is ready for download.</p><h3>Download Details</h3><ul><li><strong>File Size:</strong> {file_size}</li><li><strong>Expires:</strong> {expires_at}</li></ul><p><a href="{download_url}">Download Your Data</a></p><p>This download link will expire on {expires_at}. Please download your data before this date.</p><p>Your export includes:</p><ul><li>Profile information</li><li>Apps and configurations</li><li>Subscription history</li><li>Transaction records</li><li>Consent preferences</li></ul><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'file_size', 'expires_at', 'download_url', 'app_name'],
            ],
            [
                'slug' => 'account_deletion_scheduled',
                'name' => 'Account Deletion Scheduled',
                'category' => 'gdpr',
                'subject' => 'Your account deletion has been scheduled',
                'body' => '<p>Hello {user_name},</p><p>We have received your request to delete your account. Your account and all associated data will be permanently deleted on <strong>{deletion_date}</strong>.</p><h3>What will be deleted</h3><ul><li>Your profile and personal information</li><li>All apps and configurations</li><li>Build history and artifacts</li><li>Subscription and payment records (after legal retention period)</li><li>All associated data</li></ul><h3>Changed your mind?</h3><p>If you wish to cancel this deletion request, you can do so before <strong>{deletion_date}</strong>.</p><p><a href="{cancel_url}">Cancel Account Deletion</a></p><p>After this date, your data cannot be recovered.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'deletion_date', 'cancel_url', 'app_name'],
            ],
            [
                'slug' => 'account_deletion_cancelled',
                'name' => 'Account Deletion Cancelled',
                'category' => 'gdpr',
                'subject' => 'Your account deletion has been cancelled',
                'body' => '<p>Hello {user_name},</p><p>Good news! Your account deletion request has been cancelled. Your account and all associated data will remain intact.</p><p>No further action is required. You can continue using your account as normal.</p><p>If you did not request this cancellation or have any concerns, please contact our support team immediately.</p><p><a href="{dashboard_url}">Go to Dashboard</a></p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'dashboard_url', 'app_name'],
            ],
            [
                'slug' => 'inactive_account_warning',
                'name' => 'Inactive Account Warning',
                'category' => 'gdpr',
                'subject' => 'Your account will be deleted due to inactivity',
                'body' => '<p>Hello {user_name},</p><p>We noticed you haven\'t logged into your account for a while. In accordance with our data retention policy, inactive accounts are automatically deleted after {retention_days} days of inactivity.</p><h3>Account Status</h3><ul><li><strong>Last Login:</strong> {last_login}</li><li><strong>Scheduled Deletion:</strong> {deletion_date}</li></ul><h3>Want to keep your account?</h3><p>Simply log in before <strong>{deletion_date}</strong> to keep your account active.</p><p><a href="{login_url}">Log In Now</a></p><p>If you no longer need your account, no action is required and it will be automatically deleted on the scheduled date.</p><p>Thanks,<br>{app_name}</p>',
                'variables' => ['user_name', 'last_login', 'retention_days', 'deletion_date', 'login_url', 'app_name'],
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['slug' => $template['slug']],
                [
                    'name' => $template['name'],
                    'subject' => $template['subject'],
                    'body' => $template['body'],
                    'variables' => $template['variables'],
                    'category' => $template['category'],
                    'default_subject' => $template['subject'],
                    'default_body' => $template['body'],
                    'is_active' => true,
                ]
            );
        }
    }
}
