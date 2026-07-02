<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('account_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 255)->nullable();
            $table->string("icon", 255)->nullable();
            $table->decimal("min_transaction_amount", 28, 8)->nullable();
            $table->decimal("bonus_amount", 28, 8)->nullable();
            $table->string("description", 255)->nullable();
            $table->tinyInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("role_id");
            $table->string("name", 40)->nullable();
            $table->string("email", 40)->nullable();
            $table->string("username", 40)->nullable();
            $table->timestamp("email_verified_at")->nullable();
            $table->string("image", 255)->nullable();
            $table->string("password", 255);
            $table->boolean("status");
            $table->string("remember_token", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'username']);
            $table->unique(['tenant_id', 'email']);
        });

        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->string("title", 255)->nullable();
            $table->boolean("is_read");
            $table->text("click_url")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('admin_password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("email", 40)->nullable();
            $table->string("token", 40)->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('api_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("provider", 255)->nullable();
            $table->mediumText("credentials")->nullable();
            $table->boolean("test_mode");
            $table->string("token_type", 40)->nullable();
            $table->longText("access_token")->nullable();
            $table->timestamp("token_expired_on")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('assign_branch_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("staff_id");
            $table->integer("branch_id");
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('authorizations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("card_id");
            $table->string("status", 255);
            $table->tinyInteger("approved");
            $table->decimal("amount", 28, 8);
            $table->text("merchant_data")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('balance_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->bigInteger("wallet_id")->nullable();
            $table->unsignedInteger("beneficiary_id");
            $table->string("trx", 40)->nullable();
            $table->decimal("amount", 28, 8);
            $table->decimal("base_currency_amount", 28, 8)->nullable();
            $table->decimal("charge", 28, 8);
            $table->text("reject_reason")->nullable();
            $table->text("wire_transfer_data")->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->string("beneficiary_type", 40)->nullable();
            $table->unsignedInteger("beneficiary_id")->nullable();
            $table->string("account_number", 255)->nullable();
            $table->string("account_name", 255)->nullable();
            $table->string("short_name", 100);
            $table->text("details")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('branches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40);
            $table->string("code", 40);
            $table->string("email", 40)->nullable();
            $table->string("mobile", 40)->nullable();
            $table->string("phone", 40)->nullable();
            $table->string("fax", 40)->nullable();
            $table->string("routing_number", 40)->nullable();
            $table->string("swift_code", 40)->nullable();
            $table->string("address", 255);
            $table->text("map_location")->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('branch_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 255);
            $table->string("email", 255);
            $table->string("mobile", 255);
            $table->boolean("designation");
            $table->string("address", 255);
            $table->string("resume", 255)->nullable();
            $table->string("password", 255);
            $table->boolean("status");
            $table->string("image", 255)->nullable();
            $table->string("remember_token", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'email']);
        });

        Schema::create('branch_staff_password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("email", 40);
            $table->string("token", 40);
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('cron_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->string("alias", 40)->nullable();
            $table->text("action")->nullable();
            $table->string("url", 255)->nullable();
            $table->integer("cron_schedule_id");
            $table->dateTime("next_run")->nullable();
            $table->dateTime("last_run")->nullable();
            $table->boolean("is_running");
            $table->boolean("is_default");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('cron_job_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("cron_job_id");
            $table->dateTime("start_at")->nullable();
            $table->dateTime("end_at")->nullable();
            $table->integer("duration");
            $table->text("error")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('cron_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->integer("interval");
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->integer("card_id")->nullable();
            $table->integer("wallet_id")->nullable();
            $table->decimal("wallet_amount", 28, 8)->nullable();
            $table->boolean("is_card_issue");
            $table->integer("virtual_card_id");
            $table->unsignedInteger("branch_id");
            $table->unsignedInteger("branch_staff_id");
            $table->unsignedInteger("method_code");
            $table->decimal("amount", 28, 8);
            $table->string("method_currency", 40)->nullable();
            $table->decimal("charge", 28, 8);
            $table->decimal("rate", 28, 8);
            $table->decimal("final_amount", 28, 8);
            $table->text("detail")->nullable();
            $table->string("btc_amount", 255)->nullable();
            $table->string("btc_wallet", 255)->nullable();
            $table->string("trx", 40)->nullable();
            $table->integer("payment_try");
            $table->boolean("status");
            $table->boolean("from_api");
            $table->boolean("is_web");
            $table->text("admin_feedback")->nullable();
            $table->string("success_url", 255)->nullable();
            $table->string("failed_url", 255)->nullable();
            $table->longText("card_issue_details")->nullable();
            $table->boolean("is_topup")->nullable();
            $table->integer("last_cron");
            $table->text("topup_detail")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('device_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->boolean("is_app");
            $table->text("token");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('dps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("dps_number", 40)->nullable();
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("plan_id");
            $table->decimal("per_installment", 28, 8);
            $table->decimal("interest_rate", 5, 2);
            $table->integer("installment_interval");
            $table->integer("delay_value");
            $table->decimal("charge_per_installment", 28, 8);
            $table->decimal("delay_charge", 28, 8);
            $table->integer("given_installment");
            $table->integer("total_installment");
            $table->unsignedInteger("status");
            $table->string("withdrawn_at")->nullable();
            $table->timestamp("due_notification_sent")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('dps_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40);
            $table->decimal("per_installment", 28, 8);
            $table->integer("installment_interval");
            $table->integer("total_installment");
            $table->decimal("interest_rate", 5, 2);
            $table->decimal("final_amount", 28, 8);
            $table->integer("delay_value");
            $table->decimal("fixed_charge", 28, 8);
            $table->decimal("percent_charge", 5, 2);
            $table->unsignedInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('extensions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("act", 40)->nullable();
            $table->string("name", 40)->nullable();
            $table->text("description")->nullable();
            $table->string("image", 255)->nullable();
            $table->text("script")->nullable();
            $table->text("shortcode")->nullable();
            $table->text("support")->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('fdrs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("fdr_number", 40)->nullable();
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("plan_id");
            $table->decimal("amount", 28, 8);
            $table->decimal("per_installment", 28, 8);
            $table->integer("installment_interval");
            $table->decimal("profit", 28, 8);
            $table->unsignedInteger("status");
            $table->string("next_installment_date")->nullable();
            $table->string("locked_date")->nullable();
            $table->timestamp("closed_at")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('fdr_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40);
            $table->decimal("minimum_amount", 28, 8);
            $table->decimal("maximum_amount", 28, 8);
            $table->integer("installment_interval");
            $table->decimal("interest_rate", 5, 2);
            $table->integer("locked_days");
            $table->unsignedInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('forms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("act", 40)->nullable();
            $table->text("form_data")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('frontends', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("tempname", 40)->nullable();
            $table->string("slug", 255)->nullable();
            $table->string("data_keys", 40);
            $table->longText("data_values")->nullable();
            $table->longText("seo_content")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('gateways', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("form_id");
            $table->integer("code")->nullable();
            $table->string("name", 40)->nullable();
            $table->string("alias", 40);
            $table->string("image", 255)->nullable();
            $table->boolean("status");
            $table->text("gateway_parameters")->nullable();
            $table->text("supported_currencies")->nullable();
            $table->boolean("crypto");
            $table->text("extra")->nullable();
            $table->text("description")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('gateway_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->string("currency", 40)->nullable();
            $table->string("symbol", 40)->nullable();
            $table->integer("method_code")->nullable();
            $table->string("gateway_alias", 40)->nullable();
            $table->decimal("min_amount", 28, 8);
            $table->decimal("max_amount", 28, 8);
            $table->decimal("percent_charge", 5, 2);
            $table->decimal("fixed_charge", 28, 8);
            $table->decimal("rate", 28, 8);
            $table->text("gateway_parameter")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('general_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("site_name", 40)->nullable();
            $table->string("cur_text", 40)->nullable();
            $table->string("cur_sym", 40)->nullable();
            $table->string("email_from", 40)->nullable();
            $table->string("email_from_name", 255)->nullable();
            $table->text("email_template")->nullable();
            $table->string("sms_template", 255)->nullable();
            $table->string("sms_from", 255)->nullable();
            $table->string("push_title", 255)->nullable();
            $table->text("push_template")->nullable();
            $table->string("base_color", 40)->nullable();
            $table->string("secondary_color", 40)->nullable();
            $table->text("mail_config")->nullable();
            $table->text("sms_config")->nullable();
            $table->text("firebase_config")->nullable();
            $table->text("global_shortcodes")->nullable();
            $table->boolean("kv");
            $table->boolean("ev");
            $table->boolean("en");
            $table->boolean("sv");
            $table->boolean("sn");
            $table->boolean("pn");
            $table->boolean("force_ssl");
            $table->boolean("in_app_payment");
            $table->boolean("maintenance_mode");
            $table->boolean("secure_password");
            $table->boolean("agree");
            $table->boolean("multi_language");
            $table->boolean("registration");
            $table->string("active_template", 40)->nullable();
            $table->text("socialite_credentials")->nullable();
            $table->string("available_version", 40)->nullable();
            $table->dateTime("last_cron")->nullable();
            $table->unsignedInteger("detect_activity")->nullable();
            $table->boolean("system_customized");
            $table->unsignedInteger("paginate_number");
            $table->boolean("currency_format");
            $table->text("config_progress")->nullable();
            $table->text("modules")->nullable();
            $table->integer("account_no_length")->nullable();
            $table->string("account_no_prefix", 40)->nullable();
            $table->integer("otp_time");
            $table->decimal("daily_transfer_limit", 28, 8);
            $table->decimal("monthly_transfer_limit", 28, 8);
            $table->decimal("minimum_transfer_limit", 28, 8);
            $table->decimal("fixed_transfer_charge", 28, 8);
            $table->decimal("percent_transfer_charge", 5, 2);
            $table->unsignedInteger("referral_commission_count");
            $table->decimal("statement_fee", 5, 2);
            $table->integer("idle_time_threshold")->nullable();
            $table->string("stripe_secret_key", 255)->nullable();
            $table->string("stripe_publishable_key", 255)->nullable();
            $table->decimal("card_issue_fee", 28, 8)->nullable();
            $table->decimal("card_issue_percent_fee", 5, 2);
            $table->decimal("spending_limit", 28, 8);
            $table->boolean("auto_active_card");
            $table->string("webhook_endpoint_secret", 255)->nullable();
            $table->text("branding_config")->nullable();
            $table->decimal("yearly_card_charge", 28, 8)->nullable();
            $table->string("currency_api_key", 255)->nullable();
            $table->tinyInteger("automatic_currency_rate_update")->nullable();
            $table->decimal("currency_exchange_rate", 28, 8);
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
            $table->unique('tenant_id');
        });

        Schema::create('installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("installmentable_type", 40);
            $table->unsignedInteger("installmentable_id");
            $table->decimal("delay_charge", 28, 8);
            $table->string("installment_date")->nullable();
            $table->timestamp("given_at")->nullable();
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->string("code", 40)->nullable();
            $table->boolean("is_default");
            $table->string("image", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("loan_number", 40)->nullable();
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("plan_id");
            $table->decimal("amount", 28, 8);
            $table->decimal("per_installment", 28, 8);
            $table->integer("installment_interval");
            $table->integer("delay_value");
            $table->decimal("charge_per_installment", 28, 8);
            $table->decimal("delay_charge", 28, 8);
            $table->integer("given_installment");
            $table->integer("total_installment");
            $table->text("application_form")->nullable();
            $table->text("admin_feedback")->nullable();
            $table->unsignedInteger("status");
            $table->timestamp("due_notification_sent")->nullable();
            $table->timestamp("approved_at")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('loan_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("form_id");
            $table->string("name", 40);
            $table->decimal("minimum_amount", 28, 8);
            $table->decimal("maximum_amount", 28, 8);
            $table->decimal("per_installment", 5, 2);
            $table->integer("installment_interval");
            $table->integer("total_installment");
            $table->text("instruction")->nullable();
            $table->unsignedInteger("delay_value");
            $table->decimal("fixed_charge", 28, 8);
            $table->decimal("percent_charge", 28, 8);
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->string("sender", 40)->nullable();
            $table->string("sent_from", 40)->nullable();
            $table->string("sent_to", 40)->nullable();
            $table->string("subject", 255)->nullable();
            $table->text("message")->nullable();
            $table->string("notification_type", 40)->nullable();
            $table->string("image", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("act", 40)->nullable();
            $table->string("name", 255)->nullable();
            $table->string("subject", 255)->nullable();
            $table->string("push_title", 255)->nullable();
            $table->text("email_body")->nullable();
            $table->text("sms_body")->nullable();
            $table->text("push_body")->nullable();
            $table->text("shortcodes")->nullable();
            $table->boolean("email_status");
            $table->boolean("sms_status");
            $table->string("email_sent_from_name", 40)->nullable();
            $table->string("email_sent_from_address", 40)->nullable();
            $table->string("sms_sent_from", 40)->nullable();
            $table->boolean("push_status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('operators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("country_id");
            $table->unsignedInteger("unique_id");
            $table->unsignedInteger("operator_group_id");
            $table->string("name", 255)->nullable();
            $table->boolean("bundle");
            $table->boolean("data");
            $table->boolean("pin");
            $table->string("denomination_type", 40)->nullable();
            $table->string("destination_currency_code", 40)->nullable();
            $table->string("destination_currency_symbol", 40)->nullable();
            $table->decimal("most_popular_amount", 28, 8)->nullable();
            $table->string("min_amount", 40)->nullable();
            $table->string("max_amount", 40)->nullable();
            $table->text("logo_urls")->nullable();
            $table->text("fixed_amounts")->nullable();
            $table->mediumText("fixed_amounts_descriptions")->nullable();
            $table->mediumText("local_fixed_amounts")->nullable();
            $table->mediumText("local_fixed_amounts_descriptions")->nullable();
            $table->mediumText("suggested_amounts")->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('other_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->decimal("minimum_limit", 28, 8);
            $table->decimal("maximum_limit", 28, 8);
            $table->decimal("daily_maximum_limit", 28, 8);
            $table->decimal("monthly_maximum_limit", 28, 8);
            $table->integer("daily_total_transaction");
            $table->unsignedInteger("monthly_total_transaction");
            $table->decimal("fixed_charge", 28, 8);
            $table->decimal("percent_charge", 5, 2);
            $table->string("processing_time", 255);
            $table->text("instruction")->nullable();
            $table->text("supported_currency")->nullable();
            $table->boolean("status");
            $table->integer("form_id");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('otp_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("wallet_id")->nullable();
            $table->string("verifiable_type", 255);
            $table->unsignedInteger("verifiable_id");
            $table->unsignedInteger("user_id");
            $table->string("otp", 40)->nullable();
            $table->string("send_via", 255)->nullable();
            $table->string("notify_template", 40)->nullable();
            $table->text("additional_data")->nullable();
            $table->dateTime("send_at")->nullable();
            $table->dateTime("expired_at")->nullable();
            $table->dateTime("used_at")->nullable();
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->string("slug", 40)->nullable();
            $table->string("tempname", 40)->nullable();
            $table->text("secs")->nullable();
            $table->text("seo_content")->nullable();
            $table->boolean("is_default");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('password_resets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("email", 40)->nullable();
            $table->string("token", 40)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 255)->nullable();
            $table->string("group", 40)->nullable();
            $table->string("code", 255)->nullable();
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedBigInteger("permission_id");
            $table->unsignedBigInteger("role_id");
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("tokenable_type", 255);
            $table->unsignedBigInteger("tokenable_id");
            $table->string("name", 255);
            $table->string("token", 255);
            $table->text("abilities")->nullable();
            $table->timestamp("last_used_at")->nullable();
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('referral_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("level");
            $table->decimal("percent", 5, 2);
            $table->timestamp("created_at")->nullable();
            $table->timestamp("updated_at")->nullable();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('reward_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->bigInteger("user_id")->nullable();
            $table->bigInteger("reward_point_earning_id")->nullable();
            $table->decimal("reward_point", 28, 8)->nullable();
            $table->string("details", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('reward_point_earnings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->bigInteger("account_level_id")->nullable();
            $table->string("name", 255)->nullable();
            $table->decimal("transaction_amount", 28, 8)->nullable();
            $table->decimal("reward_point", 28, 8)->nullable();
            $table->integer("max_use")->nullable();
            $table->integer("total_used")->nullable();
            $table->integer("per_user_limit")->nullable();
            $table->string("started_at")->nullable();
            $table->string("expired_at")->nullable();
            $table->text("reward_type")->nullable();
            $table->unsignedInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('reward_point_redeems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 255)->nullable();
            $table->bigInteger("account_level_id")->nullable();
            $table->decimal("redeem_point", 28, 8)->nullable();
            $table->decimal("redeem_amount", 28, 8)->nullable();
            $table->integer("total_used")->nullable();
            $table->unsignedInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('reward_redeems', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->bigInteger("user_id")->nullable();
            $table->bigInteger("reward_point_redeem_id")->nullable();
            $table->decimal("redeem_point", 28, 8)->nullable();
            $table->decimal("redeem_amount", 28, 8)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 40)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('subscribers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("email", 40)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('support_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("support_message_id");
            $table->string("attachment", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('support_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("support_ticket_id");
            $table->unsignedInteger("admin_id");
            $table->longText("message")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("user_id")->nullable();
            $table->string("name", 40)->nullable();
            $table->string("email", 40)->nullable();
            $table->string("ticket", 40)->nullable();
            $table->string("subject", 255)->nullable();
            $table->boolean("status");
            $table->boolean("priority");
            $table->dateTime("last_reply")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('table_configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("admin_id");
            $table->string("table_name", 40);
            $table->longText("visible_columns")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("user_id");
            $table->integer("deposit_id");
            $table->integer("virtual_card_id");
            $table->decimal("amount", 28, 8);
            $table->tinyInteger("status");
            $table->string("trx", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->integer("wallet_id")->nullable();
            $table->decimal("wallet_amount", 28, 8)->nullable();
            $table->integer("virtual_card_id");
            $table->boolean("stripe_transaction");
            $table->unsignedInteger("branch_id");
            $table->unsignedInteger("branch_staff_id");
            $table->decimal("amount", 28, 8);
            $table->decimal("charge", 28, 8);
            $table->decimal("post_balance", 28, 8);
            $table->string("trx_type", 40)->nullable();
            $table->string("trx", 40)->nullable();
            $table->string("details", 255)->nullable();
            $table->string("remark", 40)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('update_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("version", 40)->nullable();
            $table->text("update_log")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("account_level_id")->nullable();
            $table->integer("branch_id");
            $table->integer("branch_staff_id");
            $table->string("account_number", 140);
            $table->string("firstname", 40)->nullable();
            $table->string("lastname", 40)->nullable();
            $table->string("username", 40);
            $table->string("email", 40);
            $table->string("dial_code", 40)->nullable();
            $table->string("country_code", 40)->nullable();
            $table->string("city", 255)->nullable();
            $table->string("state", 255)->nullable();
            $table->string("zip", 255)->nullable();
            $table->string("mobile", 40)->nullable();
            $table->unsignedInteger("ref_by");
            $table->unsignedInteger("referral_commission_count");
            $table->decimal("balance", 28, 8);
            $table->decimal("reward_point", 28, 8)->nullable();
            $table->string("password", 255);
            $table->string("image", 255)->nullable();
            $table->string("country_name", 255)->nullable();
            $table->text("address")->nullable();
            $table->boolean("status");
            $table->boolean("ev");
            $table->boolean("sv");
            $table->string("ver_code", 40)->nullable();
            $table->dateTime("ver_code_send_at")->nullable();
            $table->boolean("ts");
            $table->boolean("tv");
            $table->string("tsc", 255)->nullable();
            $table->boolean("kv");
            $table->text("kyc_data")->nullable();
            $table->string("kyc_rejection_reason", 255)->nullable();
            $table->boolean("profile_complete");
            $table->string("ban_reason", 255)->nullable();
            $table->string("remember_token", 255)->nullable();
            $table->string("provider", 255)->nullable();
            $table->string("provider_id", 255)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
            $table->unique(['tenant_id', 'username']);
            $table->unique(['tenant_id', 'email']);
            $table->unique(['tenant_id', 'account_number']);
        });

        Schema::create('user_logins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("user_id");
            $table->string("user_ip", 40)->nullable();
            $table->string("city", 40)->nullable();
            $table->string("country", 40)->nullable();
            $table->string("country_code", 40)->nullable();
            $table->string("longitude", 40)->nullable();
            $table->string("latitude", 40)->nullable();
            $table->string("browser", 40)->nullable();
            $table->string("os", 40)->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('virtual_cards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->integer("user_id");
            $table->string("email", 255)->nullable();
            $table->string("phone_number", 255)->nullable();
            $table->string("name", 255)->nullable();
            $table->string("label", 255)->nullable();
            $table->string("last4", 255)->nullable();
            $table->string("exp_month", 255)->nullable();
            $table->string("exp_year", 255)->nullable();
            $table->string("currency", 255)->nullable();
            $table->string("brand", 255)->nullable();
            $table->decimal("balance", 28, 8);
            $table->decimal("spending_limit", 28, 8);
            $table->decimal("current_spend", 28, 8);
            $table->string("status", 255)->nullable();
            $table->string("address", 255)->nullable();
            $table->string("cardholder_id", 255)->nullable();
            $table->boolean("payment_status");
            $table->string("card_id", 255)->nullable();
            $table->dateTime("charged_at");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("name", 255)->nullable();
            $table->unsignedInteger("user_id");
            $table->decimal("balance", 28, 8)->nullable();
            $table->integer("currency_id")->nullable();
            $table->unsignedInteger("status")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('wallet_currencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->string("currency", 40)->nullable();
            $table->string("symbol", 40)->nullable();
            $table->decimal("currency_rate", 28, 8)->nullable();
            $table->unsignedInteger("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('wire_transfer_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->decimal("minimum_limit", 28, 8);
            $table->decimal("maximum_limit", 28, 8);
            $table->decimal("daily_maximum_limit", 28, 8);
            $table->decimal("monthly_maximum_limit", 28, 8);
            $table->unsignedInteger("daily_total_transaction");
            $table->unsignedInteger("monthly_total_transaction");
            $table->decimal("fixed_charge", 28, 8);
            $table->decimal("percent_charge", 5, 2);
            $table->text("instruction")->nullable();
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("method_id");
            $table->unsignedInteger("user_id");
            $table->decimal("amount", 28, 8);
            $table->string("currency", 40)->nullable();
            $table->decimal("rate", 28, 8);
            $table->decimal("charge", 28, 8);
            $table->string("trx", 40)->nullable();
            $table->decimal("final_amount", 28, 8);
            $table->decimal("after_charge", 28, 8);
            $table->text("withdraw_information")->nullable();
            $table->boolean("status");
            $table->text("admin_feedback")->nullable();
            $table->integer("branch_id");
            $table->integer("branch_staff_id");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

        Schema::create('withdraw_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->cascadeOnDelete();
            $table->unsignedInteger("form_id");
            $table->string("name", 40)->nullable();
            $table->string("image", 255)->nullable();
            $table->decimal("min_limit", 28, 8)->nullable();
            $table->decimal("max_limit", 28, 8);
            $table->decimal("fixed_charge", 28, 8)->nullable();
            $table->decimal("rate", 28, 8)->nullable();
            $table->decimal("percent_charge", 5, 2)->nullable();
            $table->string("currency", 40)->nullable();
            $table->text("description")->nullable();
            $table->boolean("status");
            $table->timestamps();
            $table->index(['tenant_id', 'id']);
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('account_levels');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('admin_password_resets');
        Schema::dropIfExists('api_configurations');
        Schema::dropIfExists('assign_branch_staff');
        Schema::dropIfExists('authorizations');
        Schema::dropIfExists('balance_transfers');
        Schema::dropIfExists('beneficiaries');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('branch_staff');
        Schema::dropIfExists('branch_staff_password_resets');
        Schema::dropIfExists('cron_jobs');
        Schema::dropIfExists('cron_job_logs');
        Schema::dropIfExists('cron_schedules');
        Schema::dropIfExists('deposits');
        Schema::dropIfExists('device_tokens');
        Schema::dropIfExists('dps');
        Schema::dropIfExists('dps_plans');
        Schema::dropIfExists('extensions');
        Schema::dropIfExists('fdrs');
        Schema::dropIfExists('fdr_plans');
        Schema::dropIfExists('forms');
        Schema::dropIfExists('frontends');
        Schema::dropIfExists('gateways');
        Schema::dropIfExists('gateway_currencies');
        Schema::dropIfExists('general_settings');
        Schema::dropIfExists('installments');
        Schema::dropIfExists('languages');
        Schema::dropIfExists('loans');
        Schema::dropIfExists('loan_plans');
        Schema::dropIfExists('notification_logs');
        Schema::dropIfExists('notification_templates');
        Schema::dropIfExists('operators');
        Schema::dropIfExists('other_banks');
        Schema::dropIfExists('otp_verifications');
        Schema::dropIfExists('pages');
        Schema::dropIfExists('password_resets');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('permission_role');
        Schema::dropIfExists('personal_access_tokens');
        Schema::dropIfExists('referral_settings');
        Schema::dropIfExists('reward_points');
        Schema::dropIfExists('reward_point_earnings');
        Schema::dropIfExists('reward_point_redeems');
        Schema::dropIfExists('reward_redeems');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('subscribers');
        Schema::dropIfExists('support_attachments');
        Schema::dropIfExists('support_messages');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('table_configurations');
        Schema::dropIfExists('topups');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('update_logs');
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_logins');
        Schema::dropIfExists('virtual_cards');
        Schema::dropIfExists('wallets');
        Schema::dropIfExists('wallet_currencies');
        Schema::dropIfExists('wire_transfer_settings');
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('withdraw_methods');
    }
};
