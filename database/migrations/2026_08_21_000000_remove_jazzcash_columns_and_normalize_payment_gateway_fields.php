<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->dropJazzCashColumnsFromUsers();
        $this->normalizePaymentGatewayColumns();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $this->restoreJazzCashColumnsToUsers();
        $this->restoreLegacyPaymentColumns();
    }

    private function dropJazzCashColumnsFromUsers(): void
    {
        $columns = [
            'jazzcash_mobile',
            'jazzcash_account_title',
            'jazzcash_verified',
            'jazzcash_merchant_id',
            'jazzcash_password',
            'jazzcash_integrity_salt',
        ];

        $existing = array_values(array_filter($columns, fn (string $column) => Schema::hasColumn('users', $column)));

        if (!empty($existing)) {
            Schema::table('users', function (Blueprint $table) use ($existing) {
                $table->dropColumn($existing);
            });
        }
    }

    private function normalizePaymentGatewayColumns(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'gateway_txn_id')) {
                $table->string('gateway_txn_id')->nullable()->after('safepay_tracker_id');
            }

            if (!Schema::hasColumn('payments', 'gateway_response_code')) {
                $table->string('gateway_response_code')->nullable()->after('gateway_txn_id');
            }

            if (!Schema::hasColumn('payments', 'gateway_response_message')) {
                $table->string('gateway_response_message')->nullable()->after('gateway_response_code');
            }

            if (!Schema::hasColumn('payments', 'gateway_retrieval_ref_no')) {
                $table->string('gateway_retrieval_ref_no')->nullable()->after('gateway_response_message');
            }
        });

        if (Schema::hasColumn('payments', 'pp_txn_id')) {
            DB::table('payments')->whereNull('gateway_txn_id')->update([
                'gateway_txn_id' => DB::raw('pp_txn_id'),
            ]);
        }

        if (Schema::hasColumn('payments', 'pp_response_code')) {
            DB::table('payments')->whereNull('gateway_response_code')->update([
                'gateway_response_code' => DB::raw('pp_response_code'),
            ]);
        }

        if (Schema::hasColumn('payments', 'pp_response_message')) {
            DB::table('payments')->whereNull('gateway_response_message')->update([
                'gateway_response_message' => DB::raw('pp_response_message'),
            ]);
        }

        if (Schema::hasColumn('payments', 'pp_retrieval_ref_no')) {
            DB::table('payments')->whereNull('gateway_retrieval_ref_no')->update([
                'gateway_retrieval_ref_no' => DB::raw('pp_retrieval_ref_no'),
            ]);
        }

        $legacyColumns = [
            'pp_txn_id',
            'pp_response_code',
            'pp_response_message',
            'pp_retrieval_ref_no',
        ];

        $existingLegacy = array_values(array_filter($legacyColumns, fn (string $column) => Schema::hasColumn('payments', $column)));

        if (!empty($existingLegacy)) {
            Schema::table('payments', function (Blueprint $table) use ($existingLegacy) {
                $table->dropColumn($existingLegacy);
            });
        }
    }

    private function restoreJazzCashColumnsToUsers(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'jazzcash_mobile')) {
                $table->string('jazzcash_mobile', 15)->nullable();
            }

            if (!Schema::hasColumn('users', 'jazzcash_account_title')) {
                $table->string('jazzcash_account_title', 100)->nullable();
            }

            if (!Schema::hasColumn('users', 'jazzcash_verified')) {
                $table->boolean('jazzcash_verified')->default(false);
            }

            if (!Schema::hasColumn('users', 'jazzcash_merchant_id')) {
                $table->string('jazzcash_merchant_id')->nullable();
            }

            if (!Schema::hasColumn('users', 'jazzcash_password')) {
                $table->text('jazzcash_password')->nullable();
            }

            if (!Schema::hasColumn('users', 'jazzcash_integrity_salt')) {
                $table->text('jazzcash_integrity_salt')->nullable();
            }
        });
    }

    private function restoreLegacyPaymentColumns(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            if (!Schema::hasColumn('payments', 'pp_txn_id')) {
                $table->string('pp_txn_id')->nullable();
            }

            if (!Schema::hasColumn('payments', 'pp_response_code')) {
                $table->string('pp_response_code')->nullable();
            }

            if (!Schema::hasColumn('payments', 'pp_response_message')) {
                $table->string('pp_response_message')->nullable();
            }

            if (!Schema::hasColumn('payments', 'pp_retrieval_ref_no')) {
                $table->string('pp_retrieval_ref_no')->nullable();
            }
        });

        if (Schema::hasColumn('payments', 'gateway_txn_id')) {
            DB::table('payments')->whereNull('pp_txn_id')->update([
                'pp_txn_id' => DB::raw('gateway_txn_id'),
            ]);
        }

        if (Schema::hasColumn('payments', 'gateway_response_code')) {
            DB::table('payments')->whereNull('pp_response_code')->update([
                'pp_response_code' => DB::raw('gateway_response_code'),
            ]);
        }

        if (Schema::hasColumn('payments', 'gateway_response_message')) {
            DB::table('payments')->whereNull('pp_response_message')->update([
                'pp_response_message' => DB::raw('gateway_response_message'),
            ]);
        }

        if (Schema::hasColumn('payments', 'gateway_retrieval_ref_no')) {
            DB::table('payments')->whereNull('pp_retrieval_ref_no')->update([
                'pp_retrieval_ref_no' => DB::raw('gateway_retrieval_ref_no'),
            ]);
        }

        $genericColumns = [
            'gateway_txn_id',
            'gateway_response_code',
            'gateway_response_message',
            'gateway_retrieval_ref_no',
        ];

        $existingGeneric = array_values(array_filter($genericColumns, fn (string $column) => Schema::hasColumn('payments', $column)));

        if (!empty($existingGeneric)) {
            Schema::table('payments', function (Blueprint $table) use ($existingGeneric) {
                $table->dropColumn($existingGeneric);
            });
        }
    }
};
