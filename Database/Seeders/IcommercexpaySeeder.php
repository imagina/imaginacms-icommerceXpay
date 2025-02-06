<?php

namespace Modules\Icommercexpay\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Modules\Icommerce\Entities\PaymentMethod;

class IcommercexpaySeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
//    Model::unguard();

    if (!is_module_enabled('Icommercexpay')) {
      $this->command->alert("This module: Icommercexpay is DISABLED!! , please enable the module and then run the seed");
      exit();
    }

    //Validation if the module has been installed before
    $name = config('asgard.icommercexpay.config.paymentName');
    $paymentMethod = PaymentMethod::where('name', $name)->first();
    $PaymentMethodRepository = app('Modules\Icommerce\Repositories\PaymentMethodRepository');

    if (!$paymentMethod) {
      $options['init'] = "Modules\Icommercexpay\Http\Controllers\Api\IcommerceXpayApiController";

      $options['mainimage'] = null;
      $options['user'] = null;
      $options['pass'] = null;
      $options['mode'] = 'sandbox';
      $options['token'] = null;
      $options['minimunAmount'] = 0;
      $options['showInCurrencies'] = ['COP'];

      $titleTrans = 'icommercexpay::icommercexpays.single';
      $descriptionTrans = 'icommercexpay::icommercexpays.description';

      foreach (['en', 'es'] as $locale) {
        if ($locale == 'en') {
          $params = [
            'title' => trans($titleTrans),
            'description' => trans($descriptionTrans),
            'name' => $name,
            'status' => 1,
            'options' => $options,
          ];

          $paymentMethod = PaymentMethod::create($params);
        } else {
          $title = trans($titleTrans, [], $locale);
          $description = trans($descriptionTrans, [], $locale);

          $paymentMethod->translateOrNew($locale)->title = $title;
          $paymentMethod->translateOrNew($locale)->description = $description;

          $paymentMethod->save();
        }
      }// Foreach
    } else {
      if ($paymentMethod->description != trans('icommercexpay::icommercexpays.iaDescription', [], locale())) {
        $data = array(
          'es' => ['description' => trans('icommercexpay::icommercexpays.iaDescription', [], 'es')],
          'en' => ['description' => trans('icommercexpay::icommercexpays.iaDescription', [], 'en')]
        );
        $paymentMethod = $PaymentMethodRepository->update($paymentMethod, $data);
        //Instance file service
        $fileService = app("Modules\Media\Services\FileService");
        //Instance the file path
        $filePath = 'Modules/Icommercexpay/Resources/img/xpay_default.png';
        if (Storage::disk('local')->exists($filePath)) {
          // Obtener el contenido del archivo
          $fileContents = Storage::disk('local')->get($filePath);
          // Convertir el archivo a base64
          $base64File = base64_encode($fileContents);
          //Get base64 file
          $uploadedFile = getUploadedFileFromBase64($base64File);
          //Create file
          $file = $fileService->store($uploadedFile, 0, 'publicmedia');
          //set file if
          $fileId = $file->id;
          //Sync file id
          $paymentMethod->files()->attach($fileId, ['zone' => 'mainimage']);
        }
      }
//      $this->command->alert("This method has already been installed !!");
    }
  }
}