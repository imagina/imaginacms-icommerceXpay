<?php

namespace Modules\Icommercexpay\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Modules\Icommerce\Entities\PaymentMethod;
use Modules\Isite\Jobs\ProcessSeeds;

class IcommercexpayDatabaseSeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run()
  {

    ProcessSeeds::dispatch([
      "baseClass" => "\Modules\Icommercexpay\Database\Seeders",
      "seeds" => ["IcommercexpayModuleTableSeeder", "IcommercexpaySeeder"]
    ]);

  }
}
