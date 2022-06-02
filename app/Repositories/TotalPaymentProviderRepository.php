<?php


namespace App\Repositories;


use App\Total_payment_provider;
use Illuminate\Support\Facades\DB;

class TotalPaymentProviderRepository
{

    public function getRest($id)
    {
        return Total_payment_provider::query()->where('provider_id', '=', $id)->value('rest');
    }

    public function deletePaymentById($id, $amount, $provider_id): void
    {

        DB::table('total_payment_providers')->where('provider_id', $provider_id)->update([
            'rest' => DB::raw('rest + ' . (float)$amount . '')
        ]);

        DB::table('provider_payments')->delete($id);
    }

    public function modifyPaymentById($id, float $amount, $provider_id): void
    {
        $amountBeforeSubmit = DB::table('provider_payments')
            ->where('id', $id)->first();

        DB::table('total_payment_providers')->where('provider_id', $provider_id)->update([
            'rest' => DB::raw('rest + ' . $amountBeforeSubmit->amount . '')
        ]);

        DB::table('total_payment_providers')->where('provider_id', $provider_id)->update([
            'rest' => DB::raw('rest - ' . $amount . '')
        ]);

        DB::table('provider_payments')->where('id', $id)->update([
            'amount' => DB::raw($amount)
        ]);
    }

}