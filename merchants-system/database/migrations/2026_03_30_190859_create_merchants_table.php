<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
            $table->id();

            $table->string('membership_no')->nullable()->index();
            $table->string('organization_name')->nullable()->index();

            $table->date('registered_date')->nullable();
            $table->date('sub_date')->nullable();

            $table->text('description')->nullable();

            $table->string('org_national_no')->nullable()->index();
            $table->string('commercial_reg_no')->nullable()->index();
            $table->date('commercial_reg_date')->nullable();

            $table->string('commercial_name')->nullable()->index();

            $table->string('ccate_id')->nullable()->index();
            $table->string('delegate_to_sign_on_management')->nullable();

            $table->text('members')->nullable();

            $table->string('street')->nullable();
            $table->string('po_box')->nullable();
            $table->string('zipcode_desc')->nullable();
            $table->string('zipcode')->nullable();

            $table->boolean('contacted')->default(false)->index();
            $table->boolean('invited')->default(false)->index();

            $table->text('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
