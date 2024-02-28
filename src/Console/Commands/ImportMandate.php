<?php

namespace GoCardlessPayment\Console\Commands;

use GoCardlessPayment\Actions\Imports\ImportMandateAction;
use Illuminate\Console\Command;

class ImportMandate extends Command
{
    protected $signature = 'gocardless-payment:import:mandate
    {id : Mandate id}
    ';

    protected $description = 'Import mandate to local storage by id';

    public function handle()
    {
        $mandateId = $this->argument('id');

        try {
            $model = ImportMandateAction::make($mandateId)->execute();
        } catch (\Exception $e) {
            $this->error("Mandate not imported. [{$e->getMessage()}]");

            return static::FAILURE;
        }

        $this->info("Imported mandate [{$model->getKey()}] for customer [{$model->customer_id}].");

        return static::SUCCESS;
    }
}
